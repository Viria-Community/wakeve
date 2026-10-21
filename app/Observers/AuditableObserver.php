<?php

namespace App\Observers;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    private const HIDDEN_FIELDS = ['password', 'remember_token', 'file_path', 'updated_at', 'created_at'];

    public function created(Model $model): void
    {
        AuditLogger::record('creation', 'Création de '.class_basename($model), $model);
    }

    public function updated(Model $model): void
    {
        $fields = array_values(array_diff(array_keys($model->getChanges()), self::HIDDEN_FIELDS));
        if ($fields !== []) {
            AuditLogger::record('modification', 'Modification de '.class_basename($model), $model, $fields);
        }
    }

    public function deleted(Model $model): void
    {
        AuditLogger::record('suppression', 'Suppression de '.class_basename($model), $model);
    }
}
