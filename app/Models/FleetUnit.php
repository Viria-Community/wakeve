<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FleetUnit extends Model
{
    use SoftDeletes;

    protected $table = 'fleet_units';

    protected $fillable = [
        'company_id',
        'transport_mode_id',
        'type',
        'registration_number',
        'capacity',
        'availability',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function transportMode(): BelongsTo
    {
        return $this->belongsTo(TransportMode::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(FleetDocument::class);
    }
}
