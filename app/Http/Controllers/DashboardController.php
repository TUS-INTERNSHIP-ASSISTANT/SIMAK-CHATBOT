<?php

namespace App\Http\Controllers;

use App\Models\Document;

class DashboardController extends Controller
{
    /**
     * GET /dashboard
     * Halaman utama dashboard dengan statistik ringkas.
     */
    public function index()
    {
        // Jumlah dokumen aktif (tidak ter-soft-delete)
        $activeDocuments = Document::active()->count();

        // Jumlah dokumen nonaktif
        $inactiveDocuments = Document::where('status', 'inactive')->count();

        // Total semua dokumen (tidak ter-soft-delete)
        $totalDocuments = Document::count();

        // Dokumen terakhir diupload
        $lastDocument = Document::latest()->first();

        // Hitung berapa hari lalu dokumen terakhir diupload
        $lastUploadDaysAgo = $lastDocument
            ? (int) $lastDocument->created_at->diffInDays(now())
            : null;

        return view('components.dashboard', compact(
            'activeDocuments',
            'inactiveDocuments',
            'totalDocuments',
            'lastDocument',
            'lastUploadDaysAgo',
        ));
    }
}
