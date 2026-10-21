<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['agency_id', 'user_id', 'company_id', 'action', 'subject_type', 'subject_id', 'subject_label', 'description', 'changed_fields', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['changed_fields' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }
}
