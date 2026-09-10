<?php

namespace App\Console\Commands;

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
    protected $signature = 'job:match {job_id} {resume_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match a job vacancy with a resume using AI and print the tailored cover letter';

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

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate application: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
