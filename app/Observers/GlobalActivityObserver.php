<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class GlobalActivityObserver
{
    public function created(Model $model)
    {
        $this->logActivity($model, 'Create', 'Created new ' . class_basename($model));
    }

    public function updated(Model $model)
    {
        // Ignore updated_at changes only
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (!empty($changes)) {
            $description = 'Updated ' . class_basename($model) . ' (ID: ' . $model->getKey() . ')';
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

        $this->logActivity($model, $action, 'Deleted ' . class_basename($model) . ' (ID: ' . $model->getKey() . ')');
    }

    public function restored(Model $model)
    {
        $this->logActivity($model, 'Restore', 'Restored ' . class_basename($model) . ' (ID: ' . $model->getKey() . ')');
    }

    public function forceDeleted(Model $model)
    {
        $this->logActivity($model, 'Force Delete', 'Permanently deleted ' . class_basename($model) . ' (ID: ' . $model->getKey() . ')');
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
