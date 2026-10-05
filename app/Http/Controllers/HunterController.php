<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Services\ResumeAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HunterController extends Controller
{
    protected ResumeAnalysisService $resumeAnalysisService;

    public function __construct(ResumeAnalysisService $resumeAnalysisService)
    {
        $this->resumeAnalysisService = $resumeAnalysisService;
    }

    /**
     * Show the personal Job Hunter review page.
     * Lists all externally-imported jobs with filters and match scores.
     */
    public function index(Request $request)
    {
        $user        = auth()->user();
        $latestResume = $user->resumes()->latest()->first();
        $resumeEmbedding = ($latestResume && $latestResume->vector_embedding)
            ? json_decode($latestResume->vector_embedding, true)
            : null;
        $candidateSkills = $latestResume ? ($latestResume->skills ?? []) : [];

        // Base query: only imported (external) jobs
        $query = JobVacancy::with('company')
            ->whereNotNull('source_platform')
            ->select('job_vacancies.*');

        // Filter by platform
        if ($request->filled('platform') && in_array($request->platform, ['greenhouse', 'weworkremotely', 'adzuna', 'remotive'])) {
            $query->where('source_platform', $request->platform);
        }

        // Search by title / company
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_vacancies.title', 'like', "%{$search}%")
                  ->orWhere('job_vacancies.location', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $query->latest('job_vacancies.imported_at');

        // Fetch all for match calculation, then filter strictly for technical & eligible jobs
        $allJobs = $query->get()->filter(function ($job) {
            $check = \App\Support\JobFilter::isEligible(
                $job->title ?? '',
                $job->description ?? '',
                $job->location ?? ''
            );
            return $check['eligible'];
        })->values();

        // Find which jobs have an existing hunter application from this user
        $jobIds = $allJobs->pluck('id')->toArray();
        $existingHunterApps = JobApplication::where('userId', $user->id)
            ->where('is_personal', true)
            ->whereIn('jobVacancyId', $jobIds)
            ->get()
            ->keyBy('jobVacancyId');

        // Compute hybrid match scores + attach hunter application status, region, and skills breakdown
        $allJobs->each(function ($job) use ($resumeEmbedding, $candidateSkills, $existingHunterApps) {
            $jobEmbedding = $job->vector_embedding ? json_decode($job->vector_embedding, true) : null;
            
            $hybrid = \App\Support\SkillMatcher::computeHybridScore(
                $resumeEmbedding,
                $jobEmbedding,
                $candidateSkills,
                $job->title ?? '',
                $job->description ?? ''
            );

            $hunterApp = $existingHunterApps->get($job->id);
            $job->hunterApplication = $hunterApp;
            $job->isAudited = false;

            // If an audited application already exists, synchronize with the audited score from AI
            if ($hunterApp && $hunterApp->aiGeneratedScore !== null && $hunterApp->aiGeneratedScore > 0) {
                $job->matchScore = (int) $hunterApp->aiGeneratedScore;
                $job->isAudited  = true;
            } else {
                $job->matchScore = $hybrid['composite_score'];
            }

            $job->matchDetails   = $hybrid;
            $job->matchedSkills  = $hybrid['matched_skills'];
            $job->missingSkills  = $hybrid['missing_skills'];
            $job->skillsScore    = $hybrid['skills_score'];
            $job->vectorScore    = $hybrid['vector_score'];
            $job->trackMismatch  = $hybrid['track_mismatch'] ?? false;
            $job->trackDomain    = $hybrid['track_domain'] ?? null;
            $job->trackReason    = $hybrid['track_reason'] ?? null;
            $job->region         = $this->detectRegion($job->location ?? '', $job->title ?? '');
        });

        // Regional counts (computed across all imported jobs matching text search/platform)
        $totalSa        = $allJobs->filter(fn($j) => $j->region['code'] === 'sa')->count();
        $totalAe        = $allJobs->filter(fn($j) => $j->region['code'] === 'ae')->count();
        $totalGccOther  = $allJobs->filter(fn($j) => $j->region['code'] === 'gcc_other')->count();
        $totalWorldwide = $allJobs->filter(fn($j) => $j->region['code'] === 'worldwide')->count();
        $totalGccAll    = $totalSa + $totalAe + $totalGccOther;

        // Filter by region
        if ($request->filled('region')) {
            $reg = $request->region;
            if ($reg === 'gcc') {
                $allJobs = $allJobs->filter(fn($j) => in_array($j->region['code'], ['sa', 'ae', 'gcc_other']))->values();
            } elseif (in_array($reg, ['sa', 'ae', 'gcc_other', 'worldwide'])) {
                $allJobs = $allJobs->filter(fn($j) => $j->region['code'] === $reg)->values();
            }
        }

        // Sort: applied filter or by match
        $filterApplied = $request->input('applied_filter', 'all');
        if ($filterApplied === 'applied') {
            $allJobs = $allJobs->filter(fn($j) => $j->hunterApplication !== null)->values();
        } elseif ($filterApplied === 'not_applied') {
            $allJobs = $allJobs->filter(fn($j) => $j->hunterApplication === null)->values();
        }

        // Match Threshold Filter (Strict Recommendation: default >= 80%)
        $count80Plus = $allJobs->filter(fn($j) => ($j->matchScore ?? 0) >= 80)->count();
        $count70Plus = $allJobs->filter(fn($j) => ($j->matchScore ?? 0) >= 70)->count();
        $countAll    = $allJobs->count();

        $matchFilter = $request->input('match_filter', '80');
        if ($matchFilter === '80') {
            $allJobs = $allJobs->filter(fn($j) => ($j->matchScore ?? 0) >= 80)->values();
        } elseif ($matchFilter === '70') {
            $allJobs = $allJobs->filter(fn($j) => ($j->matchScore ?? 0) >= 70)->values();
        }

        $sortedJobs = $allJobs->sortByDesc('matchScore')->values();

        // Manual pagination
        $perPage     = 12;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $sortedJobs->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $jobs = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            count($sortedJobs),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Stats (computed from eligible technical jobs)
        $totalImported   = $allJobs->count();
        $totalApplied    = JobApplication::where('userId', $user->id)->where('is_personal', true)->count();
        $totalGreenhouse = $allJobs->where('source_platform', 'greenhouse')->count();
        $totalWwr        = $allJobs->where('source_platform', 'weworkremotely')->count();
        $totalAdzuna     = $allJobs->where('source_platform', 'adzuna')->count();
        $totalRemotive   = $allJobs->where('source_platform', 'remotive')->count();

        // User skills for display
        $userSkills = $latestResume ? ($latestResume->skills ?? []) : [];

        $resumes = $user->resumes()->latest()->get();

        return view('hunter.index', compact(
            'jobs',
            'totalImported',
            'totalApplied',
            'totalGreenhouse',
            'totalWwr',
            'totalAdzuna',
            'totalRemotive',
            'totalSa',
            'totalAe',
            'totalGccOther',
            'totalWorldwide',
            'totalGccAll',
            'userSkills',
            'resumes',
            'latestResume',
            'matchFilter',
            'count80Plus',
            'count70Plus',
            'countAll'
        ));
    }

    /**
     * Generate a tailored AI application for a specific job and save it as a Hunter application.
     * If one already exists, return the existing data without calling OpenAI again.
     *
     * POST /hunter/{jobId}/generate
     */
    public function generate(Request $request, string $jobId)
    {
        $user = auth()->user();

        $jobVacancy = JobVacancy::findOrFail($jobId);

        // Pick resume: use the selected one or fall back to latest
        $resumeId = $request->input('resume_id');
        $resume   = $resumeId
            ? Resume::where('id', $resumeId)->where('userId', $user->id)->firstOrFail()
            : $user->resumes()->latest()->first();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'No resume found. Please upload a resume first.',
            ], 422);
        }

        // Compute hybrid match score for rich details
        $jobEmbedding = $jobVacancy->vector_embedding ? json_decode($jobVacancy->vector_embedding, true) : null;
        $resumeEmbedding = $resume->vector_embedding ? json_decode($resume->vector_embedding, true) : null;
        $candidateSkills = $resume->skills ?? [];

        $hybrid = \App\Support\SkillMatcher::computeHybridScore(
            $resumeEmbedding,
            $jobEmbedding,
            $candidateSkills,
            $jobVacancy->title ?? '',
            $jobVacancy->description ?? ''
        );

        // Check if a hunter application already exists for this job
        $existing = JobApplication::where('userId', $user->id)
            ->where('is_personal', true)
            ->where('jobVacancyId', $jobId)
            ->first();

        if ($existing) {
            return response()->json([
                'success'             => true,
                'already_existed'     => true,
                'application_id'      => $existing->id,
                'cover_letter'        => $existing->tailored_cover_letter,
                'key_selling_points'  => $existing->tailored_key_points ?? [],
                'subject_line'        => $existing->suggested_subject_line,
                'hunter_status'       => $existing->hunter_status?->value ?? 'draft',
                'hunter_status_label' => $existing->hunter_status?->label() ?? 'Draft',
                'source_url'          => $jobVacancy->source_url,
                'match_score'         => $existing->aiGeneratedScore ?? $hybrid['composite_score'],
                'vector_score'        => $hybrid['vector_score'],
                'skills_score'        => $hybrid['skills_score'],
                'matched_skills'      => $hybrid['matched_skills'],
                'missing_skills'      => $hybrid['missing_skills'],
                'match_summary'       => $hybrid['summary'],
            ]);
        }

        // Build resume data array for AI
        $resumeData = [
            'name'       => $user->name,
            'summary'    => is_string($resume->summary) ? $resume->summary : json_encode($resume->summary),
            'skills'     => is_string($resume->skills)
                ? (json_decode($resume->skills, true) ?? [$resume->skills])
                : ($resume->skills ?? []),
            'experience' => is_string($resume->experience)
                ? (json_decode($resume->experience, true) ?? [])
                : ($resume->experience ?? []),
            'education'  => is_string($resume->education)
                ? (json_decode($resume->education, true) ?? [])
                : ($resume->education ?? []),
        ];

        // Call OpenAI to generate tailored application
        try {
            $result = $this->resumeAnalysisService->generateTailoredApplication($jobVacancy, $resumeData);
        } catch (\Exception $e) {
            Log::error('HunterController@generate: AI generation failed — ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'AI generation failed. Please try again.',
            ], 500);
        }

        if (empty($result['cover_letter'])) {
            return response()->json([
                'success' => false,
                'message' => 'AI returned an empty response. Please try again.',
            ], 500);
        }

        // Also compute AI match score via analyzeResume or hybrid score fallback
        $evaluation = ['aiGeneratedScore' => null, 'aiGeneratedFeedback' => null];
        try {
            $evaluation = $this->resumeAnalysisService->analyzeResume($jobVacancy, $resumeData);
        } catch (\Exception $e) {
            Log::warning('HunterController@generate: analyzeResume failed (non-critical) — ' . $e->getMessage());
        }

        if (empty($evaluation['aiGeneratedScore'])) {
            $evaluation['aiGeneratedScore'] = $hybrid['composite_score'];
            $evaluation['aiGeneratedFeedback'] = "Match calculated based on {$hybrid['summary']} with {$hybrid['vector_score']}% semantic alignment.";
        }

        // Save as Hunter Application (is_personal = true, status = draft)
        $application = JobApplication::create([
            'status'                => 'pending',
            'is_personal'           => true,
            'hunter_status'         => 'draft',
            'jobVacancyId'          => $jobId,
            'resumeId'              => $resume->id,
            'userId'                => $user->id,
            'tailored_cover_letter' => $result['cover_letter'],
            'tailored_key_points'   => $result['key_selling_points'],
            'suggested_subject_line' => $result['suggested_subject_line'],
            'aiGeneratedScore'      => $evaluation['aiGeneratedScore'] ?? null,
            'aiGeneratedFeedback'   => $evaluation['aiGeneratedFeedback'] ?? null,
        ]);

        return response()->json([
            'success'            => true,
            'already_existed'    => false,
            'application_id'     => $application->id,
            'cover_letter'       => $result['cover_letter'],
            'key_selling_points' => $result['key_selling_points'],
            'subject_line'       => $result['suggested_subject_line'],
            'hunter_status'      => 'draft',
            'hunter_status_label' => 'Draft / Prepared',
            'source_url'         => $jobVacancy->source_url,
            'match_score'        => $application->aiGeneratedScore ?? $hybrid['composite_score'],
            'vector_score'       => $hybrid['vector_score'],
            'skills_score'       => $hybrid['skills_score'],
            'matched_skills'     => $hybrid['matched_skills'],
            'missing_skills'     => $hybrid['missing_skills'],
            'match_summary'      => $hybrid['summary'],
        ]);
    }

    /**
     * Compute cosine similarity (0-100) between two embedding vectors.
     */
    private function calculateCosineSimilarity(array $vec1, array $vec2): int
    {
        if (count($vec1) !== count($vec2) || count($vec1) === 0) return 0;

        $dot = 0.0;
        $m1  = 0.0;
        $m2  = 0.0;

        foreach ($vec1 as $i => $v1) {
            $v2  = $vec2[$i];
            $dot += $v1 * $v2;
            $m1  += $v1 * $v1;
            $m2  += $v2 * $v2;
        }

        if ($m1 == 0 || $m2 == 0) return 0;

        return (int) max(0, min(100, round(($dot / (sqrt($m1) * sqrt($m2))) * 100)));
    }

    /**
     * Detect region / country for a job vacancy based on its location and title.
     *
     * Returns an array with:
     *  - code: 'sa' | 'ae' | 'gcc_other' | 'worldwide'
     *  - label: string
     *  - flag: string
     *  - badge_class: string
     */
    public function detectRegion(string $location, string $title = ''): array
    {
        $haystack = strtolower($location . ' ' . $title);

        if (preg_match('/(saudi|ksa|riyadh|jeddah|dammam|khobar|dhahran|jubail|makkah|medina)/i', $haystack)) {
            return [
                'code'        => 'sa',
                'label'       => 'Saudi Arabia',
                'flag'        => '🇸🇦',
                'badge_class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
            ];
        }

        if (preg_match('/(uae|united arab emirates|dubai|abu dhabi|sharjah|ajman)/i', $haystack)) {
            return [
                'code'        => 'ae',
                'label'       => 'UAE',
                'flag'        => '🇦🇪',
                'badge_class' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
            ];
        }

        if (preg_match('/(kuwait|qatar|doha|bahrain|manama|oman|muscat)/i', $haystack)) {
            return [
                'code'        => 'gcc_other',
                'label'       => 'Gulf (GCC)',
                'flag'        => '🇰🇼 🇶🇦',
                'badge_class' => 'bg-cyan-50 text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800/60',
            ];
        }

        return [
            'code'        => 'worldwide',
            'label'       => 'Worldwide Remote',
            'flag'        => '🌍',
            'badge_class' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/60',
        ];
    }
}
