<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';
    protected $primaryKey = 'paymentID';
    public $timestamps = false;

    protected $fillable = [
        'parentID',
        'projectID',
        'contributionID',
        'amount',
        'payment_method',
        'transaction_status',
        'transaction_date',
        'receipt_number',
        'reference_number',
        'processed_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(ParentProfile::class, 'parentID', 'parentID');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectID', 'projectID');
    }

    public function contribution()
    {
        return $this->belongsTo(ProjectContribution::class, 'contributionID', 'contributionID');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'userID');
    }
}
