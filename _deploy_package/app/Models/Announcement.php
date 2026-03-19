<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';
    protected $primaryKey = 'announcementID';

    protected $fillable = [
        'title',
        'content',
        'category',
        'audience',
        'created_by',
        'published_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created the announcement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'userID');
    }

    /**
     * Scope to get only active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope to get announcements by audience
     * Handles group audiences:
     * - supporting_staff: administrator + principal
     * - faculty: principal + teachers + administrator
     */
    public function scopeForAudience($query, $audience)
    {
        // Map singular role names to plural for database compatibility
        $audienceMap = [
            'parent' => 'parents',
            'teacher' => 'teachers',
        ];

        $dbAudience = $audienceMap[$audience] ?? $audience;

        return $query->where(function($q) use ($dbAudience, $audience) {
            // Match both singular and plural forms for compatibility
            $q->where('audience', $dbAudience)
              ->orWhere('audience', $audience)
              ->orWhere('audience', 'everyone');

            // Handle group audience memberships
            if (in_array($audience, ['administrator', 'principal'])) {
                $q->orWhere('audience', 'supporting_staff')
                  ->orWhere('audience', 'faculty');
            } elseif (in_array($audience, ['teacher', 'teachers'])) {
                $q->orWhere('audience', 'faculty');
            }
        });
    }

    /**
     * Scope to get published announcements
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeCreatedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get category badge color
     */
    public function getCategoryColorAttribute()
    {
        return match($this->category) {
            'important' => 'green',
            'notice' => 'orange',
            'update' => 'blue',
            'event' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
