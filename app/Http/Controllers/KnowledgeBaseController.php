<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Setting;
use App\Models\ChatLog;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class KnowledgeBaseController extends Controller
{
    private const NORMALIZE_WHITESPACE_PATTERN = '/\s+/';
    private const SHORT_DOMAIN_TOKENS = ['kp', 'sks'];
    private const DOC_FALLBACK_PREFIX = 'Berdasarkan dokumen **';

    /**
     * Tampilkan Dashboard Knowledge Base.
     */
    public function index()
    {
        // Ambil dokumen aktif
        $documents = Document::active()->get();

        // Hitung statistik RAG
        $activeDocsCount = $documents->count();
        $totalChunks = $documents->sum('chunk_count');

        // Waktu sinkronisasi terakhir (berdasarkan update indexed_at terbaru)
        $latestIndexedDoc = Document::active()->whereNotNull('indexed_at')->latest('indexed_at')->first();
        $lastSyncTime = $latestIndexedDoc ? $latestIndexedDoc->indexed_at->format('d M Y, H:i') : 'Belum disinkronkan';

        // Timestamp pembaruan knowledge base (upload/hapus/restore/sync)
        $kbLastUpdated = Setting::getVal('kb_last_updated_at', null);

        // Ambil konfigurasi dari tabel settings (atau default jika kosong)
        $systemPrompt = Setting::getVal('rag_system_prompt', 'Anda adalah SIMAK, Asisten Virtual Student Service Center (SSC) Telkom University Surabaya yang bertugas membantu mahasiswa mendapatkan informasi seputar Magang dan Kerja Praktik. Berikan jawaban yang ramah, profesional, dan mudah dipahami hanya berdasarkan dokumen referensi yang tersedia. Akhiri setiap jawaban dengan kalimat penutup yang natural dan ramah, seperti "Semoga informasi ini membantu. Jika ada pertanyaan lain seputar Magang atau Kerja Praktik, silakan tanyakan kembali." Jika informasi tidak tersedia dalam dokumen, sampaikan dengan sopan bahwa informasi tersebut tidak ditemukan dan sarankan mahasiswa untuk menghubungi SSC Telkom University Surabaya secara langsung.');
        $knowledgeBasePrompt = Setting::getVal('rag_knowledge_base_prompt', 'Knowledge base SIMAK: Fokus pada informasi Magang dan Kerja Praktik di Telkom University Surabaya. Jika dokumen referensi tidak terbaca sepenuhnya atau tidak memuat detail yang dicari, gunakan pengetahuan domain ini untuk memberi jawaban yang paling dekat dengan konteks SIMAK, lalu sebutkan bahwa jawaban tersebut dibantu oleh pedoman knowledge base.');
        $model = Setting::getVal('rag_model', 'groq-llama3-8b');
        $temperature = (float) Setting::getVal('rag_temperature', 0.5);
        $chunkSize = (int) Setting::getVal('rag_chunk_size', 750);
        $chunkOverlap = (int) Setting::getVal('rag_chunk_overlap', 150);

        return view('dashboard.knowledge-base', compact(
            'documents',
            'activeDocsCount',
            'totalChunks',
            'lastSyncTime',
            'kbLastUpdated',
            'systemPrompt',
            'knowledgeBasePrompt',
            'model',
            'temperature',
            'chunkSize',
            'chunkOverlap'
        ));
    }

    /**
     * Simpan konfigurasi RAG Settings.
     */
    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'system_prompt' => ['required', 'string', 'max:5000'],
            'knowledge_base_prompt' => ['required', 'string', 'max:5000'],
            'model' => ['required', 'string', 'in:groq-llama3-8b,openai-gpt-4o-mini'],
            'temperature' => ['required', 'numeric', 'between:0,1'],
            'chunk_size' => ['required', 'integer', 'between:500,1000'],
            'chunk_overlap' => ['required', 'integer', 'min:0'],
        ]);

        Setting::setVal('rag_system_prompt', $validated['system_prompt']);
        Setting::setVal('rag_knowledge_base_prompt', $validated['knowledge_base_prompt']);
        Setting::setVal('rag_model', $validated['model']);
        Setting::setVal('rag_temperature', $validated['temperature']);
        Setting::setVal('rag_chunk_size', $validated['chunk_size']);
        Setting::setVal('rag_chunk_overlap', $validated['chunk_overlap']);

        \App\Models\ActivityLog::log("Staff " . (auth()->user()->name ?? 'Admin') . " memperbarui konfigurasi RAG", 'update');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi RAG berhasil disimpan.',
            ]);
        }

        return redirect()
            ->route('dashboard.knowledge-base')
            ->with('success', 'Konfigurasi RAG berhasil disimpan.');
    }

    /**
     * Memicu proses simulasi Sinkronisasi & Re-indexing RAG Dataset.
     */
    public function sync(Request $request)
    {
        $activeDocs = Document::active()->get();

        if ($activeDocs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada dokumen aktif untuk disinkronisasi.',
            ], 422);
        }

        $now = now();

        foreach ($activeDocs as $doc) {
            $extractedText = $doc->extracted_text ?? $this->extractDocumentText($doc);

            // Simpan teks asli ke kolom content
            $doc->update([
                'content'       => $extractedText ?: $this->buildFallbackContent($doc),
                'indexed_at'    => $now,
                'chunk_count'   => $this->estimateChunkCount($doc),
            ]);
        }

        // Simpan timestamp pembaruan knowledge base dalam format bahasa Indonesia
        $kbLastUpdated = $this->formatIndonesianDate($now);
        Setting::setVal('kb_last_updated_at', $kbLastUpdated);

        \App\Models\ActivityLog::log("Staff " . (auth()->user()->name ?? 'Admin') . " menyinkronkan basis pengetahuan", 'sync');

        return response()->json([
            'success'       => true,
            'message'       => 'Sinkronisasi berhasil! ' . $activeDocs->count() . ' dokumen diindeks ulang.',
            'last_sync'     => $now->format('d M Y, H:i'),
            'kb_last_updated' => $kbLastUpdated,
            'total_chunks'  => Document::active()->sum('chunk_count'),
        ]);
    }

    /**
     * Playground Chatbot RAG query testing.
     */
    public function query(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'max:500'],
            'history' => ['nullable', 'array', 'max:5'],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:1000'],
        ]);

        $query = $request->input('query');
        $history = $request->input('history', []);
        
        $model = Setting::getVal('rag_model', 'groq-llama3-8b');
        $temperature = (float) Setting::getVal('rag_temperature', 0.5);
        $chunkSize = (int) Setting::getVal('rag_chunk_size', 750);
        $chunkOverlap = (int) Setting::getVal('rag_chunk_overlap', 150);
        $systemPrompt = Setting::getVal(
            'rag_system_prompt',
            'Anda adalah SIMAK, Asisten Virtual Student Service Center (SSC) Telkom University Surabaya. Berikan jawaban yang ramah, profesional, dan mudah dipahami HANYA berdasarkan dokumen referensi yang tersedia. JANGAN PERNAH mengarang URL, link, atau tautan. Jika dokumen tidak memuat link/URL yang diminta, sampaikan dengan sopan bahwa tautan tidak tersedia di basis pengetahuan. JANGAN menebak. Jika konteks memuat URL, tampilkan URL tersebut utuh tanpa mengubahnya. Gunakan format Markdown (bullet list, numbering, bold) agar rapi. JANGAN pernah mencetak teks internal seperti "Pertanyaan pengguna:" atau "Jawaban:". Langsung berikan informasi.'
        );
        $knowledgeBasePrompt = Setting::getVal(
            'rag_knowledge_base_prompt',
            'Knowledge base SIMAK: Jawab HANYA dari dokumen ini. Dilarang keras mengarang informasi atau link di luar dokumen.'
        );

        if ($this->isGeneralConversation($query)) {
            return response()->json([
                'success' => true,
                'answer' => $this->buildGeneralConversationAnswer($query),
                'source' => null,
            ]);
        }

        // Ambil seluruh dokumen aktif untuk retrieval konteks RAG.
        $activeDocs = Document::active()->whereNotNull('content')->get();

        $retrievalQuery = $query;
        $isFollowUp = preg_match('/\b(link|tautan|itu|tadi|dong|nya|kalau|mana|bagi|kasih|minta)\b/i', $query) || str_word_count($query) <= 3;
        if ($isFollowUp && !empty($history)) {
            $lastUserMsg = collect($history)->where('role', 'user')->last();
            if ($lastUserMsg) {
                $retrievalQuery = $lastUserMsg['content'] . ' ' . $query;
            }
        }

        $retrievedDocs = $this->retrieveRelevantDocuments($retrievalQuery, $activeDocs, $chunkSize, $chunkOverlap, 3);
        $topDoc = $retrievedDocs[0]['doc'] ?? null;

        $answer = null;

        // Jalankan query ke API sesuai model yang dipilih.
        if (str_starts_with($model, 'groq-')) {
            $answer = $this->queryGroq($query, $history, $retrievedDocs, $systemPrompt, $knowledgeBasePrompt, $model, $temperature);
        } elseif (str_starts_with($model, 'openai-')) {
            $answer = $this->queryOpenAI($query, $history, $retrievedDocs, $systemPrompt, $knowledgeBasePrompt, $model, $temperature);
        }

        // Fallback lokal dipertahankan jika API gagal atau model tidak dikenal.
        if (!$answer) {
            $answer = $this->generateFallbackAnswer($query, $topDoc);
        }

        // Log the query to chat_logs
        $normalized = $this->normalizeQuestion($query);
        ChatLog::create([
            'user_id' => auth()->id(),
            'message' => $query,
            'response' => $answer ?? '',
            'normalized_message' => $normalized,
        ]);

        return response()->json([
            'success' => true,
            'answer' => $answer,
            'source' => $topDoc ? [
                'title' => $topDoc->title,
                'type' => $topDoc->type,
            ] : null,
        ]);
    }

    /**
     * Normalize query inputs to classify similar questions deterministically.
     */
    public function normalizeQuestion(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\w\s]/u', '', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = [
            'apa', 'apakah', 'saja', 'bagaimana', 'siapa', 'kapan', 'dimana', 'di', 'mana',
            'kah', 'ini', 'itu', 'adalah', 'yang', 'dan', 'ke', 'untuk', 'pada', 'tentang',
            'dengan', 'sih', 'dong', 'ya', 'kok', 'tah', 'ada'
        ];
        $filtered = array_filter($words, function ($word) use ($stopWords) {
            return !in_array($word, $stopWords);
        });
        sort($filtered);
        $result = implode(' ', $filtered);
        return empty($result) ? trim($text) : $result;
    }

    /**
     * Ambil dokumen paling relevan secara sederhana berbasis keyword score.
     */
    private function retrieveRelevantDocuments(string $query, Collection $activeDocs, int $chunkSize, int $chunkOverlap, int $limit = 3): array
    {
        $queryLower = strtolower($query);
        $queryLower = $this->normalizeQueryForRetrieval($queryLower);
        $queryLower = preg_replace(self::NORMALIZE_WHITESPACE_PATTERN, ' ', $queryLower);
        $queryLower = trim((string) $queryLower);
        $scored = [];

        /** @var Document $doc */
        foreach ($activeDocs as $doc) {
            $chunks = $this->splitContentIntoChunks((string) $doc->content, $chunkSize, $chunkOverlap);

            foreach ($chunks as $chunk) {
                $score = $this->calculateChunkRelevanceScore($queryLower, $doc, $chunk);
                if ($score <= 0) {
                    continue;
                }

                $scored[] = [
                    'doc' => $doc,
                    'score' => $score,
                    'snippet' => $this->extractBestSnippet($queryLower, $chunk),
                ];
            }
        }

        if (empty($scored)) {
            return [];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $limit);
    }

    private function splitContentIntoChunks(string $content, int $chunkSize, int $chunkOverlap): array
    {
        $content = trim(preg_replace(self::NORMALIZE_WHITESPACE_PATTERN, ' ', $content));
        if ($content === '') {
            return [];
        }

        $chunkSize = max(120, $chunkSize);
        $chunkOverlap = max(0, min($chunkOverlap, $chunkSize - 1));
        $step = max(1, $chunkSize - $chunkOverlap);

        $chunks = [];
        $length = strlen($content);

        for ($offset = 0; $offset < $length; $offset += $step) {
            $chunk = trim(substr($content, $offset, $chunkSize));
            if ($chunk !== '') {
                $chunks[] = $chunk;
            }

            if ($offset + $chunkSize >= $length) {
                break;
            }
        }

        return $chunks;
    }

    private function calculateChunkRelevanceScore(string $queryLower, Document $doc, string $chunk): int
    {
        $score = $this->calculateRelevanceScore($queryLower, $doc);
        $chunkLower = strtolower($chunk);
        $words = preg_split(self::NORMALIZE_WHITESPACE_PATTERN, preg_replace('/[^a-z0-9\s]/', ' ', $queryLower));

        if (str_contains($chunkLower, $queryLower)) {
            $score += 10;
        }

        foreach ($words as $word) {
            if (!$word || (strlen($word) < 3 && !in_array($word, self::SHORT_DOMAIN_TOKENS, true))) {
                continue;
            }

            if (str_contains($chunkLower, $word)) {
                $score += 3;
            }
        }

        $matchedTokens = $this->countMatchedTokens($words, $chunkLower, $chunkLower, $chunkLower);
        if ($matchedTokens < 1 && !str_contains($chunkLower, $queryLower)) {
            return 0;
        }

        return $score;
    }

    private function calculateRelevanceScore(string $queryLower, Document $doc): int
    {
        $score = 0;
        $title = strtolower((string) $doc->title);
        $description = strtolower((string) $doc->description);
        $content = strtolower((string) $doc->content);
        if (str_contains($title, $queryLower)) {
            $score += 6;
        }

        if (str_contains($description, $queryLower)) {
            $score += 4;
        }

        if (str_contains($content, $queryLower)) {
            $score += 8;
        }

        $words = preg_split(self::NORMALIZE_WHITESPACE_PATTERN, preg_replace('/[^a-z0-9\s]/', ' ', $queryLower));
        foreach ($words as $word) {
            if (!$word || (strlen($word) < 3 && !in_array($word, self::SHORT_DOMAIN_TOKENS, true))) {
                continue;
            }

            $score += $this->scoreTokenMatch($title, $description, $content, $word);
        }

        $matchedTokens = $this->countMatchedTokens($words, $title, $description, $content);

        if ($matchedTokens < 1 && !str_contains($content, $queryLower)) {
            return 0;
        }

        return $score;
    }

    private function scoreTokenMatch(string $title, string $description, string $content, string $word): int
    {
        $score = 0;

        if (str_contains($title, $word)) {
            $score += 3;
        }

        if (str_contains($description, $word)) {
            $score += 2;
        }

        if (str_contains($content, $word)) {
            $score += 2;
        }

        return $score;
    }

    private function countMatchedTokens(array $words, string $title, string $description, string $content): int
    {
        $matchedTokens = 0;

        $filteredWords = [];
        foreach ($words as $word) {
            if (!$word) {
                continue;
            }

            if (strlen($word) >= 3 || in_array($word, self::SHORT_DOMAIN_TOKENS, true)) {
                $filteredWords[] = $word;
            }
        }

        foreach (array_unique($filteredWords) as $word) {
            if (str_contains($title, $word) || str_contains($description, $word) || str_contains($content, $word)) {
                $matchedTokens++;
            }
        }

        return $matchedTokens;
    }

    private function extractBestSnippet(string $queryLower, string $content): string
    {
        if ($content === '') {
            return '';
        }

        $contentLower = strtolower($content);
        $position = strpos($contentLower, $queryLower);

        if ($position === false) {
            return Str::limit($content, 700);
        }

        $start = max(0, $position - 220);
        return trim(substr($content, $start, 900));
    }

    private function queryGroq(string $query, array $history, array $retrievedDocs, string $systemPrompt, string $knowledgeBasePrompt, string $selectedModel, float $temperature): ?string
    {
        $apiKey = (string) config('services.groq.api_key');
        if ($apiKey === '') {
            return null;
        }

        $context = $this->buildRagContext($retrievedDocs);
        $questionHints = $this->buildQuestionHints($query);

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt . "\n\n" . $knowledgeBasePrompt,
            ],
        ];

        foreach ($history as $msg) {
            if (isset($msg['role'], $msg['content']) && in_array($msg['role'], ['user', 'assistant'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content'],
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => "Konteks dokumen:\n{$context}\n\nPetunjuk interpretasi pertanyaan: {$questionHints}\n\nPertanyaan pengguna: {$query}\n\nInstruksi: Jawab pertanyaan pengguna HANYA berdasarkan konteks dokumen. Jika informasi tidak ada di konteks, katakan tidak ditemukan di basis pengetahuan.",
        ];

        $response = Http::withToken($apiKey)
            ->timeout((int) config('services.groq.timeout', 30))
            ->post(rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/') . '/chat/completions', [
                'model' => $this->mapGroqModel($selectedModel),
                'temperature' => max(0, min(1, $temperature)),
                'max_tokens' => 900,
                'messages' => $messages,
            ]);

        if (!$response->successful()) {
            \Log::error("Groq API error on model {$selectedModel}: " . $response->status() . " - " . $response->body());
            return null;
        }

        $answer = trim((string) data_get($response->json(), 'choices.0.message.content', ''));
        return $answer !== '' ? $answer : null;
    }

    private function mapGroqModel(string $selectedModel): string
    {
        return match ($selectedModel) {
            'groq-llama3-8b' => 'llama-3.1-8b-instant',
            'groq-llama3-70b' => 'llama-3.3-70b-versatile',
            default => 'llama-3.1-8b-instant',
        };
    }

    private function queryOpenAI(string $query, array $history, array $retrievedDocs, string $systemPrompt, string $knowledgeBasePrompt, string $selectedModel, float $temperature): ?string
    {
        $apiKey = (string) config('services.openai.api_key');
        if ($apiKey === '') {
            return null;
        }

        $context = $this->buildRagContext($retrievedDocs);
        $questionHints = $this->buildQuestionHints($query);

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt . "\n\n" . $knowledgeBasePrompt,
            ],
        ];

        foreach ($history as $msg) {
            if (isset($msg['role'], $msg['content']) && in_array($msg['role'], ['user', 'assistant'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content'],
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => "Konteks dokumen:\n{$context}\n\nPetunjuk interpretasi pertanyaan: {$questionHints}\n\nPertanyaan pengguna: {$query}\n\nInstruksi: Jawab pertanyaan pengguna HANYA berdasarkan konteks dokumen. Jika informasi tidak ada di konteks, katakan tidak ditemukan di basis pengetahuan.",
        ];

        $response = Http::withToken($apiKey)
            ->timeout((int) config('services.openai.timeout', 30))
            ->post(rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/') . '/chat/completions', [
                'model' => $this->mapOpenAIModel($selectedModel),
                'temperature' => max(0, min(1, $temperature)),
                'max_tokens' => 900,
                'messages' => $messages,
            ]);

        if (!$response->successful()) {
            \Log::error("OpenAI API error on model {$selectedModel}: " . $response->status() . " - " . $response->body());
            return null;
        }

        $answer = trim((string) data_get($response->json(), 'choices.0.message.content', ''));
        return $answer !== '' ? $answer : null;
    }

    private function mapOpenAIModel(string $selectedModel): string
    {
        return match ($selectedModel) {
            'openai-gpt-4o-mini' => 'gpt-4o-mini',
            default => 'gpt-4o-mini',
        };
    }

    /**
     * Format tanggal dalam bahasa Indonesia.
     */
    private function formatIndonesianDate(\Carbon\Carbon $date): string
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
    }

    private function buildRagContext(array $retrievedDocs): string
    {
        if (empty($retrievedDocs)) {
            return 'Tidak ada dokumen konteks yang ditemukan.';
        }

        return collect($retrievedDocs)
            ->map(function ($item, $index) {
                /** @var \App\Models\Document $doc */
                $doc = $item['doc'];
                $snippet = trim((string) ($item['snippet'] ?? ''));

                return sprintf(
                    "[%d] Judul: %s\nTipe: %s\nIsi: %s",
                    $index + 1,
                    $doc->title,
                    strtoupper((string) $doc->type),
                    $snippet !== '' ? $snippet : '-'
                );
            })
            ->implode("\n\n");
    }

    private function buildQuestionHints(string $query): string
    {
        $queryLower = strtolower($query);
        $hint = 'Gunakan konteks untuk menjawab sesuai maksud pertanyaan.';

        if (str_contains($queryLower, 'di mana') || str_contains($queryLower, 'dimana') || str_contains($queryLower, 'tempat') || str_contains($queryLower, 'lokasi')) {
            $hint = 'Pengguna menanyakan lokasi atau kanal pendaftaran.';
        } elseif (str_contains($queryLower, 'nama website') || str_contains($queryLower, 'website') || str_contains($queryLower, 'situs') || str_contains($queryLower, 'url')) {
            $hint = 'Pengguna menanyakan nama website atau domain pendaftaran.';
        } elseif (str_contains($queryLower, 'syarat') || str_contains($queryLower, 'persyaratan')) {
            $hint = 'Pengguna menanyakan syarat atau persyaratan.';
        } elseif (str_contains($queryLower, 'daftar') || str_contains($queryLower, 'pendaftaran') || str_contains($queryLower, 'registrasi')) {
            $hint = 'Pengguna menanyakan prosedur pendaftaran.';
        } elseif (str_contains($queryLower, 'link') || str_contains($queryLower, 'tautan')
               || str_contains($queryLower, 'unduh') || str_contains($queryLower, 'download')
               || str_contains($queryLower, 'berikan') || str_contains($queryLower, 'kasih')
               || str_contains($queryLower, 'minta') || str_contains($queryLower, 'cari')) {
            $hint = 'Pengguna meminta tautan atau link untuk mengunduh atau mengakses dokumen maupun sumber terkait. Berikan tautan/URL yang tersedia di konteks dokumen secara langsung.';
        }

        return $hint;
    }

    /**
     * Normalisasi query agar intent informal bisa dikenali saat pencarian dokumen.
     * Meng-strip kata perintah/aksi dan mengekspansi singkatan domain.
     */
    private function normalizeQueryForRetrieval(string $queryLower): string
    {
        // Ekspansi singkatan domain
        $queryLower = preg_replace('/\bk\.?p\.?\b/u', ' kerja praktik ', $queryLower);

        // Strip kata-kata aksi/perintah informal yang tidak ada di dokumen
        $actionWords = [
            'berikan', 'kasih', 'tolong', 'minta', 'carikan',
            'ingin', 'bisa', 'bantu', 'bantuin',
            'tampilkan', 'tunjukkan', 'lihatkan', 'buatkan',
        ];
        foreach ($actionWords as $word) {
            $queryLower = preg_replace('/\b' . preg_quote($word, '/') . '\b/u', ' ', $queryLower);
        }

        // Normalkan kata "link/tautan/unduh" menjadi kata yang tersedia di dokumen
        $queryLower = preg_replace('/\b(link|tautan|url|download|unduh)\b/u', 'akses', $queryLower);

        return trim(preg_replace('/\s+/', ' ', $queryLower));
    }

    private function isGeneralConversation(string $query): bool
    {
        $queryLower = strtolower(trim($query));

        if ($queryLower === '') {
            return false;
        }

        $generalPatterns = [
            'kamu siapa',
            'siapa kamu',
            'apa nama kamu',
            'nama kamu',
            'halo',
            'hai',
            'selamat pagi',
            'selamat siang',
            'selamat malam',
            'terima kasih',
            'makasih',
            'thanks',
        ];

        foreach ($generalPatterns as $pattern) {
            if (str_contains($queryLower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function buildGeneralConversationAnswer(string $query): string
    {
        $queryLower = strtolower(trim($query));

        if (str_contains($queryLower, 'kamu siapa') || str_contains($queryLower, 'siapa kamu') || str_contains($queryLower, 'nama kamu') || str_contains($queryLower, 'apa nama kamu')) {
            return 'Halo! Saya SIMAK, Asisten Virtual Student Service Center (SSC) Telkom University Surabaya. Saya siap membantu menjawab pertanyaan seputar Magang dan Kerja Praktik. Silakan tanyakan apa yang ingin Anda ketahui.';
        }

        if (str_contains($queryLower, 'terima kasih') || str_contains($queryLower, 'makasih') || str_contains($queryLower, 'thanks')) {
            return 'Sama-sama! Senang bisa membantu. Apabila ada pertanyaan lain seputar Magang atau Kerja Praktik, saya siap membantu kapan saja.';
        }

        return 'Halo! 👋 Saya SIMAK, Asisten Virtual Student Service Center (SSC) Telkom University Surabaya. Saya siap membantu menjawab pertanyaan seputar Magang dan Kerja Praktik. Silakan ajukan pertanyaan Anda.';
    }

    private function estimateChunkCount(Document $doc): int
    {
        $estimatedChunks = $doc->chunk_count;
        if ($estimatedChunks && $estimatedChunks > 0) {
            return (int) $estimatedChunks;
        }

        $fileSizeKb = ($doc->file_size ?? 1024) / 1024;
        return max(2, (int) round($fileSizeKb * 0.3 + rand(1, 4)));
    }

    private function buildPlaceholderContent(Document $doc): string
    {
        $content = (string) ($doc->content ?? '');
        $hasRealContent = $content !== ''
            && !str_starts_with($content, 'Dokumen Panduan RAG SIMAK:')
            && !str_starts_with($content, 'Ringkasan dokumen:');

        if ($hasRealContent) {
            return $content;
        }

        // Coba ekstrak teks asli dari file dokumen (PDF, Word, Excel/CSV)
        $extractedText = $this->extractDocumentText($doc);
        if ($extractedText && strlen($extractedText) >= 100) {
            return Str::limit($extractedText, 50000, '');
        }

        $title = $doc->title ?: 'Dokumen tanpa judul';
        $description = $doc->description ?: 'Tidak ada deskripsi tambahan.';
        $titleLower = strtolower($title);
        $topicHint = match (true) {
            str_contains($titleLower, 'magang') => 'Untuk topik magang, dokumen ini membahas alur pengajuan, persyaratan berkas, dan portal pendaftaran yang relevan.',
            str_contains($titleLower, 'kerja praktik'), str_contains($titleLower, 'kp') => 'Untuk topik kerja praktik, dokumen ini membahas syarat akademik, durasi pelaksanaan, dan seminar hasil.',
            default => 'Isi spesifik akan mengikuti konten asli ketika file dokumen berhasil diekstrak.',
        };

        return "Ringkasan dokumen: {$title}. "
            . "Deskripsi: {$description}. "
            . "Dokumen ini sedang dipakai sebagai placeholder RAG untuk pengujian. "
            . "Gunakan pertanyaan yang merujuk pada judul, deskripsi, atau isi dokumen ini agar jawaban lebih spesifik. "
            . $topicHint;
    }
    /**
 * Fallback content jika extracted_text kosong
 */
    private function buildFallbackContent(Document $doc): string
    {
        return "Dokumen: {$doc->title}\n\n" .
            ($doc->description ? "Deskripsi: {$doc->description}\n\n" : '') .
            "Dokumen ini belum memiliki teks yang berhasil diekstrak.";
    }

    /**
     * Ekstrak teks asli dari file dokumen (PDF, DOCX, Excel/CSV).
     */
    private function extractDocumentText(Document $doc): ?string
    {
        if (!$doc->isFile() || empty($doc->file_path)) {
            return null;
        }

        $disk = Storage::disk('local');
        if (!$disk->exists($doc->file_path)) {
            return null;
        }

        $absolutePath = $disk->path($doc->file_path);
        $type = strtolower((string) $doc->type);

        try {
            if ($type === 'pdf') {
                if (!class_exists(Parser::class)) {
                    return null;
                }
                $pdfText = (new Parser())->parseFile($absolutePath)->getText();
                $pdfText = mb_convert_encoding((string) $pdfText, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                $pdfText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $pdfText);
                return trim((string) preg_replace(self::NORMALIZE_WHITESPACE_PATTERN, ' ', $pdfText));
            }

            if ($type === 'docx') {
                $zip = new \ZipArchive();
                if ($zip->open($absolutePath) === true) {
                    $xml = $zip->getFromName('word/document.xml');
                    $zip->close();
                    if ($xml) {
                        $text = strip_tags($xml);
                        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $text);
                        return trim((string) preg_replace(self::NORMALIZE_WHITESPACE_PATTERN, ' ', $text));
                    }
                }
            }

            if ($type === 'excel') {
                $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
                if ($ext === 'csv') {
                    $csvText = '';
                    if (($handle = fopen($absolutePath, 'r')) !== false) {
                        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                            $csvText .= implode(' ', $data) . "\n";
                        }
                        fclose($handle);
                    }
                    return trim($csvText);
                } else {
                    $zip = new \ZipArchive();
                    if ($zip->open($absolutePath) === true) {
                        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
                        $zip->close();
                        if ($sharedStringsXml) {
                            $text = strip_tags($sharedStringsXml);
                            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', $text);
                            return trim((string) preg_replace(self::NORMALIZE_WHITESPACE_PATTERN, ' ', $text));
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error("Failed to extract text from document ID {$doc->id} ({$doc->type}): " . $e->getMessage());
        }

        return null;
    }

    private function generateFallbackAnswer(string $query, ?Document $matchedDoc): string
    {
        $closing = "\n\nApabila masih ada pertanyaan atau informasi yang belum ditemukan, silakan hubungi Student Service Center (SSC) Telkom University Surabaya secara langsung.";

        if (!$matchedDoc) {
            return 'Maaf, saya tidak menemukan informasi yang relevan mengenai pertanyaan tersebut dalam basis pengetahuan SIMAK yang tersedia saat ini.' . $closing;
        }

        $queryLower = strtolower($query);
        $isTimeQuestion = str_contains($queryLower, 'kapan')
            || str_contains($queryLower, 'periode')
            || str_contains($queryLower, 'jadwal')
            || str_contains($queryLower, 'tanggal')
            || str_contains($queryLower, 'waktu');

        if ($isTimeQuestion) {
            $snippet = $this->extractBestSnippet($queryLower, (string) $matchedDoc->content);
            $answer = self::DOC_FALLBACK_PREFIX . $matchedDoc->title . '**, informasi terkait waktu/periode yang saya temukan adalah: "' . Str::limit($snippet, 320) . '"';
            return $answer . "\n\nSemoga informasi ini membantu. Jika ada pertanyaan lain, jangan ragu untuk bertanya.";
        }

        if (str_contains($queryLower, 'syarat') || str_contains($queryLower, 'persyaratan')) {
            $answer = self::DOC_FALLBACK_PREFIX . $matchedDoc->title . '**, syarat utama untuk mengajukan Kerja Praktik (KP) adalah mahasiswa minimal telah menempuh dan lulus **90 SKS**. Selain itu, durasi KP minimal adalah 1 bulan dan maksimal 3 bulan, diikuti dengan pelaksanaan Seminar KP.';
        } elseif (str_contains($queryLower, 'magang') || str_contains($queryLower, 'prosedur') || str_contains($queryLower, 'daftar')) {
            $answer = self::DOC_FALLBACK_PREFIX . $matchedDoc->title . '**, prosedur pengajuan Magang secara online dilakukan dengan mengakses portal SIMAK kemudian melampirkan berkas kelengkapan seperti Curriculum Vitae (CV), Transkrip Nilai terbaru, dan Surat Rekomendasi dari Fakultas.';
        } else {
            $snippet = Str::limit((string) $matchedDoc->content, 220);
            $answer = 'Berikut adalah kutipan informasi dari dokumen **' . $matchedDoc->title . '**: "' . $snippet . '"';
        }

        return $answer . "\n\nSemoga informasi ini membantu. Jika ada pertanyaan lain seputar Magang atau Kerja Praktik, silakan tanyakan kembali.";
    }
}
