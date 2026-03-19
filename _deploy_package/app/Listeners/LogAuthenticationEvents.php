<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Models\SecurityAuditLog;
use App\Models\User;

class LogAuthenticationEvents
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;
        
        SecurityAuditLog::logActivity(
            $user->userID,
            'login',
            'users',
            $user->userID,
            null,
            null,
            true,
            null
        );
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            /** @var User $user */
            $user = $event->user;
            
            SecurityAuditLog::logActivity(
                $user->userID,
                'logout',
                'users',
                $user->userID,
                null,
                null,
                true,
                null
            );
        }
    }

    /**
     * Handle failed login attempts.
     */
    public function handleFailedLogin(Failed $event): void
    {
        // Try to find user by email for failed attempts
        $user = \App\Models\User::where('email', $event->credentials['email'] ?? '')->first();
        
        SecurityAuditLog::logActivity(
            $user?->userID,
            'failed_login',
            'users',
            $user?->userID,
            null,
            ['attempted_email' => $event->credentials['email'] ?? 'unknown'],
            false,
            'Invalid credentials provided'
        );
    }
}
