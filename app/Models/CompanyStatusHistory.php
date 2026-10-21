<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyStatusHistory extends Model
{
    public $timestamps = false; // seule created_at existe, gérée explicitement par le service

    protected $table = 'company_status_history';

    protected $fillable = [
        'company_id',
        'previous_status',
        'new_status',
        'reason',
        'decided_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function decidedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function isAutomatic(): bool
    {
        return $this->decided_by === null;
    }
}
