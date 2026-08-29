<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view user');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('view user');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create user');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('edit user');
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->hasRole('admin')) {
            return false; // Prevent deleting admin
        }

        return $user->hasPermission('delete user');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermission('edit user');
    }

    public function forceDelete(User $user, User $model): bool
    {
        if ($model->hasRole('admin')) {
            return false;
        }

        return $user->hasPermission('delete user');
    }
}
