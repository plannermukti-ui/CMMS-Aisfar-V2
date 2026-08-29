<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ReffComponent extends BaseModel
{
    use SoftDeletes;

    protected $table = 'reff_components';

    protected $fillable = [
        'code',
        'name',
        'category',
        'equipment_types',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'equipment_types' => 'array',
        'sort_order' => 'integer',
    ];

    public function workOrderTasks()
    {
        return $this->hasMany(WorkOrderTask::class, 'reff_component_id');
    }

    /**
     * Scope query to components applicable for a given equipment type.
     */
    public function scopeForEquipmentType($query, ?string $equipmentType)
    {
        if (! $equipmentType) {
            return $query;
        }

        return $query->where(function ($q) use ($equipmentType) {
            $q->whereNull('equipment_types')
                ->orWhereJsonContains('equipment_types', $equipmentType)
                ->orWhereJsonContains('equipment_types', 'All');
        });
    }
}
