<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    /**
     * Store a new milestone for a project.
     */
    public function store(Request $request, int $projectID)
    {
        $project = Project::findOrFail($projectID);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'target_date' => [
                'nullable',
                'date',
                'after_or_equal:' . optional($project->start_date)->format('Y-m-d'),
                'before_or_equal:' . optional($project->target_completion_date)->format('Y-m-d'),
            ],
        ], [
            'target_date.after_or_equal' => 'Milestone target date must be on or after the project start date (' . optional($project->start_date)->format('M d, Y') . ').',
            'target_date.before_or_equal' => 'Milestone target date must be on or before the project completion date (' . optional($project->target_completion_date)->format('M d, Y') . ').',
        ]);

        $maxOrder = Milestone::where('projectID', $projectID)->max('sort_order') ?? 0;

        Milestone::create([
            'projectID' => $projectID,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'is_completed' => false,
            'sort_order' => $maxOrder + 1,
            'created_by' => auth()->user()->userID,
        ]);

        return redirect()->back()->with('success', 'Milestone added successfully.');
    }

    /**
     * Toggle milestone completion status.
     */
    public function toggle(int $projectID, int $milestoneID)
    {
        $milestone = Milestone::where('projectID', $projectID)
            ->where('milestoneID', $milestoneID)
            ->firstOrFail();

        $milestone->is_completed = !$milestone->is_completed;
        $milestone->completed_date = $milestone->is_completed ? now()->toDateString() : null;
        $milestone->save();

        return redirect()->back()->with('success', 'Milestone updated.');
    }

    /**
     * Delete a milestone.
     */
    public function destroy(int $projectID, int $milestoneID)
    {
        Milestone::where('projectID', $projectID)
            ->where('milestoneID', $milestoneID)
            ->delete();

        return redirect()->back()->with('success', 'Milestone deleted.');
    }
}
