<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // ── Core metadata ────────────────────────────────────────────────
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'docx', 'txt', 'link', 'other'])->default('other');

            // ── File fields (untuk tipe upload) ──────────────────────────────
            $table->string('file_path')->nullable();          // path di storage/app/private/
            $table->string('original_filename')->nullable();  // nama file asli
            $table->unsignedBigInteger('file_size')->nullable(); // ukuran dalam bytes
            $table->string('mime_type')->nullable();          // e.g. application/pdf

            // ── Link fields (untuk tipe link) ────────────────────────────────
            $table->string('url')->nullable();

            // ── Workflow ─────────────────────────────────────────────────────
            $table->enum('status', ['active', 'inactive', 'processing'])->default('active');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            // ── RAG pipeline (nullable — siap pakai saat upgrade ke RAG) ─────
            $table->timestamp('indexed_at')->nullable();      // kapan selesai di-index
            $table->integer('chunk_count')->nullable();       // jumlah chunk hasil parsing
            $table->longText('content')->nullable();          // teks hasil parsing/scraping

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
