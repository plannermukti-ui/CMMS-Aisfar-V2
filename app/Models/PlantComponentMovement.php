<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantComponentMovement extends BaseModel
{
    protected $table = 'plant_component_movements';

    protected $fillable = [
        'component_id',
        'from_equipment_id',
        'to_equipment_id',
        'movement_type',
        'movement_date',
        'equipment_hm',
        'component_hours_at_movement',
        'performed_by',
        'work_order_id',
        'notes',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'equipment_hm' => 'decimal:2',
        'component_hours_at_movement' => 'decimal:2',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(PlantComponent::class, 'component_id');
    }

    public function fromEquipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'from_equipment_id');
    }

    public function toEquipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'to_equipment_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
