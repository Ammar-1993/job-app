<?php

namespace App\Console\Commands;

use App\Enums\HunterApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Console\Command;

class HunterStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hunter:status {application_id} {status}
                            {--notes= : Add or append notes to this application}
                            {--channel= : Update application channel}
                            {--follow-up= : Set follow-up date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stage, notes, or follow-up date of a personal Job Hunter application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appId = $this->argument('application_id');
        $targetStatus = strtolower($this->argument('status'));

        // Match either full UUID or ends-with partial ID for convenience
        $application = JobApplication::where('id', $appId)
            ->orWhere('id', 'like', "%{$appId}")
            ->first();

        if (!$application) {
            $this->error("Job application with ID '{$appId}' not found.");
            return Command::FAILURE;
        }

        // Validate status enum
        $matchedEnum = null;
        foreach (HunterApplicationStatus::cases() as $case) {
            if ($case->value === $targetStatus || strtolower($case->name) === $targetStatus) {
                $matchedEnum = $case;
                break;
            }
        }

        if (!$matchedEnum) {
            $available = implode(', ', array_column(HunterApplicationStatus::cases(), 'value'));
            $this->error("Invalid status '{$targetStatus}'. Available stages: {$available}");
            return Command::FAILURE;
        }

        // Apply state transition
        $oldStatus = $application->hunter_status instanceof HunterApplicationStatus
            ? $application->hunter_status->label()
            : ($application->hunter_status ?? 'Draft');

        $notes = $this->option('notes');

        match ($matchedEnum) {
            HunterApplicationStatus::DRAFT => $application->hunter_status = HunterApplicationStatus::DRAFT,
            HunterApplicationStatus::APPLIED => $application->markAsApplied(now(), $this->option('channel')),
            HunterApplicationStatus::INTERVIEWING => $application->markAsInterviewing($notes),
            HunterApplicationStatus::OFFERED => $application->markAsOffered($notes),
            HunterApplicationStatus::REJECTED => $application->markAsRejected($notes),
            HunterApplicationStatus::WITHDRAWN => $application->markAsWithdrawn($notes),
        };

        if ($notes && !in_array($matchedEnum, [
            HunterApplicationStatus::INTERVIEWING,
            HunterApplicationStatus::OFFERED,
            HunterApplicationStatus::REJECTED,
            HunterApplicationStatus::WITHDRAWN
        ])) {
            $application->appendNotes($notes);
        }

        if ($channel = $this->option('channel')) {
            $application->applied_channel = $channel;
        }

        if ($followUp = $this->option('follow-up')) {
            try {
                $application->follow_up_at = \Carbon\Carbon::parse($followUp);
            } catch (\Exception $e) {
                $this->warn("Could not parse follow-up date '{$followUp}'. Expected format: YYYY-MM-DD");
            }
        }

        $application->save();

        $this->info("=================================================");
        $this->info("✅ [JOB HUNTER] Application Updated Successfully!");
        $this->info("=================================================");
        $this->line("• <fg=cyan>Job:</>          " . ($application->jobVacancy->title ?? 'N/A'));
        $this->line("• <fg=cyan>Company:</>      " . ($application->jobVacancy->company->name ?? 'External'));
        $this->line("• <fg=cyan>Previous Stage:</> {$oldStatus}");
        $this->line("• <fg=cyan>New Stage:</>      " . $application->hunter_status->label());
        if ($application->applied_at) {
            $this->line("• <fg=cyan>Applied At:</>     " . $application->applied_at->toDateTimeString());
        }
        if ($application->follow_up_at) {
            $this->line("• <fg=cyan>Follow-up Date:</> " . $application->follow_up_at->format('Y-m-d'));
        }
        if ($notes) {
            $this->line("• <fg=cyan>Latest Note:</>    {$notes}");
        }

        return Command::SUCCESS;
    }
}
