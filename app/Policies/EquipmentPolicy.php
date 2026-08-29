<?php

namespace App\Policies;

use App\Models\Equipment;
use App\Models\User;

class EquipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view equipment');
    }

    public function view(User $user, Equipment $equipment): bool
    {
        return $user->hasPermission('view equipment');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create equipment');
    }

    public function update(User $user, Equipment $equipment): bool
    {
        return $user->hasPermission('edit equipment');
    }

    public function delete(User $user, Equipment $equipment): bool
    {
        return $user->hasPermission('delete equipment');
    }

    public function restore(User $user, Equipment $equipment): bool
    {
        return $user->hasPermission('edit equipment');
    }

    public function forceDelete(User $user, Equipment $equipment): bool
    {
        return $user->hasPermission('delete equipment');
    }
}
