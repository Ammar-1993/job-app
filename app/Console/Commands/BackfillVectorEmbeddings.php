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
    protected $signature = 'app:backfill-embeddings {--force : Re-embed ALL rows (use after changing the embedding text format)} {--jobs-only : Re-embed only Job Vacancies} {--resumes-only : Re-embed only Resumes}';

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
        if (! $this->option('jobs-only')) {
            $this->info('Backfilling Resumes...');
            $resumes = ($this->option('force') ? Resume::query() : Resume::whereNull('vector_embedding'))->get();
            foreach ($resumes as $resume) {
            try {
                $textToEmbed = \App\Support\EmbeddingText::forResume([
                    'summary' => $resume->summary,
                    'skills' => $resume->skills,
                    'experience' => $resume->experience,
                    'education' => $resume->education
                ]);
                $response = OpenAI::embeddings()->create([
                    'model' => 'text-embedding-3-small',
                    'input' => $textToEmbed,
                ]);
                $resume->forceFill([
                    'vector_embedding' => json_encode($response->embeddings[0]->embedding)
                ])->saveQuietly();
                $this->info("Generated embedding for Resume ID: {$resume->id}");
            } catch (\Exception $e) {
                $this->error("Failed to generate embedding for Resume ID: {$resume->id} - {$e->getMessage()}");
            }
        }
        }

        if (! $this->option('resumes-only')) {
            $this->info('Backfilling Job Vacancies...');
            $jobs = ($this->option('force') ? JobVacancy::query() : JobVacancy::whereNull('vector_embedding'))->get();
            foreach ($jobs as $job) {
                try {
                    $textToEmbed = \App\Support\EmbeddingText::forJob([
                        'title' => $job->title,
                        'description' => $job->description,
                        'location' => $job->location,
                        'type' => $job->type,
                    ]);
                    $response = OpenAI::embeddings()->create([
                        'model' => 'text-embedding-3-small',
                        'input' => $textToEmbed,
                    ]);
                    $job->forceFill([
                        'vector_embedding' => json_encode($response->embeddings[0]->embedding)
                    ])->saveQuietly();
                    $this->info("Generated embedding for JobVacancy ID: {$job->id}");
                } catch (\Exception $e) {
                    $this->error("Failed to generate embedding for JobVacancy ID: {$job->id} - {$e->getMessage()}");
                }
            }
        }

        $this->info('Backfill completed successfully!');
    }
}
