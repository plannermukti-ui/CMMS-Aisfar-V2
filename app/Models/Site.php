<?php

namespace App\Models;

class Site extends BaseModel
{
    protected $fillable = [
        'site_code',
        'site_name',
        'address',
        'remarks',
    ];
}
