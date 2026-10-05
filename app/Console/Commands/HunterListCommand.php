<?php

namespace App\Console\Commands;

use App\Models\JobApplication;
use Illuminate\Console\Command;

class HunterListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hunter:list 
                            {--status= : Filter by hunter stage (draft, applied, interviewing, offered, rejected, withdrawn)}
                            {--user= : Filter by user email or name}
                            {--limit=20 : Max number of applications to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List and monitor all personal Job Hunter applications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = JobApplication::personal()
            ->with(['jobVacancy.company', 'user', 'resume'])
            ->latest('updated_at');

        if ($status = $this->option('status')) {
            $query->where('hunter_status', strtolower($status));
        }

        if ($userFilter = $this->option('user')) {
            $query->whereHas('user', function ($q) use ($userFilter) {
                $q->where('email', 'like', "%{$userFilter}%")
                  ->orWhere('name', 'like', "%{$userFilter}%");
            });
        }

        $limit = (int) $this->option('limit');
        $applications = $query->take($limit)->get();

        if ($applications->isEmpty()) {
            $this->info("No personal Job Hunter applications found.");
            $this->line("Tip: Run <fg=cyan>php artisan job:match {job_id} {resume_id} --save</> to match and track a new job!");
            return Command::SUCCESS;
        }

        $this->info("================================================================================");
        $this->info("🎯 JOB HUNTER TRACKER — Personal Applications (" . $applications->count() . ")");
        $this->info("================================================================================");

        $headers = ['ID (Last 8)', 'Job Title', 'Company', 'Stage', 'Channel', 'Applied Date', 'Follow-up'];
        $rows = [];

        foreach ($applications as $app) {
            $shortId = substr($app->id, -8);
            $jobTitle = \Illuminate\Support\Str::limit($app->jobVacancy->title ?? 'N/A', 25);
            $company = \Illuminate\Support\Str::limit($app->jobVacancy->company->name ?? 'External', 18);
            
            $stageLabel = $app->hunter_status instanceof \App\Enums\HunterApplicationStatus
                ? $app->hunter_status->label()
                : ($app->hunter_status ?? 'Draft');

            $stageColored = match (strtolower($app->hunter_status instanceof \App\Enums\HunterApplicationStatus ? $app->hunter_status->value : ($app->hunter_status ?? 'draft'))) {
                'draft' => "<fg=gray>{$stageLabel}</>",
                'applied' => "<fg=blue;options=bold>{$stageLabel}</>",
                'interviewing' => "<fg=magenta;options=bold>{$stageLabel}</>",
                'offered' => "<fg=green;options=bold>{$stageLabel}</>",
                'rejected' => "<fg=red>{$stageLabel}</>",
                'withdrawn' => "<fg=yellow>{$stageLabel}</>",
                default => $stageLabel,
            };

            $channel = $app->applied_channel ?? '-';
            $appliedDate = $app->applied_at ? $app->applied_at->format('Y-m-d') : '-';
            $followUp = $app->follow_up_at ? $app->follow_up_at->format('Y-m-d') : '-';

            $rows[] = [
                $shortId,
                $jobTitle,
                $company,
                $stageColored,
                $channel,
                $appliedDate,
                $followUp,
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->line("Commands you can use:");
        $this->line("• <fg=yellow>php artisan hunter:status {id} {stage} [--notes=...]</> to advance stages");
        $this->line("• <fg=yellow>php artisan job:match {job_id} {resume_id} --save</> to generate & track new jobs");

        return Command::SUCCESS;
    }
}
