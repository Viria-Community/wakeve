<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $fillable = ['name', 'place_type', 'city', 'province'];

    public function originLines(): HasMany
    {
        return $this->hasMany(Line::class, 'origin_place_id');
    }

    public function destinationLines(): HasMany
    {
        return $this->hasMany(Line::class, 'destination_place_id');
    }

    public function lines(): BelongsToMany
    {
        return $this->belongsToMany(Line::class, 'line_places')->withPivot('stop_order');
    }

    public function scopeActiveLines($query)
    {
        return $query->whereHas('lines', fn ($q) => $q->where('status', 'active'));
    }
}
