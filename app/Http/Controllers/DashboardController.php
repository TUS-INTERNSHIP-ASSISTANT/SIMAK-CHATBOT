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

    /**
     * POST /dashboard/manajemen-staff
     * Update the logged-in staff user's name and/or password.
     */
    public function updateStaff(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $rules = [
            'nama_lengkap' => ['required', 'string', 'max:255'],
        ];

        // If password is provided, validate it
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        $oldName = $user->name;
        $user->name = $validated['nama_lengkap'];

        $passwordChanged = false;
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
            $passwordChanged = true;
        }

        $user->save();

        // Log the activity
        if ($oldName !== $user->name && $passwordChanged) {
            \App\Models\ActivityLog::log("Staff {$oldName} memperbarui nama menjadi {$user->name} dan mengganti password", 'update', $user->id);
        } elseif ($oldName !== $user->name) {
            \App\Models\ActivityLog::log("Staff {$oldName} memperbarui nama menjadi {$user->name}", 'update', $user->id);
        } elseif ($passwordChanged) {
            \App\Models\ActivityLog::log("Staff {$user->name} memperbarui password", 'update', $user->id);
        }

        return redirect()->back()->with('success', 'Profil staff berhasil diperbarui.');
    }
}
