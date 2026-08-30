<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends BaseModel
{
    use SoftDeletes;

    protected $table = 'equipments';

    protected $fillable = [
        'unit',
        'no',
        'status',
        'reff_equip_id',
        'sn_unit',
        'engine_model',
        'sn_engine',
        'eqp_capacity',
        'no_police',
        'attachment',
        'hp_kw',
        'year_build',
        'date_receive',
        'site_id',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function reffEquip()
    {
        return $this->belongsTo(ReffEquip::class, 'reff_equip_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'equipment_id');
    }

    public function latestWorkOrder()
    {
        return $this->hasOne(WorkOrder::class, 'equipment_id')->latestOfMany();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getNameAttribute(): ?string
    {
        return $this->unit ?? $this->no ?? 'Equipment #'.$this->id;
    }

    public function hmLogs()
    {
        return $this->hasMany(EquipmentHm::class, 'equipment_id')->orderBy('date', 'desc');
    }

    public function latestHmLog()
    {
        return $this->hasOne(EquipmentHm::class, 'equipment_id')->latestOfMany();
    }

    public function getHmAtDate($date)
    {
        $log = $this->hmLogs()->whereDate('date', $date)->first();

        return $log ? $log->hm_value : null;
    }

    public function getLastHmBeforeDate($date)
    {
        $log = $this->hmLogs()->whereDate('date', '<=', $date)->first();

        return $log ? $log->hm_value : 0;
    }
}
