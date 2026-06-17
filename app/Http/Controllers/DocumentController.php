<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'text/plain',  // beberapa sistem mengirim CSV sebagai text/plain
        ],
    ];

    /**
     * Peta tipe dokumen → ekstensi yang diizinkan.
     */
    private const TYPE_EXT_MAP = [
        'pdf' => ['pdf'],
        'docx' => ['doc', 'docx'],
        'excel' => ['xls', 'xlsx', 'csv'],
    ];

    /**
     * GET /dashboard/kelola-dokumen
     * Tampilkan daftar dokumen dengan pagination & filter.
     */
    public function index(Request $request)
    {
        $allowedTypes = ['pdf', 'docx', 'excel'];

        $query = Document::with('uploader')->latest();

        // Filter by type
        if ($request->filled('type') && in_array($request->type, $allowedTypes)) {
            $query->ofType($request->type);
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['active', 'inactive', 'processing'])) {
            $query->where('status', $request->status);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $documents = $query->paginate(10)->withQueryString();

        return view('dashboard.kelola-dokumen', compact('documents'));
    }

    /**
     * POST /dashboard/kelola-dokumen
     * Upload & simpan dokumen baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:pdf,docx,excel'],
            'file' => [
                'required',
                'file',
                'max:10240',                              // 10 MB
                'mimes:pdf,doc,docx,xls,xlsx,csv',
            ],
        ]);

        $file = $request->file('file');

        // ── Validasi cross-check: ekstensi file vs tipe yang dipilih ─────────
        $ext = strtolower($file->getClientOriginalExtension());
        $type = $validated['type'];
        $allowedExts = self::TYPE_EXT_MAP[$type] ?? [];

        if (!in_array($ext, $allowedExts)) {
            return back()
                ->withInput()
                ->withErrors([
                    'file' => sprintf(
                        'File yang diupload (%s) tidak sesuai dengan tipe yang dipilih (%s). Ekstensi yang diizinkan: %s.',
                        strtoupper($ext),
                        strtoupper($type),
                        implode(', ', array_map('strtoupper', $allowedExts))
                    ),
                ]);
        }

        $path = $file->store('documents', 'local');

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $type,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'active',
            'uploaded_by' => Auth::id(),
        ];

        Document::create($data);

        \App\Models\ActivityLog::log("Staff " . Auth::user()->name . " mengunggah file " . $data['original_filename'], 'upload');

        return redirect()
            ->route('dashboard.kelola-dokumen.index')
            ->with('success', 'Dokumen berhasil diupload.');
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
