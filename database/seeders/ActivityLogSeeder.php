<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : null;
        $userName = $user ? $user->name : 'Staff Akademik';

        $activities = [
            [
                'activity' => "{$userName} mengunggah file SOP KP.pdf",
                'type' => 'upload',
                'created_at' => now()->subHours(2),
            ],
            [
                'activity' => 'Knowledge Base diperbarui',
                'type' => 'update',
                'created_at' => now()->subHours(1)->subMinutes(30),
            ],
            [
                'activity' => "{$userName} menghapus dokumen lama",
                'type' => 'delete',
                'created_at' => now()->subHour(),
            ],
            [
                'activity' => "{$userName} berhasil login",
                'type' => 'login',
                'created_at' => now()->subMinutes(45),
            ],
            [
                'activity' => 'Statistik chatbot diperbarui otomatis',
                'type' => 'update',
                'created_at' => now()->subMinutes(15),
            ],
        ];

        foreach ($activities as $act) {
            ActivityLog::create([
                'user_id' => $userId,
                'activity' => $act['activity'],
                'type' => $act['type'],
                'created_at' => $act['created_at'],
                'updated_at' => $act['created_at'],
            ]);
        }
    }
}
