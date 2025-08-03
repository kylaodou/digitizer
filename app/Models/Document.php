<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'original_filename',
        'file_path',
        'thumbnail_path',
        'mime_type',
        'file_size',
        'page_count',
        'document_type', // Type de scan (recto/recto_verso)
        'processing_cost',
        'status',
        'ocr_text',
        'is_ocr_completed',
        'processed_at',
        'metadata',
        // Colonnes IA
        'ai_summary',
        'ai_analysis_completed',
        'ai_document_type',
        'extracted_info',
        'ai_analyzed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'page_count' => 'integer',
        'processing_cost' => 'decimal:2',
        'is_ocr_completed' => 'boolean',
        'ai_analysis_completed' => 'boolean',
        'processed_at' => 'datetime',
        'ai_analyzed_at' => 'datetime',
        'metadata' => 'array',
        'extracted_info' => 'array',
    ];

    /**
     * Get the user that owns the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoice items for the document.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Calculate processing cost based on document type and pages.
     */
    public function calculateProcessingCost(): float
    {
        $rectoRate = 200; // FCFA per recto page
        $rectoVersoRate = 150; // FCFA per recto-verso page

        if ($this->document_type === 'recto_verso') {
            return $this->page_count * $rectoVersoRate;
        } else {
            return $this->page_count * $rectoRate;
        }
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    /**
     * Check if document processing is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if document processing failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark document as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark document as failed.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
            'processed_at' => now(),
        ]);
    }
}
