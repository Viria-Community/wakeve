<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TransportMode extends Model
{
    protected $table = 'transport_modes';

    protected $fillable = [
        'label',
    ];

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_transport_mode');
    }
}
