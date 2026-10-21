<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDocument extends Model
{
    protected $table = 'company_documents';

    protected $fillable = [
        'company_id',
        'document_type_id',
        'file_path',
        'issued_at',
        'valid_until',
        'status',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'review_comment',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'valid_until' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isExpired(): bool
    {
        return $this->valid_until->isPast();
    }
}
