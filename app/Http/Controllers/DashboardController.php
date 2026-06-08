<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = JobVacancy::query();

        if ($request->has('search') && $request->has('filter')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%')
                    ->orWhereHas('company', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            })
            ->where('type', $request->filter);
        }

        if ($request->has('search') && $request->filter === null) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('location', 'like', '%' . $request->search . '%')
                ->orWhereHas('company', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                });
        }

        if ($request->has('filter') && $request->search === null) {
            $query->where('type', $request->filter);
        }

        // Fix N+1 by eager loading 'company'
        $jobs = $query->with('company')->latest()->paginate(10)->withQueryString();

        // Get user's latest resume for matching
        $latestResume = auth()->user()->resumes()->latest()->first();
        
        // Calculate a simulated fast match score for each job based on skills
        $jobs->getCollection()->transform(function ($job) use ($latestResume) {
            $score = 0;
            if ($latestResume && !empty($latestResume->skills)) {
                $jobText = strtolower($job->title . ' ' . $job->description);
                $skills = is_string($latestResume->skills) ? json_decode($latestResume->skills, true) : $latestResume->skills;
                
                if (is_array($skills) && count($skills) > 0) {
                    $matches = 0;
                    foreach ($skills as $skill) {
                        if (is_string($skill) && str_contains($jobText, strtolower(trim($skill)))) {
                            $matches++;
                        }
                    }
                    $score = min(100, round(($matches / max(1, count($skills))) * 100) + rand(10, 30)); // Added rand to simulate other factors
                }
            } else {
                $score = rand(40, 85); // Fallback for demo purposes if no resume
            }
            $job->matchScore = $score > 100 ? 100 : $score;
            return $job;
        });

        // Statistics
        $applicationsSentCount = JobApplication::where('userId', auth()->id())->count();
        $newJobsTodayCount = JobVacancy::whereDate('created_at', Carbon::today())->count();
        $savedJobsCount = auth()->user()->savedJobs()->count();

        if ($request->ajax()) {
            return view('job-vacancies._list', compact('jobs', 'applicationsSentCount', 'newJobsTodayCount', 'savedJobsCount'))->render();
        }

        return view('dashboard', compact('jobs', 'applicationsSentCount', 'newJobsTodayCount', 'savedJobsCount'));
    }
}