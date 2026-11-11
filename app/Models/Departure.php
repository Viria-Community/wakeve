<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departure extends Model
{
    use SoftDeletes;

    protected $table = 'departures';

    protected $fillable = [
        'departure_date',
        'departure_time',
        'status',
        'line_id',
        'fleet_unit_id',
        'agency_id',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'departure_time' => 'datetime:H:i:s',
        ];
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    public function fleetUnit(): BelongsTo
    {
        return $this->belongsTo(FleetUnit::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'line_id.company_id');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->whereHas('line', fn ($q) => $q->where('company_id', $companyId));
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function fleetUnitBelongsToLineCompany(): bool
    {
        return $this->fleetUnit
            && $this->fleetUnit->company_id === $this->line?->company_id;
    }
}
