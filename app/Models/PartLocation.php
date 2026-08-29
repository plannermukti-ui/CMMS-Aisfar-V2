<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartLocation extends BaseModel
{
    protected $table = 'part_locations';

    protected $fillable = [
        'part_id',
        'site_id',
        'warehouse_name',
        'rack_location',
        'stock_qty',
        'is_primary',
        'notes',
    ];

    protected $casts = [
        'stock_qty' => 'decimal:2',
        'is_primary' => 'boolean',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function getFullLocationLabelAttribute(): string
    {
        $siteName = $this->site ? $this->site->site_name : 'Gudang Utama';
        $warehouse = $this->warehouse_name ?: 'Gudang';

        return "{$siteName} ({$warehouse}) - {$this->rack_location}";
    }
}
