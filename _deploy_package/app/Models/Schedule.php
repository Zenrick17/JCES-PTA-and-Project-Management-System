<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $primaryKey = 'scheduleID';

    protected $fillable = [
        'title',
        'description',
        'scheduled_date',
        'start_time',
        'end_time',
        'category',
        'priority',
        'visibility',
        'created_by',
        'is_active',
        'is_completed',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'is_active' => 'boolean',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the user who created the schedule
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'userID');
    }

    /**
     * Scope to get only active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get upcoming schedules
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now())
                    ->where('is_completed', false);
    }

    /**
     * Scope to get recent/past schedules
     */
    public function scopeRecent($query)
    {
        return $query->where('scheduled_date', '<', now())
                    ->orWhere('is_completed', true);
    }

    /**
     * Scope to filter by visibility/role
     * Handles group audiences:
     * - supporting_staff: administrator + principal
     * - faculty: principal + teachers + administrator
     */
    public function scopeForRole($query, $role)
    {
        // Map singular role names to plural for database compatibility
        $roleMap = [
            'parent' => 'parents',
            'teacher' => 'teachers',
        ];

        $dbRole = $roleMap[$role] ?? $role;

        return $query->where(function($q) use ($dbRole, $role) {
            // Match both singular and plural forms for compatibility
            $q->where('visibility', $dbRole)
              ->orWhere('visibility', $role)
              ->orWhere('visibility', 'everyone');

            // Handle group audience memberships
            if (in_array($role, ['administrator', 'principal'])) {
                $q->orWhere('visibility', 'supporting_staff')
                  ->orWhere('visibility', 'faculty');
            } elseif (in_array($role, ['teacher', 'teachers'])) {
                $q->orWhere('visibility', 'faculty');
            }
        });
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Get category badge color
     */
    public function getCategoryColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'purple',
            'low' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->scheduled_date->format('M j');
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return Carbon::parse($this->start_time)->format('g:i A') . ' – ' . Carbon::parse($this->end_time)->format('g:i A');
        }
        return null;
    }

    /**
     * Get time ago for recent items
     */
    public function getTimeAgoAttribute()
    {
        return $this->scheduled_date->diffForHumans();
    }
}
