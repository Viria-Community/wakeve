<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FleetDocument extends Model
{
    protected $table = 'fleet_documents';

    protected $fillable = [
        'fleet_unit_id',
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

    public function fleetUnit(): BelongsTo
    {
        return $this->belongsTo(FleetUnit::class);
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
