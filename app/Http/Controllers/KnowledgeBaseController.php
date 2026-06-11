<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
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
            'model'         => ['required', 'string', 'in:gemini-1.5-flash,gemini-1.5-pro,groq-llama3-8b,groq-llama3-70b'],
            'temperature'   => ['required', 'numeric', 'between:0,1'],
            'chunk_size'    => ['required', 'integer', 'between:500,1000'],
            'chunk_overlap' => ['required', 'integer', 'min:0'],
        ]);

        Setting::setVal('rag_system_prompt', $validated['system_prompt']);
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

        // Simulasi pengindeksan: update chunk_count dan indexed_at secara acak
        foreach ($activeDocs as $doc) {
            // Jika chunk_count masih kosong/0, hitung perkiraan acak berdasarkan file size (1 KB ~ 2-4 chunks)
            $estimatedChunks = $doc->chunk_count;
            if (!$estimatedChunks || $estimatedChunks === 0) {
                $fileSizeKb = ($doc->file_size ?? 1024) / 1024;
                $estimatedChunks = max(2, (int) round($fileSizeKb * 0.3 + rand(1, 4)));
            }

            // Simulasikan teks content jika kosong (untuk simulasi pencarian RAG di playground)
            $content = $doc->content;
            if (empty($content)) {
                $content = "Dokumen Panduan RAG SIMAK: Ini adalah isi konten dokumen untuk " . $doc->title . ". "
                    . "Mengenai Kerja Praktik (KP): Syarat pengajuan KP adalah mahasiswa minimal telah lulus 90 SKS, "
                    . "durasi pelaksanaan KP minimal adalah 1 bulan dan maksimal 3 bulan. Seminar KP wajib diikuti setelah laporan disetujui. "
                    . "Mengenai Magang: Prosedur pendaftaran magang online harus melalui portal SIMAK dan melampirkan berkas Curriculum Vitae (CV), "
                    . "Transkrip Nilai terbaru, dan Surat Rekomendasi Fakultas.";
            }

            $doc->update([
                'indexed_at'  => now(),
                'chunk_count' => $estimatedChunks,
                'content'     => $content,
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
        $queryLower = strtolower($query);

        // Cari di database dokumen aktif yang isinya mirip dengan query
        $activeDocs = Document::active()->whereNotNull('content')->get();
        $matchedDoc = null;
        $answer = null;

        foreach ($activeDocs as $doc) {
            $contentLower = strtolower($doc->content);
            // Cek kecocokan kata kunci dasar
            if (str_contains($contentLower, $queryLower) || 
                $this->hasKeywordMatch($queryLower, $doc->title) ||
                $this->hasKeywordMatch($queryLower, $doc->description)
            ) {
                $matchedDoc = $doc;
                break;
            }
        }

        if ($matchedDoc) {
            // Simulasikan jawaban berdasarkan dokumen pencocokan
            if (str_contains($queryLower, 'syarat') || str_contains($queryLower, 'kp') || str_contains($queryLower, 'kerja praktik')) {
                $answer = "Berdasarkan dokumen **" . $matchedDoc->title . "**, syarat utama untuk mengajukan Kerja Praktik (KP) adalah mahasiswa minimal telah menempuh dan lulus **90 SKS**. Selain itu, durasi KP minimal adalah 1 bulan dan maksimal 3 bulan, diikuti dengan pelaksanaan Seminar KP.";
            } elseif (str_contains($queryLower, 'magang') || str_contains($queryLower, 'prosedur') || str_contains($queryLower, 'daftar')) {
                $answer = "Berdasarkan dokumen **" . $matchedDoc->title . "**, prosedur pengajuan Magang secara online dilakukan dengan mengakses portal SIMAK kemudian melampirkan berkas kelengkapan seperti Curriculum Vitae (CV), Transkrip Nilai terbaru, dan Surat Rekomendasi dari Fakultas.";
            } else {
                // Jawaban fallback generik menggunakan snippet konten dokumen
                $snippet = Str::limit($matchedDoc->content, 220);
                $answer = "Berikut adalah kutipan informasi dari dokumen **" . $matchedDoc->title . "**: \"" . $snippet . "\"";
            }
        } else {
            // Fallback jika tidak ada dokumen yang cocok
            $answer = "Maaf, saya tidak menemukan informasi yang relevan mengenai pertanyaan tersebut dalam basis pengetahuan aktif SIMAK. Harap pastikan dokumen panduan terkait sudah diunggah dan diatur berstatus Aktif di dashboard Kelola Dokumen.";
        }

        return response()->json([
            'success' => true,
            'answer'  => $answer,
            'source'  => $matchedDoc ? [
                'title' => $matchedDoc->title,
                'type'  => $matchedDoc->type,
            ] : null,
        ]);
    }

    /**
     * Helper pencocokan keyword sederhana.
     */
    private function hasKeywordMatch(string $query, ?string $text): bool
    {
        if (empty($text)) return false;
        $textLower = strtolower($text);
        $words = explode(' ', str_replace(['?', ',', '.', '/'], '', $query));
        
        foreach ($words as $word) {
            if (strlen($word) > 2 && str_contains($textLower, $word)) {
                return true;
            }
        }
        return false;
    }
}
