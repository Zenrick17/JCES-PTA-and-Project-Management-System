<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectContribution extends Model
{
    protected $table = 'project_contributions';
    protected $primaryKey = 'contributionID';
    public $timestamps = false;

    protected $fillable = [
        'projectID',
        'parentID',
        'contribution_amount',
        'payment_method',
        'payment_status',
        'contribution_date',
        'receipt_number',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'contribution_amount' => 'decimal:2',
        'contribution_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectID', 'projectID');
    }

    public function parent()
    {
        return $this->belongsTo(ParentProfile::class, 'parentID', 'parentID');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'userID');
    }

    public function transaction()
    {
        return $this->hasOne(PaymentTransaction::class, 'contributionID', 'contributionID');
    }
}
