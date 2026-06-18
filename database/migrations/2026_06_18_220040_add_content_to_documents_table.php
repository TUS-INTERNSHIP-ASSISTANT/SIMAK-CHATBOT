<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Kolom untuk RAG
            if (!Schema::hasColumn('documents', 'extracted_text')) {
                $table->longText('extracted_text')->nullable()->after('status');
            }

            if (!Schema::hasColumn('documents', 'content')) {
                $table->longText('content')->nullable()->after('extracted_text');
            }

            if (!Schema::hasColumn('documents', 'chunk_count')) {
                $table->integer('chunk_count')->default(0)->after('content');
            }

            if (!Schema::hasColumn('documents', 'indexed_at')) {
                $table->timestamp('indexed_at')->nullable()->after('chunk_count');
            }
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'extracted_text',
                'content',
                'chunk_count',
                'indexed_at'
            ]);
        });
    }
};
