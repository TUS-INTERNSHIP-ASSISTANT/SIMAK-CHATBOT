<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class DocumentController extends Controller
{
    /**
     * Peta tipe dokumen → MIME types yang diizinkan.
     */
    private const TYPE_MIME_MAP = [
        'pdf' => ['application/pdf'],
        'docx' => [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'excel' => [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
            'text/plain',
        ],
    ];

    private const TYPE_EXT_MAP = [
        'pdf' => ['pdf'],
        'docx' => ['doc', 'docx'],
        'excel' => ['xls', 'xlsx', 'csv'],
    ];

    public function index(Request $request)
    {
        $allowedTypes = ['pdf', 'docx', 'excel'];

        $query = Document::with('uploader')->latest();

        if ($request->filled('type') && in_array($request->type, $allowedTypes)) {
            $query->ofType($request->type);
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive', 'processing'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $documents = $query->paginate(10)->withQueryString();

        return view('dashboard.kelola-dokumen', compact('documents'));
    }

    /**
     * Upload Dokumen + Extract Text (khusus PDF)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type'        => ['required', 'in:pdf,docx,excel'],
            'file'        => ['required','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,csv'],
        ]);

        $file = $request->file('file');
        $type = $validated['type'];

        // Validasi ekstensi
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::TYPE_EXT_MAP[$type] ?? [])) {
            return back()->withInput()->withErrors([
                'file' => "File tidak sesuai dengan tipe yang dipilih ({$type}).",
            ]);
        }

        $path = $file->store('documents', 'local');

        $data = [
            'title'             => $validated['title'],
            'description'       => $validated['description'] ?? null,
            'type'              => $type,
            'file_path'         => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size'         => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'status'            => 'processing',           // Mulai processing
            'uploaded_by'       => Auth::id(),
            'extracted_text'    => null,                   // Akan diisi nanti
        ];

        $document = Document::create($data);

        // === EKSTRAKSI TEKS UNTUK PDF ===
        if ($type === 'pdf') {
            try {
                $fullPath = Storage::disk('local')->path($path);

                $parser = new Parser();
                $pdf = $parser->parseFile($fullPath);

                $text = $pdf->getText();                    // Ambil semua teks
                $text = trim(preg_replace('/\s+/', ' ', $text)); // Bersihkan whitespace

                $document->update([
                    'extracted_text' => $text,
                    'status'         => 'active',
                ]);

            } catch (\Exception $e) {
                // Tetap simpan dokumen meski ekstraksi gagal
                $document->update([
                    'status' => 'active',
                    'extracted_text' => 'Gagal mengekstrak teks: ' . $e->getMessage(),
                ]);
                \Log::error("PDF Extraction failed for document {$document->id}: " . $e->getMessage());
            }
        } else {
            // Untuk docx/excel sementara kosong (bisa ditambah nanti)
            $document->update(['status' => 'active']);
        }

        \App\Models\ActivityLog::log(
            "Staff " . Auth::user()->name . " mengunggah file " . $data['original_filename'],
            'upload'
        );

        return redirect()
            ->route('dashboard.kelola-dokumen.index')
            ->with('success', 'Dokumen berhasil diupload dan diproses.');
    }

    /**
     * GET /dashboard/kelola-dokumen/{document}
     * Download file dokumen.
     */
    public function show(Document $document)
    {
        // Guard: pastikan file_path tidak null/kosong
        if (empty($document->file_path)) {
            return redirect()
                ->route('dashboard.kelola-dokumen.index')
                ->with('error', 'File tidak tersedia untuk dokumen ini.');
        }

        try {
            // Pastikan file benar-benar ada di storage
            if (!Storage::disk('local')->exists($document->file_path)) {
                return redirect()
                    ->route('dashboard.kelola-dokumen.index')
                    ->with('error', 'File tidak ditemukan di server. Mungkin sudah dihapus.');
            }

            return Storage::disk('local')->download(
                $document->file_path,
                $document->original_filename ?? basename($document->file_path)
            );
        } catch (\Exception $e) {
            return redirect()
                ->route('dashboard.kelola-dokumen.index')
                ->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * PUT /dashboard/kelola-dokumen/{document}
     * Update metadata dokumen (judul, deskripsi, status).
     */
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $document->update($validated);

        \App\Models\ActivityLog::log("Staff " . Auth::user()->name . " memperbarui dokumen " . $document->title, 'update');

        return redirect()
            ->route('dashboard.kelola-dokumen.index')
            ->with('success', 'Dokumen "' . $document->title . '" berhasil diperbarui.');
    }

    /**
     * DELETE /dashboard/kelola-dokumen/{document}
     * Soft-delete dokumen dan hapus file fisik dari storage.
     */
    public function destroy(Document $document)
    {
        // Hapus file fisik dari storage sebelum soft-delete
        if (!empty($document->file_path)) {
            try {
                if (Storage::disk('local')->exists($document->file_path)) {
                    Storage::disk('local')->delete($document->file_path);
                }
            } catch (\Exception $e) {
                // Lanjutkan proses delete meski file fisik gagal dihapus
            }
        }

        $title = $document->title;
        $document->delete();

        \App\Models\ActivityLog::log("Staff " . Auth::user()->name . " menghapus dokumen " . $title, 'delete');

        return redirect()
            ->route('dashboard.kelola-dokumen.index')
            ->with('success', 'Dokumen "' . $title . '" berhasil dihapus.');
    }

    /**
     * POST /dashboard/kelola-dokumen/{id}/restore
     * Restore dokumen yang di-soft-delete.
     */
    public function restore(int $id)
    {
        $document = Document::withTrashed()->findOrFail($id);
        $document->restore();

        \App\Models\ActivityLog::log("Staff " . Auth::user()->name . " memulihkan dokumen " . $document->title, 'update');

        return redirect()
            ->route('dashboard.kelola-dokumen.index')
            ->with('success', 'Dokumen berhasil dipulihkan.');
    }
}
