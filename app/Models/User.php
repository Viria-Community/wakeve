<?php

namespace App\Models;

use App\Services\PermissionCatalog;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
        'status',
        'custom_role_id',
        'platform_role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtolower(trim($value)),
        );
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function decidedStatusChanges(): HasMany
    {
        return $this->hasMany(CompanyStatusHistory::class, 'decided_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPlatformStaff(): bool
    {
        return $this->role === 'platform_staff';
    }

    public function isPlatformMember(): bool
    {
        return $this->isAdmin() || $this->isPlatformStaff();
    }

    public function platformRole(): BelongsTo
    {
        return $this->belongsTo(PlatformRole::class);
    }

    public function permitsPlatform(string $permission): bool
    {
        return $this->isAdmin() || ($this->isPlatformStaff()
            && $this->platformRole
            && in_array($permission, $this->platformRole->permissions ?? [], true));
    }

    public function customRole(): BelongsTo
    {
        return $this->belongsTo(CustomRole::class);
    }

    public function canManageRoles(): bool
    {
        return $this->isAdmin() || ($this->role === 'company_manager' && $this->company_id && ! $this->custom_role_id);
    }

    public function permits(string $permission): bool
    {
        if ($this->isPlatformMember()) {
            return $this->isAdmin() || $this->permitsPlatform($permission);
        }

        if (! $this->custom_role_id) {
            return true;
        }

        $role = $this->customRole;

        return $role && $role->company_id === $this->company_id
            && ($role->scope !== null || $role->base_role === $this->role)
            && in_array($permission, $role->permissions, true)
            && in_array($permission, $role->scope !== null ? PermissionCatalog::forScope($role->scope) : PermissionCatalog::allowed($this->role), true);
    }

    public function accessScope(): string
    {
        if ($this->custom_role_id && $this->customRole?->scope !== null) {
            return $this->customRole->scope;
        }

        return match ($this->role) {
            'agency_manager' => 'agency',
            'agent' => 'self',
            default => 'company',
        };
    }

    public function hasFreeRole(): bool
    {
        return $this->custom_role_id && $this->customRole?->scope !== null;
    }

    public function canAccessCompany(Company $company): bool
    {
        return $this->isAdmin() || $this->permitsPlatform('companies.view') || $this->company_id === $company->getKey();
    }
}
