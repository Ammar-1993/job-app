<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Spatie\PdfToText\Pdf;

class ResumeAnalysisService
{
    public function extractResumeInformation(string $fileUrl)
    {
        try {
            // Extract raw text from the resume pdf file (read pdf file, and get the text)
            $rawText = $this->extractTextFromPdf($fileUrl);

            Log::debug('Successfully extracted text from pdf file' . strlen($rawText) . ' characters');

            // Use OpenAI API to organize the text into a structured format
                $response = $this->callOpenAiWithRetriesAndFallback([
                    // model will be set by the fallback caller
                'messages' => [
                    [
                        'role' => 'system',
                            'content' => 'You are a precise resume parser. Extract information exactly as it appears in the resume without adding any interpretation or additional information. The output should be in JSON format.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Parse the following resume content and extract the information as a JSON Object with the exact keys: 'summary', 'skills', 'experience', 'education'.
Important constraints:
- 'summary' MUST be a single string summarizing the profile.
- 'skills' MUST be an array of strings (e.g. [\"PHP\", \"Laravel\"]).
- 'experience' MUST be an array of objects. Each object must have keys like 'job_title', 'company', 'duration', and 'description'.
- 'education' MUST be an array of objects. Each object must have keys like 'degree', 'institution', and 'graduation_year'.
If a section is entirely missing from the resume, return an empty array [] for it (or empty string for summary).
The resume content is: {$rawText}"
                    ]
                ],
                'response_format' => [
                    'type' => 'json_object'
                ],
                'temperature' => 0.1  // Sets the randomness of the AI response to 0, making it deterministic and focused on the most likely completion
                ], ['gpt-4o', 'gpt-4', 'gpt-3.5-turbo']);

            $result = $response->choices[0]->message->content;
            Log::debug('OpenAI response: ' . $result);

            $parsedResult = $this->extractFirstJson($result);

            if ($parsedResult === null) {
                Log::error('Failed to parse OpenAI response: unable to find valid JSON in response');
                throw new \Exception('Failed to parse OpenAI response');
            }

            // Validate the parsed result
            $requiredKeys = ['summary', 'skills', 'experience', 'education'];
            $missingKeys = array_diff($requiredKeys, array_keys($parsedResult));

            if (count($missingKeys) > 0) {
                Log::error('Missing required keys: ' . implode(', ', $missingKeys));
                throw new \Exception('Missing required keys in the parsed result');
            }

            // Return the JSON object
            return [
                'summary' => $parsedResult['summary'] ?? '',
                'skills' => $parsedResult['skills'] ?? [],
                'experience' => $parsedResult['experience'] ?? [],
                'education' => $parsedResult['education'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('Error extracting resume information: ' . $e->getMessage() . ' | trace: ' . $e->getTraceAsString());
            return [
                'summary' => '',
                'skills' => [],
                'experience' => [],
                'education' => []
            ];
        }
    }

    public function analyzeResume($jobVacancy, $resumeData) {
        try { 
            $jobDetails = json_encode([
                'job_title' => $jobVacancy->title,
                'job_description' => $jobVacancy->description,
                'job_location' => $jobVacancy->location,
                'job_type' => $jobVacancy->type,
                'job_salary' => $jobVacancy->salary,
            ]);

            $resumeDetails = json_encode($resumeData);

            $response = $this->callOpenAiWithRetriesAndFallback([
                // model will be set by the fallback caller
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an expert HR professional and job recruiter. You are given a job vacancy and a resume. 
                                      Your task is to analyze the resume and determine if the candidate is a good fit for the job. 
                                      The output MUST be in JSON format with exactly two keys: 'aiGeneratedScore' (integer 0-100) and 'aiGeneratedFeedback' (string).
                                      The 'aiGeneratedFeedback' MUST be written entirely in ENGLISH language and formatted in Markdown using bullet points. Structure it exactly as follows:
                                      
                                      **🟢 Strengths:**
                                      * [Strength 1]
                                      * [Strength 2]

                                      **🔴 Gaps & Areas for Improvement:**
                                      * [Gap 1]
                                      * [Gap 2]

                                      **💡 Verdict:**
                                      [A brief summary of the candidate's suitability]"
                    ],
                    [   
                        'role' => 'user',
                        'content' => "Please evalute this job application. Job Details: {$jobDetails}. Resume Details: {$resumeDetails}"
                    ]
                ],
                'response_format' => [
                    'type' => 'json_object'
                ],
                'temperature' => 0.1
            ], ['gpt-4o', 'gpt-4', 'gpt-3.5-turbo']);

            $result = $response->choices[0]->message->content;
            Log::debug('OpenAI evaluationresponse: ' . $result);

            $parsedResult = $this->extractFirstJson($result);

            if ($parsedResult === null) {
                Log::error('Failed to parse OpenAI response: unable to find valid JSON in response');
                throw new \Exception('Failed to parse OpenAI response');
            }

            if(!isset($parsedResult['aiGeneratedScore']) || !isset($parsedResult['aiGeneratedFeedback'])) {
                Log::error('Missing required keys in the parsed result');
                throw new \Exception('Missing required keys in the parsed result');
            }

            return $parsedResult;
   
        } catch (\Exception $e) {
            Log::error('Error analyzing resume: ' . $e->getMessage());
            return [
                'aiGeneratedScore' => 0,
                'aiGeneratedFeedback' => 'An error occurred while analyzing the resume. Please try again later.'
            ];
        }
    }


    /**
     * توليد خطاب تغطية ونقاط بيع مخصصة لوظيفة محددة.
     *
     * تأخذ هذه الدالة نفس المدخلات التي تأخذها analyzeResume() وتُنتج:
     *   - cover_letter: خطاب تغطية جاهز للإرسال، مخصص للوظيفة والمرشح
     *   - key_selling_points: مصفوفة من 3-5 نقاط قوة مختارة ذكياً
     *   - suggested_subject_line: سطر موضوع مقترح للإيميل
     *
     * @param  mixed $jobVacancy  كائن يحتوي على title/description/location/type/salary
     * @param  array $resumeData  مصفوفة من extractResumeInformation() أو ما يعادلها
     * @return array{cover_letter: string, key_selling_points: array, suggested_subject_line: string}
     */
    public function generateTailoredApplication($jobVacancy, array $resumeData): array
    {
        $fallback = [
            'cover_letter'          => '',
            'key_selling_points'    => [],
            'suggested_subject_line' => '',
        ];

        try {
            $jobDetails = json_encode([
                'job_title'       => $jobVacancy->title,
                'job_description' => $jobVacancy->description,
                'job_location'    => $jobVacancy->location,
                'job_type'        => $jobVacancy->type,
                'job_salary'      => $jobVacancy->salary,
            ], JSON_UNESCAPED_UNICODE);

            // نُقلّص الـ resume data لتجنب تجاوز حد الـ tokens
            $resumeSummary = json_encode([
                'summary'    => $resumeData['summary']    ?? '',
                'skills'     => $resumeData['skills']     ?? [],
                'experience' => array_slice($resumeData['experience'] ?? [], 0, 4),
                'education'  => $resumeData['education']  ?? [],
            ], JSON_UNESCAPED_UNICODE);

            $candidateName = $resumeData['name'] ?? 'Ammar Al-Najjar';

            $response = $this->callOpenAiWithRetriesAndFallback([
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => "You are an expert career coach and technical recruiter who writes highly
personalized, compelling job application materials for software engineers.
Your cover letters:
- Open with a strong, specific hook tied to the company/role (NOT generic openers like 'I am writing to apply')
- Highlight 2-3 concrete projects or achievements that directly map to the job requirements
- Use the STAR format subtly (Situation, Task, Action, Result) for key points
- Sound human, confident, and enthusiastic — never robotic or desperate
- Are appropriately concise (3-4 paragraphs, ~250-350 words)
- Close with a clear, confident call to action

Output MUST be valid JSON with exactly these keys:
  'cover_letter': string (the full cover letter text, plain text with paragraph breaks using \\n\\n)
  'key_selling_points': array of 3-5 strings (each a concise bullet point strength)
  'suggested_subject_line': string (for the application email)"
                    ],
                    [
                        'role'    => 'user',
                        'content' => "Write a tailored job application package for the following:

CANDIDATE NAME: {$candidateName}

JOB DETAILS:
{$jobDetails}

CANDIDATE RESUME DATA:
{$resumeSummary}

Requirements:
- The cover letter must reference specific skills/projects from the resume that match the job description
- Key selling points should be 3-5 of the candidate's strongest matches for THIS specific job
- The subject line should include the job title and a differentiator
- Write everything in ENGLISH"
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature'     => 0.6, // أعلى قليلاً من التحليل لخطاب أكثر طبيعية وإنسانية
            ], ['gpt-4o', 'gpt-4', 'gpt-3.5-turbo']);

            $result       = $response->choices[0]->message->content;
            $parsedResult = $this->extractFirstJson($result);

            if ($parsedResult === null) {
                Log::error('generateTailoredApplication: Failed to parse OpenAI JSON response');
                return $fallback;
            }

            $requiredKeys = ['cover_letter', 'key_selling_points', 'suggested_subject_line'];
            foreach ($requiredKeys as $key) {
                if (! isset($parsedResult[$key])) {
                    Log::error("generateTailoredApplication: Missing key '{$key}' in response");
                    return $fallback;
                }
            }

            Log::info('generateTailoredApplication: Successfully generated application for job: ' . $jobVacancy->title);

            return [
                'cover_letter'           => trim($parsedResult['cover_letter']),
                'key_selling_points'     => (array) $parsedResult['key_selling_points'],
                'suggested_subject_line' => trim($parsedResult['suggested_subject_line']),
            ];

        } catch (\Exception $e) {
            Log::error('generateTailoredApplication: Error — ' . $e->getMessage());
            return $fallback;
        }
    }


    public function generateEmbedding(string $text): array
    {
        try {
            $response = OpenAI::embeddings()->create([
                'model' => 'text-embedding-3-small',
                'input' => $text,
            ]);

            return $response->embeddings[0]->embedding;
        } catch (\Exception $e) {
            Log::error('Error generating embedding: ' . $e->getMessage());
            return [];
        }
    }

    private function extractTextFromPdf(string $fileUrl): string
    {
        // Reading the file from the cloud to local disk storage in temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'resume');

        $filePath = parse_url($fileUrl, PHP_URL_PATH);
        if (!$filePath) {
            throw new \Exception('Invalid file URL');
        }

        $filename = basename($filePath);

        $storagePath = "resumes/{$filename}";

        if (!Storage::disk('cloud')->exists($storagePath)) {
            throw new \Exception('File not found');
        }

        $pdfContent = Storage::disk('cloud')->get($storagePath);
        if (!$pdfContent) {
            throw new \Exception('Failed to read file');
        }

        file_put_contents($tempFile, $pdfContent);

        // Check if pdf-to-text is installed
        $pdfToTextPath = ['/opt/homebrew/bin/pdftotext', '/usr/bin/pdftotext', '/usr/local/bin/pdftotext'];
        $pdfToTextAvailable = false;

        foreach ($pdfToTextPath as $path) {
            if (file_exists($path)) {
                $pdfToTextAvailable = true;
                break;
            }
        }

        if (!$pdfToTextAvailable) {
            throw new \Exception('pdf-to-text is not installed');
        }

        // Extract text from the pdf file
        $instance = new Pdf();
        $instance->setPdf($tempFile);
        $text = $instance->text();

        // Clean up the temp file
        unlink($tempFile);

        return $text;
    }

    /**
     * Call OpenAI chat.create with retries on rate limit or temporary failures.
     * Returns the response object on success or throws the last exception on failure.
     */
    private function callOpenAiWithRetries(array $payload)
    {
        $maxAttempts = 3;
        $attempt = 0;
        $backoffSeconds = 1;

        while ($attempt < $maxAttempts) {
            try {
                return OpenAI::chat()->create($payload);
            } catch (\Throwable $e) {
                $attempt++;

                $message = strtolower($e->getMessage() ?? '');
                $code = (int) $e->getCode();

                $isRateLimit = ($code === 429) || str_contains($message, 'rate limit') || str_contains($message, 'too many requests');

                Log::warning("OpenAI request failed (attempt {$attempt}/{$maxAttempts}) - code: {$code}, message: {$e->getMessage()}");

                if ($attempt >= $maxAttempts || ! $isRateLimit) {
                    throw $e;
                }

                // jittered backoff: base seconds + random milliseconds
                $jitterMs = rand(0, 500);
                $sleepMicro = ($backoffSeconds * 1000000) + ($jitterMs * 1000);
                usleep($sleepMicro);
                $backoffSeconds *= 2;
            }
        }

        throw new \RuntimeException('OpenAI request failed after retries');
    }

    /**
     * Try a list of models as fallback. For each model, call the retrying requester.
     */
    private function callOpenAiWithRetriesAndFallback(array $payload, array $models)
    {
        $lastException = null;

        foreach ($models as $model) {
            $payload['model'] = $model;
            try {
                Log::info("Trying OpenAI model: {$model}");
                return $this->callOpenAiWithRetries($payload);
            } catch (\Throwable $e) {
                $lastException = $e;
                $msg = strtolower($e->getMessage() ?? '');
                $code = (int) $e->getCode();

                $isRateLimit = ($code === 429) || str_contains($msg, 'rate limit') || str_contains($msg, 'too many requests');
                $isModelNotFound = str_contains($msg, 'model') && (str_contains($msg, 'not found') || str_contains($msg, 'does not exist') || str_contains($msg, 'is not available') || str_contains($msg, 'unknown model'));

                Log::warning("Model {$model} failed: {$e->getMessage()}");

                if ($isModelNotFound) {
                    // try next model immediately
                    continue;
                }

                if ($isRateLimit) {
                    // wait a little before trying the next model
                    sleep(1);
                    continue;
                }

                // for other errors, break and rethrow after loop
                break;
            }
        }

        if ($lastException) {
            throw $lastException;
        }

        throw new \RuntimeException('OpenAI request failed using all fallback models');
    }

    /**
     * Extract the first JSON object or array found in a string.
     * Returns associative array on success or null on failure.
     */
    private function extractFirstJson(string $text): ?array
    {
        $start = null;
        $stack = [];
        $len = strlen($text);

        for ($i = 0; $i < $len; $i++) {
            $char = $text[$i];
            if ($char === '{' || $char === '[') {
                if (empty($stack)) {
                    $start = $i;
                }
                $stack[] = $char;
            } elseif ($char === '}' || $char === ']') {
                if (empty($stack)) {
                    continue;
                }
                $last = array_pop($stack);
                if (($last === '{' && $char !== '}') || ($last === '[' && $char !== ']')) {
                    $stack = [];
                    $start = null;
                    continue;
                }
                if (empty($stack) && $start !== null) {
                    $jsonText = substr($text, $start, $i - $start + 1);
                    $decoded = json_decode($jsonText, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return $decoded;
                    }
                    $start = null;
                }
            }
        }

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return null;
    }
}