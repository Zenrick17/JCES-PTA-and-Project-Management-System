<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'projectID';
    public $timestamps = false;

    protected $fillable = [
        'project_name',
        'description',
        'goals',
        'target_budget',
        'current_amount',
        'start_date',
        'target_completion_date',
        'actual_completion_date',
        'project_status',
        'created_by',
        'created_date',
        'updated_date',
    ];

    protected $casts = [
        'target_budget' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function contributions()
    {
        return $this->hasMany(ProjectContribution::class, 'projectID', 'projectID');
    }

    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class, 'projectID', 'projectID');
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'projectID', 'projectID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'userID');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'projectID', 'projectID')->orderBy('sort_order');
    }

    /**
     * Get the latest progress percentage from updates.
     */
    public function getLatestProgressAttribute(): float
    {
        $latest = $this->updates()->orderBy('update_date', 'desc')->first();
        return $latest ? (float) $latest->progress_percentage : 0;
    }

    /**
     * Get milestone completion percentage.
     */
    public function getMilestoneProgressAttribute(): float
    {
        $total = $this->milestones()->count();
        if ($total === 0) return 0;
        $completed = $this->milestones()->where('is_completed', true)->count();
        return round(($completed / $total) * 100, 1);
    }

    /**
     * Get fund progress percentage.
     */
    public function getFundProgressAttribute(): float
    {
        if ($this->target_budget <= 0) return 0;
        return round(min(($this->current_amount / $this->target_budget) * 100, 100), 1);
    }
}
