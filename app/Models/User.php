<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saved(function (self $user) {
            $user->syncParentProfile();
        });
    }

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'userID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_date';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'plain_password',
        'user_type',
        'first_name',
        'last_name',
        'phone',
        'address',
        'is_active',
        'is_archived',
        'failed_login_attempts',
        'account_locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
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
            'created_date' => 'datetime',
            'last_login' => 'datetime',
            'password_changed_date' => 'datetime',
            'account_locked_until' => 'datetime',
            'is_active' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the name attribute (for compatibility)
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';

        if (empty($firstName) && empty($lastName)) {
            return $this->username ?? 'Unknown User';
        }

        return trim($firstName . ' ' . $lastName);
    }

    /**
     * Get id attribute for compatibility with Laravel's default scaffolding.
     *
     * @return int|null
     */
    public function getIdAttribute()
    {
        return $this->attributes['userID'] ?? null;
    }

    /**
     * Get password attribute for compatibility with Laravel's default scaffolding.
     *
     * @return string|null
     */
    public function getPasswordAttribute()
    {
        return $this->attributes['password_hash'] ?? null;
    }

    /**
     * Set name attribute by splitting into first and last names.
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $parts = preg_split('/\s+/', trim((string) $value), 2);
        $this->attributes['first_name'] = $parts[0] ?? '';
        $this->attributes['last_name'] = $parts[1] ?? '';
    }

    /**
     * Set the password attribute (for compatibility)
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = bcrypt($value);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to exclude archived users.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Check if user is an administrator
     */
    public function isAdministrator()
    {
        return $this->user_type === 'administrator';
    }

    /**
     * Check if user is a principal
     */
    public function isPrincipal()
    {
        return $this->user_type === 'principal';
    }

    /**
     * Check if user is a teacher
     */
    public function isTeacher()
    {
        return $this->user_type === 'teacher';
    }

    /**
     * Check if user is a parent
     */
    public function isParent()
    {
        return $this->user_type === 'parent';
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update([
            'last_login' => now(),
            'failed_login_attempts' => 0
        ]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedLoginAttempts()
    {
        $updateData = [
            'failed_login_attempts' => $this->failed_login_attempts + 1
        ];

        // Lock account after 5 failed attempts for 30 minutes
        if ($this->failed_login_attempts >= 4) { // >= 4 because we're incrementing
            $updateData['account_locked_until'] = now()->addMinutes(30);
        }

        $this->update($updateData);
        $this->refresh(); // Refresh the model to get updated values
    }

    /**
     * Check if account is locked
     */
    public function isLocked()
    {
        if ($this->account_locked_until && $this->account_locked_until->isFuture()) {
            return true;
        }

        // Reset lock if time has passed
        if ($this->account_locked_until && $this->account_locked_until->isPast()) {
            $this->update([
                'account_locked_until' => null,
                'failed_login_attempts' => 0
            ]);
            $this->refresh();
        }

        return false;
    }

    /**
     * Relationship with parent profile
     */
    public function parentProfile()
    {
        return $this->hasOne(ParentProfile::class, 'userID', 'userID');
    }

    /**
     * Relationship with role assignments
     */
    public function roleAssignments()
    {
        return $this->hasMany(UserRoleAssignment::class, 'userID', 'userID');
    }

    /**
     * Relationship with audit logs
     */
    public function auditLogs()
    {
        return $this->hasMany(SecurityAuditLog::class, 'userID', 'userID');
    }

    /**
     * Relationship with sessions
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'userID', 'userID');
    }

    public function syncParentProfile(): void
    {
        if ($this->user_type !== 'parent' || !Schema::hasTable('parents')) {
            return;
        }

        $parentProfile = ParentProfile::query()
            ->where('userID', $this->userID)
            ->orWhere('email', $this->email)
            ->first();

        $profileData = [
            'userID' => $this->userID,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone ?: '0000000000',
            'password_hash' => $this->password_hash,
            'account_status' => $this->is_active ? 'active' : 'inactive',
        ];

        if ($parentProfile) {
            $parentProfile->fill($profileData);
            $parentProfile->save();

            return;
        }

        ParentProfile::create($profileData);
    }
}
