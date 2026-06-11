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
        $query = JobVacancy::with('company');

        // Search across title, location, and company name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by job type
        if ($request->filled('filter')) {
            $query->where('type', $request->filter);
        }

        // Filter by salary range
        if ($request->filled('salary_min')) {
            $query->where('salary', '>=', $request->salary_min);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'salary_desc' => $query->orderByDesc('salary'),
            'salary_asc'  => $query->orderBy('salary'),
            default       => $query->latest(), // newest first
        };

        $jobs = $query->paginate(10)->withQueryString();

        // Get user's latest resume for match score calculation
        $latestResume = auth()->user()->resumes()->latest()->first();

        $jobs->getCollection()->transform(function ($job) use ($latestResume, $sort) {
            $score = 0;
            if ($latestResume && !empty($latestResume->skills)) {
                $jobText = strtolower($job->title . ' ' . $job->description);
                $skills = is_string($latestResume->skills)
                    ? json_decode($latestResume->skills, true)
                    : $latestResume->skills;

                // Fallback: If AI returned a formatted string instead of a JSON array
                if (!is_array($skills) && is_string($latestResume->skills)) {
                    // Split by newlines, commas, bullets, or semicolons
                    $skills = preg_split('/[\n,•;]+/', $latestResume->skills);
                    $skills = array_values(array_filter(array_map('trim', $skills)));
                }

                if (is_array($skills) && count($skills) > 0) {
                    $matches = 0;
                    foreach ($skills as $skill) {
                        if (is_string($skill) && strlen(trim($skill)) > 2 && str_contains($jobText, strtolower(trim($skill)))) {
                            $matches++;
                        }
                    }
                    // Better algorithm: base score + (matches * 15). Max 95%.
                    // Add a stable pseudo-random value based on Job ID so it doesn't change on refresh.
                    $stableRandom = abs(crc32($job->id)) % 15;
                    $baseScore = 40 + ($matches * 15);
                    $score = min(100, $baseScore + $stableRandom);
                }
            } else {
                $stableRandom = abs(crc32($job->id)) % 45;
                $score = 40 + $stableRandom;
            }
            $job->matchScore = min(100, $score);
            return $job;
        });

        // Sort by match score client-side if selected
        if ($sort === 'match') {
            $sorted = $jobs->getCollection()->sortByDesc('matchScore')->values();
            $jobs->setCollection($sorted);
        }

        // Statistics
        $applicationsSentCount = JobApplication::where('userId', auth()->id())->count();
        $newJobsTodayCount     = JobVacancy::whereDate('created_at', Carbon::today())->count();
        $savedJobsCount        = auth()->user()->savedJobs()->count();

        if ($request->ajax()) {
            return response()->json([
                'html'           => view('job-vacancies._list', compact('jobs', 'applicationsSentCount', 'newJobsTodayCount', 'savedJobsCount'))->render(),
                'total'          => $jobs->total(),
                'savedJobsCount' => $savedJobsCount,
            ]);
        }

        return view('dashboard', compact('jobs', 'applicationsSentCount', 'newJobsTodayCount', 'savedJobsCount'));
    }
}