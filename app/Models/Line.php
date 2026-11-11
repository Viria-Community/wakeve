<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Line extends Model
{
    protected $fillable = [
        'label',
        'status',
        'company_id',
        'transport_mode_id',
        'origin_place_id',
        'destination_place_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function transportMode(): BelongsTo
    {
        return $this->belongsTo(TransportMode::class);
    }

    public function originPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'origin_place_id');
    }

    public function destinationPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'destination_place_id');
    }

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'line_places')
            ->withPivot('stop_order')
            ->orderBy('line_places.stop_order');
    }

    public function departures(): HasMany
    {
        return $this->hasMany(Departure::class);
    }

    public function fares(): HasMany
    {
        return $this->hasMany(Fare::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function modeCoherent(): bool
    {
        return $this->company
            ->transportModes()
            ->whereKey($this->transport_mode_id)
            ->exists();
    }
}
