<?php

namespace App\Http\Controllers;

use App\Models\ProjectUpdate;
use Illuminate\Http\Request;

class ProjectUpdateController extends Controller
{
    public function store(Request $request, int $projectID)
    {
        $validated = $request->validate([
            'update_title' => ['required', 'string', 'max:200'],
            'update_description' => ['required', 'string'],
            'milestone_achieved' => ['nullable', 'string', 'max:200'],
            'progress_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        ProjectUpdate::create([
            'projectID' => $projectID,
            'update_title' => $validated['update_title'],
            'update_description' => $validated['update_description'],
            'milestone_achieved' => $validated['milestone_achieved'] ?? null,
            'progress_percentage' => $validated['progress_percentage'] ?? 0,
            'update_date' => now(),
            'updated_by' => auth()->user()->userID,
        ]);

        return redirect()->back();
    }

    public function destroy(int $projectID, int $updateID)
    {
        ProjectUpdate::where('projectID', $projectID)
            ->where('updateID', $updateID)
            ->delete();

        return redirect()->back();
    }
}
