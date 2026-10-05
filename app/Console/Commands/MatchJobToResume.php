<?php

namespace App\Console\Commands;

use App\Enums\HunterApplicationStatus;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Services\ResumeAnalysisService;
use Illuminate\Console\Command;

class MatchJobToResume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:match {job_id} {resume_id}
                            {--save : Save result as a Job Hunter personal application}
                            {--applied : Set status to Applied immediately instead of Draft}
                            {--channel= : Submission channel (e.g. greenhouse, email, website, linkedin)}
                            {--notes= : Initial notes for this application}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match a job vacancy with a resume using AI, print materials, and optionally save to Job Hunter tracker';

    /**
     * Execute the console command.
     */
    public function handle(ResumeAnalysisService $resumeAnalysisService)
    {
        $jobId = $this->argument('job_id');
        $resumeId = $this->argument('resume_id');

        $this->info("Looking up Job ID: {$jobId} and Resume ID: {$resumeId}...");

        $job = JobVacancy::find($jobId);
        if (!$job) {
            $this->error("Job vacancy with ID {$jobId} not found.");
            return Command::FAILURE;
        }

        $resume = Resume::find($resumeId);
        if (!$resume) {
            $this->error("Resume with ID {$resumeId} not found.");
            return Command::FAILURE;
        }

        $this->info("Found Job: {$job->title} at " . ($job->company->name ?? 'Unknown Company'));
        $this->info("Found Resume for candidate: {$resume->user->name}");
        $this->info("Starting AI generation. Please wait (this may take 10-30 seconds)...\n");

        $resumeData = [
            'experience' => $resume->experience,
            'education' => $resume->education,
            'skills' => $resume->skills,
            'summary' => $resume->summary,
            'achievements' => $resume->achievements ?? '',
        ];

        try {
            $result = $resumeAnalysisService->generateTailoredApplication($job, $resumeData);
            
            $this->info("=============================================");
            $this->info("✅ TAILORED APPLICATION GENERATED SUCCESSFULLY");
            $this->info("=============================================\n");

            $this->line("<fg=yellow;options=bold>Email Subject:</>");
            $this->line($result['suggested_subject_line'] . "\n");

            $this->line("<fg=yellow;options=bold>Key Selling Points:</>");
            foreach ($result['key_selling_points'] as $point) {
                $this->line("• " . $point);
            }
            $this->line("\n");

            $this->line("<fg=yellow;options=bold>Cover Letter:</>");
            $this->line($result['cover_letter'] . "\n");

            // Check if --save option is passed to track in Job Hunter Mode
            if ($this->option('save')) {
                $isApplied = (bool) $this->option('applied');
                $channel = $this->option('channel') ?? ($job->source_platform ?? 'direct_site');
                $initialNotes = $this->option('notes');

                $this->info("Calculating AI match score and recruiter evaluation...");
                $analysis = $resumeAnalysisService->analyzeResume($job, $resumeData);

                $application = JobApplication::where('jobVacancyId', $job->id)
                    ->where('userId', $resume->userId)
                    ->where('is_personal', true)
                    ->first();

                if (!$application) {
                    $application = new JobApplication();
                    $application->jobVacancyId = $job->id;
                    $application->userId = $resume->userId;
                    $application->is_personal = true;
                    $application->status = 'pending';
                }

                $application->resumeId = $resume->id;
                $application->aiGeneratedScore = $analysis['aiGeneratedScore'] ?? 85;
                $application->aiGeneratedFeedback = $analysis['aiGeneratedFeedback'] ?? null;

                $application->hunter_status = $isApplied
                    ? HunterApplicationStatus::APPLIED
                    : ($application->hunter_status ?? HunterApplicationStatus::DRAFT);
                
                $application->applied_channel = $channel;
                if ($isApplied && !$application->applied_at) {
                    $application->applied_at = now();
                }

                $application->suggested_subject_line = $result['suggested_subject_line'];
                $application->tailored_key_points = $result['key_selling_points'];
                $application->tailored_cover_letter = $result['cover_letter'];

                if ($initialNotes) {
                    $application->appendNotes($initialNotes);
                }

                $application->save();

                $this->newLine();
                $this->info("🎯 [JOB HUNTER] Application saved successfully to tracker!");
                $this->line("• <fg=cyan>Application ID:</> {$application->id}");
                $this->line("• <fg=cyan>Hunter Stage:</>   " . $application->hunter_status->label());
                $this->line("• <fg=cyan>AI Match Score:</>  {$application->aiGeneratedScore}%");
                $this->line("• <fg=cyan>Channel:</>        {$channel}");
                if ($application->applied_at) {
                    $this->line("• <fg=cyan>Applied At:</>     " . $application->applied_at->toDateTimeString());
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate application: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

