<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fare extends Model
{
    protected $table = 'fares';

    protected $fillable = [
        'amount',
        'fare_class',
        'travel_class',
        'status',
        'line_id',
        'origin_place_id',
        'destination_place_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    public function originPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'origin_place_id');
    }

    public function destinationPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'destination_place_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }
}
