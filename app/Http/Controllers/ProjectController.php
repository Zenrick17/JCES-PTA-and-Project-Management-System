<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Milestone;
use App\Models\ProjectContribution;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    private function resolveProjectsView(string $view): string
    {
        $user = Auth::user();

        if ($user && $user->user_type === 'administrator') {
            return "administrator.projects.$view";
        }

        if ($user && $user->user_type === 'teacher') {
            return "teacher.projects.$view";
        }

        return "principal.projects.$view";
    }

    private function resolveProjectsRoute(string $route): string
    {
        $user = Auth::user();

        if ($user && $user->user_type === 'administrator') {
            return "administrator.projects.$route";
        }

        if ($user && $user->user_type === 'teacher') {
            return "teacher.projects.$route";
        }

        return "principal.projects.$route";
    }

    /**
     * Check if current user can create projects (Principal only)
     */
    private function canCreateProject(): bool
    {
        $user = Auth::user();
        return $user && $user->user_type === 'principal';
    }

    /**
     * Check if current user can manage projects (Administrator/Teacher)
     */
    private function canManageProject(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->user_type, ['administrator', 'teacher']);
    }

    /**
     * Check if current user can approve project closure (Principal only)
     */
    private function canApproveClosure(): bool
    {
        $user = Auth::user();
        return $user && $user->user_type === 'principal';
    }

    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $projectsQuery = Project::query();

        if (!empty($status)) {
            $projectsQuery->where('project_status', $status);
        }

        if (!empty($search)) {
            $projectsQuery->where('project_name', 'like', '%' . $search . '%');
        }

        $projects = $projectsQuery
            ->orderBy('created_date', 'desc')
            ->paginate(15)
            ->appends($request->all());

        $statusOptions = ['created', 'active', 'in_progress', 'completed', 'archived', 'cancelled'];

        return view($this->resolveProjectsView('index'), compact('projects', 'status', 'search', 'statusOptions'));
    }

    public function create()
    {
        // Only Principal can create projects
        if (!$this->canCreateProject()) {
            abort(403, 'Only the Principal can create new projects.');
        }

        $statusOptions = ['created', 'active', 'in_progress', 'completed', 'archived', 'cancelled'];

        return view($this->resolveProjectsView('create'), compact('statusOptions'));
    }

    public function store(Request $request)
    {
        // Only Principal can create projects
        if (!$this->canCreateProject()) {
            abort(403, 'Only the Principal can create new projects.');
        }

        $validated = $request->validate([
            'project_name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'goals' => ['nullable', 'string'],
            'title' => ['nullable', 'string', 'max:200'],
            'venue' => ['nullable', 'string', 'max:200'],
            'time' => ['nullable', 'string', 'max:100'],
            'objective' => ['nullable', 'string'],
            'project_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'target_budget' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'target_completion_date' => ['required', 'date', 'after_or_equal:start_date'],
            'project_status' => ['nullable', 'in:created,active,in_progress,completed,archived,cancelled'],
        ]);

        $photoPath = null;
        if ($request->hasFile('project_photo')) {
            $filename = 'project_' . now()->format('Ymd_His') . '_' . uniqid() . '.' . $request->file('project_photo')->getClientOriginalExtension();
            $storedPath = $request->file('project_photo')->storeAs('projects', $filename, 'public');
            $photoPath = Storage::url($storedPath);
        }

        $description = $validated['description'] ?? null;
        if (empty($description)) {
            $parts = [];
            if (!empty($validated['title'])) {
                $parts[] = 'Title: ' . $validated['title'];
            }
            if (!empty($validated['venue'])) {
                $parts[] = 'Venue: ' . $validated['venue'];
            }
            if (!empty($validated['time'])) {
                $parts[] = 'Time: ' . $validated['time'];
            }
            if (!empty($photoPath)) {
                $parts[] = 'Photo: ' . $photoPath;
            }
            $description = !empty($parts) ? implode("\n", $parts) : 'Project details pending.';
        }

        $goals = $validated['goals'] ?? null;
        if (empty($goals)) {
            $goals = $validated['objective'] ?? 'Objectives pending.';
        }

        $project = Project::create([
            'project_name' => $validated['project_name'],
            'description' => $description,
            'goals' => $goals,
            'target_budget' => $validated['target_budget'],
            'current_amount' => 0,
            'start_date' => $validated['start_date'],
            'target_completion_date' => $validated['target_completion_date'],
            'project_status' => $validated['project_status'] ?? 'created',
            'created_by' => Auth::user()->userID,
            'created_date' => now(),
            'updated_date' => now(),
        ]);

        return redirect()
            ->route($this->resolveProjectsRoute('show'), $project->projectID)
            ->with('success', 'Project created successfully.');
    }

    public function show(int $projectID)
    {
        $project = Project::with(['contributions.parent', 'milestones'])
            ->where('projectID', $projectID)
            ->firstOrFail();

        $updates = ProjectUpdate::where('projectID', $projectID)
            ->orderBy('update_date', 'desc')
            ->get();

        $contributions = ProjectContribution::with(['parent', 'processedBy'])
            ->where('projectID', $projectID)
            ->orderBy('contribution_date', 'desc')
            ->paginate(10);

        $milestones = Milestone::where('projectID', $projectID)
            ->orderBy('sort_order')
            ->get();

        // Calculate progress metrics
        $latestProgress = $updates->first() ? (float) $updates->first()->progress_percentage : 0;
        $fundProgress = $project->target_budget > 0
            ? round(min(($project->current_amount / $project->target_budget) * 100, 100), 1)
            : 0;
        $totalMilestones = $milestones->count();
        $completedMilestones = $milestones->where('is_completed', true)->count();
        $milestoneProgress = $totalMilestones > 0
            ? round(($completedMilestones / $totalMilestones) * 100, 1)
            : 0;

        return view($this->resolveProjectsView('show'), compact(
            'project', 'updates', 'contributions', 'milestones',
            'latestProgress', 'fundProgress', 'milestoneProgress',
            'totalMilestones', 'completedMilestones'
        ));
    }

    public function edit(int $projectID)
    {
        abort_unless(
            in_array(Auth::user()?->user_type, ['principal', 'administrator', 'teacher'], true),
            403,
            'Unauthorized action.'
        );

        $project = Project::where('projectID', $projectID)->firstOrFail();
        $statusOptions = ['created', 'active', 'in_progress', 'completed', 'archived', 'cancelled'];

        return view($this->resolveProjectsView('edit'), compact('project', 'statusOptions'));
    }

    public function update(Request $request, int $projectID)
    {
        abort_unless(
            in_array(Auth::user()?->user_type, ['principal', 'administrator', 'teacher'], true),
            403,
            'Unauthorized action.'
        );

        $validated = $request->validate([
            'project_name' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'goals' => ['required', 'string'],
            'target_budget' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'target_completion_date' => ['required', 'date', 'after_or_equal:start_date'],
            'project_status' => ['required', 'in:created,active,in_progress,completed,archived,cancelled'],
            'actual_completion_date' => ['nullable', 'date'],
        ]);

        $project = Project::where('projectID', $projectID)->firstOrFail();

        $allowedTransitions = [
            'created' => ['active', 'cancelled'],
            'active' => ['in_progress', 'completed', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => ['archived'],
            'archived' => [],
            'cancelled' => [],
        ];

        $currentStatus = $project->project_status;
        $nextStatus = $validated['project_status'];
        if ($currentStatus !== $nextStatus && !in_array($nextStatus, $allowedTransitions[$currentStatus] ?? [], true)) {
            abort(422, "Invalid project status transition: {$currentStatus} -> {$nextStatus}");
        }

        $project->fill([
            'project_name' => $validated['project_name'],
            'description' => $validated['description'],
            'goals' => $validated['goals'],
            'target_budget' => $validated['target_budget'],
            'start_date' => $validated['start_date'],
            'target_completion_date' => $validated['target_completion_date'],
            'project_status' => $validated['project_status'],
            'actual_completion_date' => $validated['actual_completion_date'],
            'updated_date' => now(),
        ]);

        if ($project->project_status === 'completed' && empty($project->actual_completion_date)) {
            $project->actual_completion_date = now()->toDateString();
        }

        $project->save();

        return redirect()->route($this->resolveProjectsRoute('show'), $project->projectID);
    }

    public function requestClosure(int $projectID)
    {
        $user = Auth::user();

        // Administrator or Teacher can request closure
        if (!$user || !in_array($user->user_type, ['administrator', 'teacher'])) {
            abort(403, 'Unauthorized action.');
        }

        $project = Project::where('projectID', $projectID)->firstOrFail();

        if (!in_array($project->project_status, ['active', 'in_progress'])) {
            return redirect()->back();
        }

        $project->project_status = 'completed';
        $project->updated_date = now();
        $project->actual_completion_date = $project->actual_completion_date ?? now()->toDateString();
        $project->save();

        return redirect()->route($this->resolveProjectsRoute('show'), $project->projectID);
    }

    /**
     * Activate a project for parent contributions (Administrator/Teacher only)
     */
    public function activate(int $projectID)
    {
        $user = Auth::user();

        // Only Administrator or Teacher can activate projects
        if (!$user || !in_array($user->user_type, ['administrator', 'teacher'])) {
            abort(403, 'Only Administrator or Teacher can activate projects.');
        }

        $project = Project::where('projectID', $projectID)->firstOrFail();

        // Can only activate projects with 'created' status
        if ($project->project_status !== 'created') {
            return redirect()->back()->with('error', 'Only projects with "Not Started" status can be activated.');
        }

        $project->project_status = 'active';
        $project->updated_date = now();
        $project->save();

        return redirect()->route($this->resolveProjectsRoute('show'), $project->projectID);
    }

    public function approveClosure(int $projectID)
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'principal') {
            abort(403, 'Unauthorized action.');
        }

        $project = Project::where('projectID', $projectID)->firstOrFail();

        if ($project->project_status !== 'completed') {
            return redirect()->back();
        }

        $project->project_status = 'archived';
        $project->updated_date = now();
        $project->actual_completion_date = $project->actual_completion_date ?? now()->toDateString();
        $project->save();

        return redirect()->route($this->resolveProjectsRoute('show'), $project->projectID);
    }

    public function destroy(int $projectID)
    {
        $user = Auth::user();

        // Administrator or Teacher can archive projects
        if (!$user || !in_array($user->user_type, ['administrator', 'teacher'])) {
            abort(403, 'Only Administrator or Teacher can archive projects.');
        }

        $project = Project::where('projectID', $projectID)->firstOrFail();
        $project->project_status = 'archived';
        $project->updated_date = now();
        $project->actual_completion_date = $project->actual_completion_date ?? now()->toDateString();
        $project->save();

        return redirect()->route($this->resolveProjectsRoute('index'));
    }
}
