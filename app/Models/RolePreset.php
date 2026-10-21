<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePreset extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return ['permissions' => 'array'];
    }
}
