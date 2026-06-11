<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * - Hapus semua dokumen bertipe 'txt', 'link', 'other' (beserta file fisiknya)
     * - Tambahkan nilai 'excel' ke enum type
     * - Perkecil pilihan enum menjadi: pdf, docx, excel
     */
    public function up(): void
    {
        // 1. Hapus file fisik untuk dokumen yang akan dihapus
        $docsToDelete = DB::table('documents')
            ->whereIn('type', ['txt', 'link', 'other'])
            ->whereNull('deleted_at')
            ->get(['file_path']);

        foreach ($docsToDelete as $doc) {
            if ($doc->file_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($doc->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($doc->file_path);
            }
        }

        // 2. Hard-delete (termasuk soft-deleted) semua dokumen bertipe lama
        DB::table('documents')
            ->whereIn('type', ['txt', 'link', 'other'])
            ->delete();

        // 3. Alter enum column — MySQL memerlukan MODIFY COLUMN
        // Ubah enum menjadi: pdf, docx, excel
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('pdf','docx','excel') NOT NULL DEFAULT 'pdf'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum ke nilai asal
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('pdf','docx','txt','link','other') NOT NULL DEFAULT 'other'");
        }
    }
};
