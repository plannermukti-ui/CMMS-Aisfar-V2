<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends BaseModel
{
    protected $fillable = [
        'user_id', 'action', 'module', 'model_id', 'model_type',
        'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'json',
            'new_values' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function log(string $action, string $module, Model $model, ?array $oldValues = null)
    {
        self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'model_id' => $model->id,
            'model_type' => $model::class,
            'old_values' => $oldValues,
            'new_values' => $model->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
