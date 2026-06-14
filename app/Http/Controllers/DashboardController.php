<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = JobVacancy::with('company')->select('job_vacancies.*');

        // Search across title, location, and company name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_vacancies.title', 'like', "%{$search}%")
                  ->orWhere('job_vacancies.location', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by job type
        if ($request->filled('filter')) {
            $query->where('job_vacancies.type', $request->filter);
        }

        // Filter by salary range
        if ($request->filled('salary_min')) {
            $query->where('job_vacancies.salary', '>=', $request->salary_min);
        }

        // Get user's latest resume for match score calculation
        $latestResume = auth()->user()->resumes()->latest()->first();

        // Sorting & Match Calculation
        $sort = $request->get('sort', 'newest');
        
        if ($sort === 'match') {
            // Because we compute Cosine Similarity in PHP, we fetch all active jobs, compute score, sort, and manually paginate.
            $allJobs = $query->get();
            $resumeEmbedding = ($latestResume && $latestResume->vector_embedding) ? json_decode($latestResume->vector_embedding, true) : null;

            // Pre-fetch AI scores from user applications
            $jobIds = $allJobs->pluck('id')->toArray();
            $userApplications = JobApplication::where('userId', auth()->id())
                ->whereIn('jobVacancyId', $jobIds)
                ->pluck('aiGeneratedScore', 'jobVacancyId')
                ->toArray();

            $allJobs->each(function ($job) use ($resumeEmbedding, $userApplications) {
                if (isset($userApplications[$job->id]) && $userApplications[$job->id] !== null) {
                    $job->matchScore = (int) $userApplications[$job->id];
                } else {
                    $jobEmbedding = $job->vector_embedding ? json_decode($job->vector_embedding, true) : null;
                    $job->matchScore = ($resumeEmbedding && $jobEmbedding) 
                        ? $this->calculateCosineSimilarity($resumeEmbedding, $jobEmbedding) 
                        : 0;
                }
            });

            $sortedJobs = $allJobs->sortByDesc('matchScore')->values();
            
            // Manual pagination
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 10;
            $currentItems = $sortedJobs->slice(($currentPage - 1) * $perPage, $perPage)->all();
            
            $jobs = new LengthAwarePaginator($currentItems, count($sortedJobs), $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query()
            ]);
        } else {
            // Standard SQL Sorting and Pagination
            match ($sort) {
                'salary_desc' => $query->orderByDesc('job_vacancies.salary'),
                'salary_asc'  => $query->orderBy('job_vacancies.salary'),
                default       => $query->latest('job_vacancies.created_at'), // newest first
            };

            $jobs = $query->paginate(10)->withQueryString();

            $resumeEmbedding = ($latestResume && $latestResume->vector_embedding) ? json_decode($latestResume->vector_embedding, true) : null;
            $jobIds = $jobs->pluck('id')->toArray();
            $userApplications = JobApplication::where('userId', auth()->id())
                ->whereIn('jobVacancyId', $jobIds)
                ->pluck('aiGeneratedScore', 'jobVacancyId')
                ->toArray();

            $jobs->getCollection()->transform(function ($job) use ($resumeEmbedding, $userApplications) {
                if (isset($userApplications[$job->id]) && $userApplications[$job->id] !== null) {
                    $job->matchScore = (int) $userApplications[$job->id];
                    return $job;
                }
                
                $jobEmbedding = $job->vector_embedding ? json_decode($job->vector_embedding, true) : null;
                $job->matchScore = ($resumeEmbedding && $jobEmbedding) 
                    ? $this->calculateCosineSimilarity($resumeEmbedding, $jobEmbedding) 
                    : 0;
                
                return $job;
            });
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

    private function calculateCosineSimilarity(array $vec1, array $vec2): int
    {
        if (count($vec1) !== count($vec2) || count($vec1) === 0) return 0;
        
        $dotProduct = 0.0;
        $mag1 = 0.0;
        $mag2 = 0.0;

        foreach ($vec1 as $i => $v1) {
            $v2 = $vec2[$i];
            $dotProduct += $v1 * $v2;
            $mag1 += $v1 * $v1;
            $mag2 += $v2 * $v2;
        }

        if ($mag1 == 0 || $mag2 == 0) return 0;

        $similarity = $dotProduct / (sqrt($mag1) * sqrt($mag2));
        
        return (int) max(0, min(100, round($similarity * 100)));
    }
}