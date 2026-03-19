<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectContribution;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentProjectController extends Controller
{
    /**
     * Display a listing of active projects visible to parents.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $baseQuery = Project::with(['updates'])->orderBy('start_date', 'desc');

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('goals', 'like', "%{$search}%");
            });
        }

        $upcomingProjects = (clone $baseQuery)
            ->whereIn('project_status', ['active', 'in_progress'])
            ->get();

        $previousProjects = (clone $baseQuery)
            ->whereIn('project_status', ['completed', 'archived'])
            ->get();

        foreach ([$upcomingProjects, $previousProjects] as $collection) {
            foreach ($collection as $project) {
                $project->progress_percentage = $project->target_budget > 0
                    ? min(100, round(($project->current_amount / $project->target_budget) * 100, 1))
                    : 0;

                $project->latest_update = $project->updates()->latest('update_date')->first();
                $project->parsed_details = $this->parseDescription($project->description);
            }
        }

        return view('parent.projects.index', compact('upcomingProjects', 'previousProjects', 'search'));
    }

    /**
     * Display the specified project details.
     */
    public function show($projectID)
    {
        $project = Project::with(['contributions.parent', 'updates.updater', 'creator'])
            ->whereIn('project_status', ['active', 'in_progress'])
            ->findOrFail($projectID);

        // Calculate progress
        $project->progress_percentage = $project->target_budget > 0 
            ? min(100, round(($project->current_amount / $project->target_budget) * 100, 1))
            : 0;

        // Parse goals to separate goals and success criteria
        $goalsData = $this->parseGoals($project->goals);
        $project->parsed_goals = $goalsData['goals'];
        $project->parsed_criteria = $goalsData['criteria'];

        // Get contribution statistics
        $contributionStats = [
            'total_contributions' => $project->contributions()->where('payment_status', 'completed')->count(),
            'total_amount' => $project->current_amount,
            'unique_parents' => $project->contributions()->distinct('parentID')->count('parentID'),
        ];

        // Get recent updates
        $recentUpdates = $project->updates()
            ->with('updater')
            ->orderBy('update_date', 'desc')
            ->take(5)
            ->get();

        // Check if current parent has contributed
        $parentProfile = Auth::user()->parentProfile;
        $hasContributed = false;
        $parentContributions = collect();
        
        if ($parentProfile) {
            $parentContributions = $project->contributions()
                ->where('parentID', $parentProfile->parentID)
                ->orderBy('contribution_date', 'desc')
                ->get();
            $hasContributed = $parentContributions->isNotEmpty();
        }

        return view('parent.projects.show', compact(
            'project',
            'contributionStats',
            'recentUpdates',
            'hasContributed',
            'parentContributions'
        ));
    }

    /**
     * Parse goals field to extract goals and success criteria.
     */
    private function parseGoals($goalsText)
    {
        $goals = '';
        $criteria = '';

        if (strpos($goalsText, '|') !== false) {
            $parts = explode('|', $goalsText, 2);
            
            // Extract goals
            if (isset($parts[0])) {
                $goals = trim(str_replace('Goals:', '', $parts[0]));
            }
            
            // Extract success criteria
            if (isset($parts[1])) {
                $criteria = trim(str_replace('Success Criteria:', '', $parts[1]));
            }
        } else {
            // If no structured format, treat entire text as goals
            $goals = $goalsText;
        }

        return [
            'goals' => $goals,
            'criteria' => $criteria
        ];
    }

    /**
     * Parse description field to extract structured details.
     */
    private function parseDescription(?string $description): array
    {
        $details = [
            'title' => null,
            'date' => null,
            'time' => null,
            'venue' => null,
            'objective' => null,
            'photo' => null,
        ];

        if (empty($description)) {
            return $details;
        }

        $lines = preg_split('/\r\n|\r|\n/', $description);
        foreach ($lines as $line) {
            $line = trim($line);
            if (stripos($line, 'Title:') === 0) {
                $details['title'] = trim(substr($line, strlen('Title:')));
            } elseif (stripos($line, 'Date:') === 0) {
                $details['date'] = trim(substr($line, strlen('Date:')));
            } elseif (stripos($line, 'Time:') === 0) {
                $details['time'] = trim(substr($line, strlen('Time:')));
            } elseif (stripos($line, 'Venue:') === 0) {
                $details['venue'] = trim(substr($line, strlen('Venue:')));
            } elseif (stripos($line, 'Objective:') === 0) {
                $details['objective'] = trim(substr($line, strlen('Objective:')));
            } elseif (stripos($line, 'Photo:') === 0) {
                $details['photo'] = trim(substr($line, strlen('Photo:')));
            }
        }

        return $details;
    }
}
