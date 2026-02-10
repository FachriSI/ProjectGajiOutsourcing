<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class GlobalActivityObserver
{
    public function created(Model $model)
    {
        $this->logActivity($model, 'Create', 'Created new ' . class_basename($model) . ': ' . $this->getModelIdentifier($model));
    }

    public function updated(Model $model)
    {
        // Ignore updated_at changes only
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (!empty($changes)) {
            $details = [];
            $original = $model->getOriginal();
            
            foreach ($changes as $key => $newValue) {
                // Handle non-scalar values (arrays/objects)
                if (!is_scalar($newValue) && !is_null($newValue)) {
                    $newValue = json_encode($newValue);
                }

                // Skip long text fields to avoid cluttering logs
                if (strlen((string)$newValue) > 50) {
                    $newValue = substr((string)$newValue, 0, 50) . '...';
                }
                
                $oldValue = $original[$key] ?? '-';

                if (!is_scalar($oldValue) && !is_null($oldValue)) {
                    $oldValue = json_encode($oldValue);
                }
                
                 if (strlen((string)$oldValue) > 50) {
                    $oldValue = substr((string)$oldValue, 0, 50) . '...';
                }

                $details[] = "$key: '$oldValue' -> '$newValue'";
            }

            $description = 'Updated ' . class_basename($model) . ' (' . $this->getModelIdentifier($model) . '). Changes: ' . implode(', ', $details);
            $this->logActivity($model, 'Update', $description);
        }
    }

    public function deleted(Model $model)
    {
        // Check if soft deleted
        if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
             $action = 'Soft Delete';
        } else {
             $action = 'Delete';
        }

        $this->logActivity($model, $action, 'Deleted ' . class_basename($model) . ' (' . $this->getModelIdentifier($model) . ')');
    }

    public function restored(Model $model)
    {
        $this->logActivity($model, 'Restore', 'Restored ' . class_basename($model) . ' (' . $this->getModelIdentifier($model) . ')');
    }

    public function forceDeleted(Model $model)
    {
        $this->logActivity($model, 'Force Delete', 'Permanently deleted ' . class_basename($model) . ' (' . $this->getModelIdentifier($model) . ')');
    }

    protected function getModelIdentifier(Model $model)
    {
        // Try common name attributes
        $candidates = ['name', 'nama', 'title', 'judul', 'description', 'keterangan', 'paket', 'unit_kerja', 'perusahaan'];
        
        foreach ($candidates as $attribute) {
            if (!empty($model->$attribute) && is_scalar($model->$attribute)) {
                return $model->$attribute . ' (ID: ' . $model->getKey() . ')';
            }
        }
        
        return 'ID: ' . $model->getKey();
    }

    protected function logActivity(Model $model, $action, $description)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
