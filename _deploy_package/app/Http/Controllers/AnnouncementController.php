<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter');

        $query = Announcement::with('creator')
            ->active()
            ->published();

        // Role-based filtering
        if ($user->user_type === 'parent') {
            $query->forAudience('parents');
        } elseif ($user->user_type === 'teacher') {
            $query->forAudience('teachers');
        } elseif ($user->user_type === 'administrator') {
            $query->forAudience('administrator');
        } elseif ($user->user_type === 'principal') {
            $query->forAudience('principal');
        } else {
            $query->forAudience('everyone');
        }

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
            ->forRole($user->user_type)
            ->orderBy('scheduled_date', 'asc')
            ->limit(3)
            ->get();

        $view = $user->user_type === 'principal'
            ? 'principal.announcements.index'
            : 'administrator.announcements.index';

        return view($view, compact('announcements', 'upcomingSchedules'));
    }

    /**
     * Show the form for creating a new announcement
     */
    public function create()
    {
        return view('administrator.announcements.create');
    }

    /**
     * Store a newly created announcement
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|in:important,notice,update,event',
                'audience' => 'required|in:everyone,parents,teachers,administrator,principal,supporting_staff,faculty',
                'published_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after:published_at',
            ]);

            $announcement = Announcement::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'audience' => $validated['audience'],
                'created_by' => Auth::id(),
                'published_at' => $validated['published_at'] ?? now(),
                'expires_at' => $validated['expires_at'] ?? null,
                'is_active' => true,
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement published successfully!',
                    'announcement' => $announcement
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }

        return redirect()->route('administrator.announcements')
            ->with('success', 'Announcement created successfully!');
    }

    /**
     * Show the form for editing an announcement
     */
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('administrator.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:important,notice,update,event',
            'audience' => 'required|in:everyone,parents,teachers,administrator,principal,supporting_staff,faculty',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_active' => 'boolean',
        ]);

        $announcement->update($validated);

        return redirect()->route('administrator.announcements')
            ->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified announcement
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('administrator.announcements')
            ->with('success', 'Announcement deleted successfully!');
    }
}
