<?php

namespace App\Models;

class Permission extends BaseModel
{
    protected $fillable = ['name', 'display_name', 'category', 'action', 'description', 'route_name'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
