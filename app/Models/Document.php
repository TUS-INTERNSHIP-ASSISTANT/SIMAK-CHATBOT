<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'url',
        'status',
        'uploaded_by',
        'indexed_at',
        'chunk_count',
        'content',
    ];

    protected $casts = [
        'indexed_at' => 'datetime',
        'file_size'  => 'integer',
        'chunk_count' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Apakah dokumen ini berupa file yang di-upload (bukan link).
     */
    public function isFile(): bool
    {
        return $this->type !== 'link' && ! empty($this->file_path);
    }

    /**
     * Apakah dokumen ini berupa link eksternal.
     */
    public function isLink(): bool
    {
        return $this->type === 'link';
    }

    /**
     * Ukuran file dalam format human-readable (e.g. "1.2 MB").
     */
    public function formattedSize(): string
    {
        if (! $this->file_size) {
            return '—';
        }

        $bytes = (int) $this->file_size;

        if ($bytes < 1024) {
            return "{$bytes} B";
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return round($bytes / 1048576, 1) . ' MB';
        }
    }
}
