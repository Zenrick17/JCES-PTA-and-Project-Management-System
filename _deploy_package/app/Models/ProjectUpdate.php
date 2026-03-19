<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUpdate extends Model
{
    protected $table = 'project_updates';
    protected $primaryKey = 'updateID';
    public $timestamps = false;

    protected $fillable = [
        'projectID',
        'update_title',
        'update_description',
        'milestone_achieved',
        'progress_percentage',
        'update_date',
        'updated_by',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'update_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectID', 'projectID');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'userID');
    }
}
