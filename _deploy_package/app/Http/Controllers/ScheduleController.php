<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Store a newly created schedule
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'scheduled_date' => 'required|date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'priority' => 'required|in:high,medium,low',
                'visibility' => 'required|in:everyone,administrator,principal,teachers,parents,supporting_staff,faculty',
            ]);

            $schedule = Schedule::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'scheduled_date' => $validated['scheduled_date'],
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'priority' => $validated['priority'],
                'visibility' => $validated['visibility'],
                'created_by' => Auth::id(),
                'is_active' => true,
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Schedule created successfully!',
                    'schedule' => $schedule
                ]);
            }

            return redirect()->route('administrator.announcements')
                ->with('success', 'Schedule created successfully!');

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
    }
}
