<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $table = 'document_types';

    protected $fillable = [
        'label',
        'applicable_to',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function companyDocuments(): HasMany
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function fleetDocuments(): HasMany
    {
        return $this->hasMany(FleetDocument::class);
    }
}
