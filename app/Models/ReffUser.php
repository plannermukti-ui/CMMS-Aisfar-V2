<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReffUser extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'code',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];
}
