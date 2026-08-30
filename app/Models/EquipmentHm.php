<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentHm extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'equipment_id',
        'date',
        'hm_value',
        'is_interpolated',
        'source',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'hm_value' => 'integer',
        'is_interpolated' => 'boolean',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
