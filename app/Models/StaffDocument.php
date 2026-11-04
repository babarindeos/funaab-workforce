<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A document in a staff member's Registry file.
 * Registry uploads are approved immediately; staff uploads are "pending"
 * until Registry approves or rejects them (with a reason).
 */
class StaffDocument extends Model
{
    use \App\Models\Concerns\HasUuid;

    public const APPROVED = 'approved';
    public const PENDING  = 'pending';
    public const REJECTED = 'rejected';

    public const SOURCE_REGISTRY = 'registry';
    public const SOURCE_STAFF    = 'staff';

    protected $fillable = [
        'staff_id', 'document_type_id', 'title', 'document_date', 'description',
        'file_path', 'original_name', 'mime_type', 'file_size',
        'source', 'status', 'uploaded_by', 'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected $casts = [
        'document_date' => 'date',
        'reviewed_at'   => 'datetime',
    ];

    public function getRouteKeyName(): string { return 'uuid'; }

    public function staff()    { return $this->belongsTo(Staff::class); }
    public function type()     { return $this->belongsTo(DocumentType::class, 'document_type_id'); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function isApproved(): bool { return $this->status === self::APPROVED; }
    public function isPending(): bool  { return $this->status === self::PENDING; }
    public function isRejected(): bool { return $this->status === self::REJECTED; }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::APPROVED => 'Official',
            self::PENDING  => 'Pending approval',
            self::REJECTED => 'Rejected',
            default        => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::APPROVED => 'badge-approved',
            self::PENDING  => 'badge-pending',
            self::REJECTED => 'badge-rejected',
            default        => 'badge-info',
        };
    }

    public function getSizeLabelAttribute(): string
    {
        $kb = $this->file_size / 1024;
        return $kb >= 1024 ? number_format($kb / 1024, 1) . ' MB' : number_format($kb) . ' KB';
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: ($this->type?->name ?? $this->original_name);
    }
}
