<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Schedule;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    /**
     * Display the parent dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get parent profile with children
        $parentProfile = \App\Models\ParentProfile::where('userID', $user->userID)->first();

        // Get children through relationship
        $children = collect();
        if ($parentProfile) {
            $children = \Illuminate\Support\Facades\DB::table('parent_student_relationships as psr')
                ->join('students as s', 'psr.studentID', '=', 's.studentID')
                ->where('psr.parentID', $parentProfile->parentID)
                ->where('s.enrollment_status', 'active')
                ->select('s.*')
                ->get();
        }

        // Get recent announcements (prioritize important, then 3 most recent)
        $recentAnnouncements = Announcement::with('creator')
            ->active()
            ->published()
            ->forAudience('parents')
            ->orderByRaw("CASE WHEN category = 'important' THEN 0 ELSE 1 END")
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        // Get upcoming schedules (exclusive to parent role)
        $upcomingSchedules = Schedule::active()
            ->upcoming()
            ->forRole('parent')
            ->orderBy('scheduled_date', 'asc')
            ->limit(3)
            ->get();

        // Dashboard statistics
        $stats = [
            'childrenCount' => $children->count(),
            'upcomingEvents' => Schedule::active()
                ->upcoming()
                ->forRole('parent')
                ->whereMonth('scheduled_date', now()->month)
                ->whereYear('scheduled_date', now()->year)
                ->count(),
        ];

        return view('parent.dashboard', compact('recentAnnouncements', 'upcomingSchedules', 'stats', 'children'));
    }

    /**
     * Display the parent announcements page
     */
    public function announcements(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter');

        $query = Announcement::with('creator')
            ->active()
            ->published()
            ->forAudience('parents');

        // Date filtering
        if ($filter === 'Today') {
            $query->whereDate('created_at', today());
        } elseif ($filter === 'This Week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filter === 'This Month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        // Prioritize important announcements
        $announcements = $query
            ->orderByRaw("CASE WHEN category = 'important' THEN 0 ELSE 1 END")
            ->orderBy('published_at', 'desc')
            ->get();

        // Get upcoming schedules for the user
        $upcomingSchedules = Schedule::active()
            ->upcoming()
            ->forRole('parent')
            ->orderBy('scheduled_date', 'asc')
            ->limit(3)
            ->get();

        return view('parent.announcement.index', compact('announcements', 'upcomingSchedules'));
    }
}
