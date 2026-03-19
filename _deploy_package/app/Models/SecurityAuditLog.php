<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAuditLog extends Model
{
    use HasFactory;

    protected $table = 'security_audit_log';
    protected $primaryKey = 'logID';
    
    public $timestamps = false; // We use custom timestamp column

    protected $fillable = [
        'userID',
        'action',
        'table_affected',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id',
        'timestamp',
        'success',
        'error_message'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'timestamp' => 'datetime',
        'success' => 'boolean'
    ];

    /**
     * Get the name of the "created at" column.
     */
    public function getCreatedAtColumn()
    {
        return 'timestamp';
    }

    /**
     * Get the name of the "updated at" column.
     */
    public function getUpdatedAtColumn()
    {
        return null; // No updated_at column
    }

    /**
     * Get the user that performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    /**
     * Log a user activity
     */
    public static function logActivity(
        ?int $userId,
        string $action,
        ?string $tableAffected = null,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        bool $success = true,
        ?string $errorMessage = null
    ): void {
        self::create([
            'userID' => $userId,
            'action' => $action,
            'table_affected' => $tableAffected,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'timestamp' => now(),
            'success' => $success,
            'error_message' => $errorMessage
        ]);
    }

    /**
     * Get activity logs with filters
     */
    public static function getActivityLogs(array $filters = [], int $perPage = 15)
    {
        $query = self::with('user')
            ->orderBy('timestamp', 'desc');

        // Apply filters
        if (!empty($filters['user_id'])) {
            $query->where('userID', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', 'like', '%' . $filters['action'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('timestamp', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('timestamp', '<=', $filters['date_to']);
        }

        if (!empty($filters['ip_address'])) {
            $query->where('ip_address', 'like', '%' . $filters['ip_address'] . '%');
        }

        if (isset($filters['success']) && $filters['success'] !== '') {
            $query->where('success', $filters['success'] === 'true');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get action statistics
     */
    public static function getActionStats(int $days = 30): array
    {
        return self::where('timestamp', '>=', now()->subDays($days))
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'action')
            ->toArray();
    }

    /**
     * Get user activity statistics
     */
    public static function getUserActivityStats(int $days = 30): array
    {
        return self::with('user')
            ->where('timestamp', '>=', now()->subDays($days))
            ->selectRaw('userID, COUNT(*) as count')
            ->groupBy('userID')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'user' => $log->user ? $log->user->first_name . ' ' . $log->user->last_name : 'Unknown User',
                    'count' => $log->count
                ];
            })
            ->toArray();
    }
}
