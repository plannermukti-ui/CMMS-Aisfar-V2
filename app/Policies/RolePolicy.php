<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view role');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('view role');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create role');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermission('edit role');
    }

    public function delete(User $user, Role $role): bool
    {
        // Prevent deleting admin role
        if ($role->name === 'admin') {
            return false;
        }

        return $user->hasPermission('delete role');
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->hasPermission('edit role');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        if ($role->name === 'admin') {
            return false;
        }

        return $user->hasPermission('delete role');
    }
}
