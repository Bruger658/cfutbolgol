<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Support\UserRole;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'theme_preference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Get the user's initials
     */

     public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasRole(string ...$roles): bool
    {
        $currentRole = $this->roleModel?->slug ?? $this->role;

        return in_array($currentRole, $roles, true);
    }

    public function hasPermission(string $permission): bool
    {
        if ($permission === 'manage-users') {
            return $this->hasRole(UserRole::ADMIN);
        }

         if ($this->hasRole(UserRole::ADMIN)) {
            return true;
        }

        if ($this->roleModel) {
            $this->roleModel->loadMissing('permissions');

            return $this->roleModel->hasPermission($permission);
        }

        $roles = UserRole::PERMISSIONS[$permission] ?? [];

       return in_array($this->role, $roles, true);
    }

    public function roleLabel(): string
    {
        return $this->roleModel?->name ?? UserRole::LABELS[$this->role] ?? ucfirst((string) $this->role);
    }

    
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
