<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['username', 'full_name', 'name', 'email', 'password', 'status', 'bio', 'photo', 'department_id', 'position_id', 'nik', 'join_year', 'date_of_birth', 'phone', 'address', 'gender', 'allowed_modules', 'site_id', 'created_by', 'updated_by'])]
#[Hidden(['password', 'remember_token', 'deleted_at'])]
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasUuids;
    use SoftDeletes;

    public function getFilamentName(): string
    {
        return $this->full_name ?? $this->username ?? 'Admin User';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function assignedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'assigned_to_id');
    }

    public function requestedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'requester_id');
    }

    public function mechanicWorkOrders()
    {
        return $this->belongsToMany(WorkOrder::class, 'work_order_mechanics')
            ->withPivot('hours_spent')
            ->withTimestamps();
    }

    public function getAllPermissions(): Collection
    {
        return $this->load('roles.permissions')
            ->roles
            ->flatMap(fn ($role) => $role->permissions)
            ->unique('id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->getAllPermissions()->contains('name', $permission);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function getPermissionsByCategory(): Collection
    {
        return $this->getAllPermissions()->groupBy('category')->sortKeys();
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the site ID for data filtering. Returns null if 'all' or no site assigned.
     */
    public function getSiteFilterId(): ?string
    {
        return $this->site_id;
    }

    /**
     * Check if this user is restricted to a specific site.
     */
    public function isSiteRestricted(): bool
    {
        return filled($this->site_id);
    }

    public function department()
    {
        return $this->belongsTo(ReffUser::class, 'department_id')->where('type', 'department');
    }

    public function position()
    {
        return $this->belongsTo(ReffUser::class, 'position_id')->where('type', 'position');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'allowed_modules' => 'array',
        ];
    }
}
