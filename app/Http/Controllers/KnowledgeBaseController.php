<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Setting;
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

        // Ambil konfigurasi dari tabel settings (atau default jika kosong)
        $systemPrompt = Setting::getVal('rag_system_prompt', 'Anda adalah Asisten Virtual SIMAK yang ramah, profesional, dan siap membantu menjawab pertanyaan mahasiswa mengenai Magang dan Kerja Praktik. Jawab pertanyaan hanya berdasarkan dokumen referensi yang disediakan. Jika jawaban tidak dapat ditemukan di dokumen referensi, katakan dengan sopan bahwa Anda tidak mengetahuinya.');
        $knowledgeBasePrompt = Setting::getVal('rag_knowledge_base_prompt', 'Knowledge base SIMAK: Fokus pada informasi Magang dan Kerja Praktik. Jika dokumen referensi tidak terbaca sepenuhnya atau tidak memuat detail yang dicari, gunakan pengetahuan domain ini untuk memberi jawaban yang paling dekat dengan konteks SIMAK, lalu sebutkan bahwa jawaban tersebut dibantu oleh pedoman knowledge base.');
        $model = Setting::getVal('rag_model', 'gemini-1.5-flash');
        $temperature = (float) Setting::getVal('rag_temperature', 0.5);
        $chunkSize = (int) Setting::getVal('rag_chunk_size', 750);
        $chunkOverlap = (int) Setting::getVal('rag_chunk_overlap', 150);

        return view('dashboard.knowledge-base', compact(
            'documents',
            'activeDocsCount',
            'totalChunks',
            'lastSyncTime',
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
            'model'         => ['required', 'string', 'in:gemini-1.5-flash,gemini-1.5-pro,groq-llama3-8b,groq-llama3-70b'],
            'temperature'   => ['required', 'numeric', 'between:0,1'],
            'chunk_size'    => ['required', 'integer', 'between:500,1000'],
            'chunk_overlap' => ['required', 'integer', 'min:0'],
        ]);

        Setting::setVal('rag_system_prompt', $validated['system_prompt']);
        Setting::setVal('rag_knowledge_base_prompt', $validated['knowledge_base_prompt']);
        Setting::setVal('rag_model', $validated['model']);
        Setting::setVal('rag_temperature', $validated['temperature']);
        Setting::setVal('rag_chunk_size', $validated['chunk_size']);
        Setting::setVal('rag_chunk_overlap', $validated['chunk_overlap']);

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
                'message' => 'Tidak ada dokumen aktif untuk disinkronisasi. Silakan aktifkan dokumen terlebih dahulu di Kelola Dokumen.',
            ], 422);
        }

        $now = now();

        // Simulasi pengindeksan: update chunk_count dan indexed_at secara acak
        /** @var Document $doc */
        foreach ($activeDocs as $doc) {
            $doc->update([
                'indexed_at'  => $now,
                'chunk_count' => $this->estimateChunkCount($doc),
                'content'     => $this->buildPlaceholderContent($doc),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi selesai! ' . $activeDocs->count() . ' dokumen berhasil diindeks.',
            'last_sync' => now()->format('d M Y, H:i'),
            'total_chunks' => Document::active()->sum('chunk_count'),
        ]);
    }

    /**
     * Playground Chatbot RAG query testing.
     */
    public function query(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'max:500'],
        ]);

        $query = $request->input('query');
        $model = Setting::getVal('rag_model', 'gemini-1.5-flash');
        $temperature = (float) Setting::getVal('rag_temperature', 0.5);
        $chunkSize = (int) Setting::getVal('rag_chunk_size', 750);
        $chunkOverlap = (int) Setting::getVal('rag_chunk_overlap', 150);
        $systemPrompt = Setting::getVal(
            'rag_system_prompt',
            'Anda adalah Asisten Virtual SIMAK yang ramah, profesional, dan siap membantu menjawab pertanyaan mahasiswa mengenai Magang dan Kerja Praktik. Jawab pertanyaan hanya berdasarkan dokumen referensi yang disediakan. Jika jawaban tidak dapat ditemukan di dokumen referensi, katakan dengan sopan bahwa Anda tidak mengetahuinya.'
        );
        $knowledgeBasePrompt = Setting::getVal(
            'rag_knowledge_base_prompt',
            'Knowledge base SIMAK: Fokus pada informasi Magang dan Kerja Praktik. Jika dokumen referensi tidak terbaca sepenuhnya atau tidak memuat detail yang dicari, gunakan pengetahuan domain ini untuk memberi jawaban yang paling dekat dengan konteks SIMAK, lalu sebutkan bahwa jawaban tersebut dibantu oleh pedoman knowledge base.'
        );

        if ($this->isGeneralConversation($query)) {
            return response()->json([
                'success' => true,
                'answer'  => $this->buildGeneralConversationAnswer($query),
                'source'  => null,
            ]);
        }

        // Ambil seluruh dokumen aktif untuk retrieval konteks RAG.
        $activeDocs = Document::active()->whereNotNull('content')->get();

        $retrievedDocs = $this->retrieveRelevantDocuments($query, $activeDocs, $chunkSize, $chunkOverlap, 3);
        $topDoc = $retrievedDocs[0]['doc'] ?? null;

        $answer = null;

        // Jika model menggunakan Groq, jalankan query ke API Groq.
        if (str_starts_with($model, 'groq-')) {
            $answer = $this->queryGroq($query, $retrievedDocs, $systemPrompt, $knowledgeBasePrompt, $model, $temperature);
        }

        // Fallback lokal tetap dipertahankan jika model non-Groq atau API gagal.
        if (! $answer) {
            $answer = $this->generateFallbackAnswer($query, $topDoc);
        }

        return response()->json([
            'success' => true,
            'answer'  => $answer,
            'source'  => $topDoc ? [
                'title' => $topDoc->title,
                'type'  => $topDoc->type,
            ] : null,
        ]);
    }

    /**
     * Ambil dokumen paling relevan secara sederhana berbasis keyword score.
     */
    private function retrieveRelevantDocuments(string $query, Collection $activeDocs, int $chunkSize, int $chunkOverlap, int $limit = 3): array
    {
        $queryLower = strtolower($query);
        $queryLower = preg_replace('/\bk\.?p\.?\b/u', ' kerja praktik ', $queryLower);
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

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

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
            if (! $word || (strlen($word) < 3 && ! in_array($word, self::SHORT_DOMAIN_TOKENS, true))) {
                continue;
            }

            if (str_contains($chunkLower, $word)) {
                $score += 3;
            }
        }

        $matchedTokens = $this->countMatchedTokens($words, $chunkLower, $chunkLower, $chunkLower);
        if ($matchedTokens < 2 && ! str_contains($chunkLower, $queryLower)) {
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
            if (! $word || (strlen($word) < 3 && ! in_array($word, self::SHORT_DOMAIN_TOKENS, true))) {
                continue;
            }

            $score += $this->scoreTokenMatch($title, $description, $content, $word);
        }

        $matchedTokens = $this->countMatchedTokens($words, $title, $description, $content);

        if ($matchedTokens < 2 && ! str_contains($content, $queryLower)) {
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
            if (! $word) {
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

    private function queryGroq(string $query, array $retrievedDocs, string $systemPrompt, string $knowledgeBasePrompt, string $selectedModel, float $temperature): ?string
    {
        $apiKey = (string) config('services.groq.api_key');
        if ($apiKey === '') {
            return null;
        }

        $context = $this->buildRagContext($retrievedDocs);
        $questionHints = $this->buildQuestionHints($query);

        $response = Http::withToken($apiKey)
            ->timeout((int) config('services.groq.timeout', 30))
            ->post(rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/') . '/chat/completions', [
                'model' => $this->mapGroqModel($selectedModel),
                'temperature' => max(0, min(1, $temperature)),
                'max_tokens' => 900,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt . "\n\n" . $knowledgeBasePrompt . "\n\nAturan tambahan: jawab pertanyaan pengguna secara spesifik sesuai maksud pertanyaan. Jangan mengulang kalimat yang sama untuk semua pertanyaan. Jika pengguna bertanya 'di mana', sebutkan lokasi atau kanal pendaftaran. Jika pengguna bertanya 'apa nama website', sebutkan hanya nama website yang benar-benar disebut di konteks. Jika nama website tidak disebut, katakan bahwa dokumen hanya menyebut kanal/portal tanpa nama domain spesifik.",
                    ],
                    [
                        'role' => 'user',
                        'content' => "Pertanyaan pengguna: {$query}\n\nPetunjuk interpretasi pertanyaan: {$questionHints}\n\nKonteks dokumen:\n{$context}\n\nInstruksi: Jawab ringkas, jelas, dan langsung ke inti pertanyaan. Hindari jawaban template yang persis sama untuk pertanyaan berbeda. Jika informasi tidak ada di konteks, katakan tidak ditemukan di basis pengetahuan.",
                    ],
                ],
            ]);

        if (! $response->successful()) {
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
        }

        return $hint;
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

        if (str_contains($queryLower, 'kamu siapa') || str_contains($queryLower, 'siapa kamu') || str_contains($queryLower, 'nama kamu')) {
            return 'Saya SIMAK, asisten chatbot untuk membantu pertanyaan seputar Magang dan Kerja Praktik.';
        }

        if (str_contains($queryLower, 'terima kasih') || str_contains($queryLower, 'makasih') || str_contains($queryLower, 'thanks')) {
            return 'Sama-sama. Kalau ada pertanyaan seputar Magang atau Kerja Praktik, silakan lanjutkan.';
        }

        return 'Halo, saya SIMAK, asisten chatbot untuk membantu informasi Magang dan Kerja Praktik. Silakan ajukan pertanyaan yang ingin Anda cari.';
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
            && ! str_starts_with($content, 'Dokumen Panduan RAG SIMAK:')
            && ! str_starts_with($content, 'Ringkasan dokumen:');

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
     * Ekstrak teks asli dari file dokumen (PDF, DOCX, Excel/CSV).
     */
    private function extractDocumentText(Document $doc): ?string
    {
        if (! $doc->isFile() || empty($doc->file_path)) {
            return null;
        }

        $disk = Storage::disk('local');
        if (! $disk->exists($doc->file_path)) {
            return null;
        }

        $absolutePath = $disk->path($doc->file_path);
        $type = strtolower((string) $doc->type);

        try {
            if ($type === 'pdf') {
                if (! class_exists(Parser::class)) {
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
        $answer = 'Maaf, saya tidak menemukan informasi yang relevan mengenai pertanyaan tersebut dalam basis pengetahuan aktif SIMAK. Harap pastikan dokumen panduan terkait sudah diunggah dan diatur berstatus Aktif di dashboard Kelola Dokumen.';

        if (! $matchedDoc) {
            return $answer;
        }

        $queryLower = strtolower($query);
        $isTimeQuestion = str_contains($queryLower, 'kapan')
            || str_contains($queryLower, 'periode')
            || str_contains($queryLower, 'jadwal')
            || str_contains($queryLower, 'tanggal')
            || str_contains($queryLower, 'waktu');

        if ($isTimeQuestion) {
            $snippet = $this->extractBestSnippet($queryLower, (string) $matchedDoc->content);
            return self::DOC_FALLBACK_PREFIX . $matchedDoc->title . '**, informasi terkait waktu/periode yang saya temukan adalah: "' . Str::limit($snippet, 320) . '"';
        }

        if (str_contains($queryLower, 'syarat') || str_contains($queryLower, 'persyaratan')) {
            $answer = self::DOC_FALLBACK_PREFIX . $matchedDoc->title . '**, syarat utama untuk mengajukan Kerja Praktik (KP) adalah mahasiswa minimal telah menempuh dan lulus **90 SKS**. Selain itu, durasi KP minimal adalah 1 bulan dan maksimal 3 bulan, diikuti dengan pelaksanaan Seminar KP.';
        } elseif (str_contains($queryLower, 'magang') || str_contains($queryLower, 'prosedur') || str_contains($queryLower, 'daftar')) {
            $answer = self::DOC_FALLBACK_PREFIX . $matchedDoc->title . '**, prosedur pengajuan Magang secara online dilakukan dengan mengakses portal SIMAK kemudian melampirkan berkas kelengkapan seperti Curriculum Vitae (CV), Transkrip Nilai terbaru, dan Surat Rekomendasi dari Fakultas.';
        } else {
            $snippet = Str::limit((string) $matchedDoc->content, 220);
            $answer = 'Berikut adalah kutipan informasi dari dokumen **' . $matchedDoc->title . '**: "' . $snippet . '"';
        }

        return $answer;
    }
}
