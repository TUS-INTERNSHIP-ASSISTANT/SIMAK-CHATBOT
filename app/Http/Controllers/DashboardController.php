<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ActivityLog;
use App\Models\ChatLog;
use Illuminate\Http\Request;

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

        // Load 10 latest activity logs for initial server side rendering
        $initialLogs = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'activity' => $log->activity,
                    'type' => $log->type,
                    'time' => $log->created_at->format('d M Y, H:i'),
                ];
            });

        // Load Top 5 popular questions dynamically from chat_logs
        $popularLogs = ChatLog::select('normalized_message')
            ->selectRaw('count(*) as count')
            ->whereNotNull('normalized_message')
            ->groupBy('normalized_message')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $faqData = $popularLogs->map(function ($item, $index) {
            $rawMessage = ChatLog::where('normalized_message', $item->normalized_message)
                ->select('message')
                ->get()
                ->groupBy('message')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first();

            return [
                'no' => $index + 1,
                'question' => $rawMessage ?: 'Pertanyaan tidak diketahui',
                'count' => $item->count,
            ];
        })->toArray();

        return view('components.dashboard', compact(
            'activeDocuments',
            'inactiveDocuments',
            'totalDocuments',
            'lastDocument',
            'lastUploadDaysAgo',
            'initialLogs',
            'faqData',
        ));
    }

    /**
     * GET /dashboard/activity-logs
     * Mengembalikan list log aktivitas staff untuk polling real-time.
     */
    public function activityLogs()
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'activity' => $log->activity,
                    'type' => $log->type,
                    'time' => $log->created_at->format('d M Y, H:i'),
                ];
            });

        return response()->json($logs);
    }

    /**
     * GET /dashboard/pertanyaan-populer
     * Halaman analitik daftar pertanyaan populer (Top 10 atau Semua).
     */
    public function popularQuestions(Request $request)
    {
        $showAll = $request->boolean('all', false);

        $query = ChatLog::select('normalized_message')
            ->selectRaw('count(*) as count')
            ->whereNotNull('normalized_message')
            ->groupBy('normalized_message')
            ->orderByDesc('count');

        if (!$showAll) {
            $query->limit(10);
        }

        $logs = $query->get();

        $popularQuestions = $logs->map(function ($item, $index) {
            $rawMessage = ChatLog::where('normalized_message', $item->normalized_message)
                ->select('message')
                ->get()
                ->groupBy('message')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first();

            return [
                'no' => $index + 1,
                'question' => $rawMessage ?: 'Pertanyaan tidak diketahui',
                'count' => $item->count,
            ];
        });

        return view('dashboard.pertanyaan-populer', compact('popularQuestions', 'showAll'));
    }
}
