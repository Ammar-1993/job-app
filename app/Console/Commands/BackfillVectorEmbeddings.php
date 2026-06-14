<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobVacancy;
use App\Models\Resume;
use OpenAI\Laravel\Facades\OpenAI;

class BackfillVectorEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-embeddings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfills vector embeddings for all existing Job Vacancies and Resumes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Backfilling Resumes...');
        $resumes = Resume::whereNull('vector_embedding')->get();
        foreach ($resumes as $resume) {
            try {
                $textToEmbed = json_encode([
                    'summary' => $resume->summary,
                    'skills' => $resume->skills,
                    'experience' => $resume->experience,
                    'education' => $resume->education
                ]);
                $response = OpenAI::embeddings()->create([
                    'model' => 'text-embedding-3-small',
                    'input' => $textToEmbed,
                ]);
                $resume->update([
                    'vector_embedding' => json_encode($response->embeddings[0]->embedding)
                ]);
                $this->info("Generated embedding for Resume ID: {$resume->id}");
            } catch (\Exception $e) {
                $this->error("Failed to generate embedding for Resume ID: {$resume->id} - {$e->getMessage()}");
            }
        }

        $this->info('Backfilling Job Vacancies...');
        $jobs = JobVacancy::whereNull('vector_embedding')->get();
        foreach ($jobs as $job) {
            try {
                $textToEmbed = json_encode([
                    'title' => $job->title,
                    'description' => $job->description,
                    'location' => $job->location,
                    'type' => $job->type,
                ]);
                $response = OpenAI::embeddings()->create([
                    'model' => 'text-embedding-3-small',
                    'input' => $textToEmbed,
                ]);
                $job->update([
                    'vector_embedding' => json_encode($response->embeddings[0]->embedding)
                ]);
                $this->info("Generated embedding for JobVacancy ID: {$job->id}");
            } catch (\Exception $e) {
                $this->error("Failed to generate embedding for JobVacancy ID: {$job->id} - {$e->getMessage()}");
            }
        }

        $this->info('Backfill completed successfully!');
    }
}
