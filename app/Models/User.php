<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @mixin \Eloquent
 */
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
        'profile_picture',
        'password',
        'role',
        'permissions',
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
            'permissions' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Admins have absolute power
        if ($this->role === 'admin') {
            return true;
        }

        // Coordinators have all powers except Account Management
        if ($this->role === 'coordinator') {
            return $permission !== 'manage_accounts';
        }

        $perms = $this->permissions ?? [];
        if (in_array($permission, $perms)) {
            return true;
        }

        // Logical grouping for Masterlist (Requested by USER)
        // If they have edit_masterlist, they can do EVERYTHING in masterlist
        if (in_array('edit_masterlist', $perms)) {
            $masterlistPool = [
                'view_masterlist', 
                'edit_masterlist', 
                'manage_documents', 
                'export_masterlist', 
                'change_status',
                'add_employee'
            ];
            if (in_array($permission, $masterlistPool)) return true;
        }

        // Logical grouping for Archive (Requested by USER)
        if (in_array('edit_archive', $perms)) {
            $archivePool = [
                'view_archive', 
                'edit_archive', 
                'manage_documents',
                'export_archive'
            ];
            if (in_array($permission, $archivePool)) return true;
        }

        // Intelligent fallback: if you can edit a module, you can naturally view it
        if (str_starts_with($permission, 'view_')) {
            $module = str_replace('view_', '', $permission);
            if (in_array('edit_' . $module, $perms)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
