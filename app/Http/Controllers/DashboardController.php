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

        // Sorting
        $sort = $request->get('sort', 'newest');
        if ($sort === 'match') {
            $query->leftJoin('job_applications', function($join) {
                $join->on('job_vacancies.id', '=', 'job_applications.jobVacancyId')
                     ->where('job_applications.userId', '=', auth()->id());
            });

            $scoreSql = "0";
            if ($latestResume && !empty($latestResume->skills)) {
                $skills = is_string($latestResume->skills)
                    ? json_decode($latestResume->skills, true)
                    : $latestResume->skills;

                if (!is_array($skills) && is_string($latestResume->skills)) {
                    $skills = preg_split('/[\n,•;]+/', $latestResume->skills);
                }

                if (is_array($skills) && count($skills) > 0) {
                    $skills = array_values(array_filter(array_map('trim', $skills)));
                    $validSkillsCount = 0;
                    $skillCases = [];

                    foreach ($skills as $skill) {
                        $skillStr = strtolower($skill);
                        if (strlen($skillStr) > 1) {
                            $validSkillsCount++;
                            $safeSkill = \Illuminate\Support\Facades\DB::connection()->getPdo()->quote('%' . $skillStr . '%');
                            $skillCases[] = "(CASE WHEN LOWER(CONCAT(job_vacancies.title, ' ', job_vacancies.description)) LIKE {$safeSkill} THEN 1 ELSE 0 END)";
                        }
                    }

                    if ($validSkillsCount > 0) {
                        $sumSql = implode(' + ', $skillCases);
                        $divisor = min(5, $validSkillsCount);
                        $scoreSql = "LEAST(100, ROUND(($sumSql) / $divisor * 100))";
                    }
                }
            }

            $query->orderByRaw("COALESCE(job_applications.aiGeneratedScore, $scoreSql) DESC")
                  ->orderByDesc('job_vacancies.created_at');
        } else {
            match ($sort) {
                'salary_desc' => $query->orderByDesc('job_vacancies.salary'),
                'salary_asc'  => $query->orderBy('job_vacancies.salary'),
                default       => $query->latest('job_vacancies.created_at'), // newest first
            };
        }

        $jobs = $query->paginate(10)->withQueryString();

        // Pre-fetch actual AI scores from existing applications to prevent N+1 queries
        $jobIds = $jobs->pluck('id')->toArray();
        $userApplications = JobApplication::where('userId', auth()->id())
            ->whereIn('jobVacancyId', $jobIds)
            ->pluck('aiGeneratedScore', 'jobVacancyId')
            ->toArray();

        $jobs->getCollection()->transform(function ($job) use ($latestResume, $userApplications) {
            // If the user already applied, use the highly accurate AI score
            if (isset($userApplications[$job->id]) && $userApplications[$job->id] !== null) {
                $job->matchScore = (int) $userApplications[$job->id];
                return $job;
            }

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
                }

                if (is_array($skills) && count($skills) > 0) {
                    $skills = array_values(array_filter(array_map('trim', $skills)));
                    $matches = 0;
                    $validSkillsCount = 0;

                    foreach ($skills as $skill) {
                        $skillStr = strtolower($skill);
                        if (strlen($skillStr) > 1) { // Allowing 2-char skills like 'Go', 'C#', 'JS'
                            $validSkillsCount++;
                            // Use word boundary for accurate matching to avoid partial word matches
                            // Special handling for characters like C++ or C#
                            $escapedSkill = preg_quote($skillStr, '/');
                            if (preg_match('/\b' . $escapedSkill . '\b/i', $jobText)) {
                                $matches++;
                            } elseif (str_contains($jobText, $skillStr)) {
                                $matches++;
                            }
                        }
                    }

                    if ($validSkillsCount > 0 && $matches > 0) {
                        // Calculate an honest score. 
                        // We assume matching up to 5 distinct skills is a great indicator of a strong match,
                        // avoiding penalizing users who list many skills when only a few are needed for the job.
                        $score = (int) min(100, round(($matches / min(5, $validSkillsCount)) * 100));
                    }
                }
            } 
            
            // Score is an honest 0 if no skills matched or no resume is found.
            $job->matchScore = $score;
            return $job;
        });



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