<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $table = 'project_milestones';
    protected $primaryKey = 'milestoneID';

    protected $fillable = [
        'projectID',
        'title',
        'description',
        'target_date',
        'completed_date',
        'is_completed',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_date' => 'date',
        'is_completed' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectID', 'projectID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'userID');
    }
}
