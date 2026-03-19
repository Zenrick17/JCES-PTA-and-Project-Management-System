<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardMetric extends Model
{
    protected $table = 'dashboard_metrics';
    protected $primaryKey = 'metricID';
    public $timestamps = false;

    protected $fillable = [
        'metric_name',
        'metric_category',
        'current_value',
        'target_value',
        'unit_of_measure',
        'calculation_method',
        'last_updated',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'current_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'last_updated' => 'datetime',
    ];
}
