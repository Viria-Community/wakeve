<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[\Illuminate\Database\Eloquent\Attributes\ObservedBy([\App\Observers\CompanyObserver::class])]
class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'application_reference',
        'legal_name',
        'trade_name',
        'rccm',
        'nif',
        'email',
        'phone',
        'address',
        'city',
        'province',
        // 'status' volontairement exclu — ADR-005 : seul
        // CompanyStatusService peut modifier le statut d'une compagnie.
    ];

    protected function casts(): array
    {
        return [
            'validated_at' => 'datetime',
            'status' => CompanyStatus::class, // lecture typée ; écriture réservée au service
        ];
    }

    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function fleetUnits(): HasMany
    {
        return $this->hasMany(FleetUnit::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(CompanyStatusHistory::class)
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function transportModes(): BelongsToMany
    {
        return $this->belongsToMany(TransportMode::class, 'company_transport_mode');
    }
}
