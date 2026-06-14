<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Resume;
use Illuminate\Http\Request;
use App\Models\JobVacancy;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

use App\Http\Requests\ApplyJobRequest;
use App\Services\ResumeAnalysisService;
class JobVacancyController extends Controller
{
    protected $resumeAnalysisService;

    public function __construct(ResumeAnalysisService $resumeAnalysisService)
    {
        $this->resumeAnalysisService = $resumeAnalysisService;
    }

    public function show(string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        $jobVacancy->increment('viewCount');
        return view('job-vacancies.show', compact('jobVacancy'));
    }

    public function apply(string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);

        // Prevent accessing the application form if already applied
        if (JobApplication::where('jobVacancyId', $id)->where('userId', auth()->id())->exists()) {
            return redirect()->route('job-vacancies.show', $id)->with('error', 'You have already applied for this job.');
        }

        $resumes = auth()->user()->resumes;
        return view('job-vacancies.apply', compact('jobVacancy', 'resumes'));
    }

    public function previewResume(Request $request, string $id)
    {
        try {
            if ($request->input('resume_option') === 'new_resume') {
                $request->validate(['resume_file' => 'required|file|mimes:pdf|max:5120']);
                $file = $request->file('resume_file');
                $extension = $file->getClientOriginalExtension();
                $originalFileName = $file->getClientOriginalName();
                $fileName = 'resume_' . time() . '.' . $extension;

                $path = Storage::disk('cloud')->putFileAs('resumes', $file, $fileName, 'public');
                $fileUrl = config('filesystems.disks.cloud.url') . '/' . $path;

                $extractedInfo = $this->resumeAnalysisService->extractResumeInformation($fileUrl);

                $resumeTextToEmbed = json_encode([
                    'summary' => $extractedInfo['summary'],
                    'skills' => $extractedInfo['skills'],
                    'experience' => $extractedInfo['experience'],
                    'education' => $extractedInfo['education']
                ]);
                $vectorEmbedding = json_encode($this->resumeAnalysisService->generateEmbedding($resumeTextToEmbed));

                $resume = Resume::create([
                    'fileName' => $originalFileName,
                    'fileUrl' => $path,
                    'userId' => auth()->id(),
                    'contactDetails' => json_encode([
                        'name' => auth()->user()->name,
                        'email' => auth()->user()->email,
                    ]),
                    'summary' => $extractedInfo['summary'],
                    'skills' => $extractedInfo['skills'],
                    'experience' => $extractedInfo['experience'],
                    'education' => $extractedInfo['education'],
                    'vector_embedding' => $vectorEmbedding
                ]);

                return response()->json([
                    'success' => true,
                    'resume_id' => $resume->id,
                    'extracted' => [
                        'skills' => is_string($resume->skills) ? (json_decode($resume->skills) ?? $resume->skills) : $resume->skills,
                        'experience' => is_string($resume->experience) ? (json_decode($resume->experience) ?? $resume->experience) : $resume->experience,
                        'summary' => $resume->summary,
                        'education' => is_string($resume->education) ? (json_decode($resume->education) ?? $resume->education) : $resume->education,
                    ]
                ]);
            } else {
                $request->validate(['resume_option' => 'required|exists:resumes,id']);
                $resume = Resume::findOrFail($request->input('resume_option'));
                return response()->json([
                    'success' => true,
                    'resume_id' => $resume->id,
                    'extracted' => [
                        'skills' => is_string($resume->skills) ? (json_decode($resume->skills) ?? $resume->skills) : $resume->skills,
                        'experience' => is_string($resume->experience) ? (json_decode($resume->experience) ?? $resume->experience) : $resume->experience,
                        'summary' => $resume->summary,
                        'education' => is_string($resume->education) ? (json_decode($resume->education) ?? $resume->education) : $resume->education,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function processApplication(ApplyJobRequest $request, string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);

        // Prevent API submission if already applied
        if (JobApplication::where('jobVacancyId', $id)->where('userId', auth()->id())->exists()) {
            return response()->json(['success' => false, 'message' => 'You have already applied for this job.'], 400);
        }

        $resumeId = $request->input('resume_option'); // This will now always be an ID because of the preview step
        
        $resume = Resume::findOrFail($resumeId);

        $extractedInfo = [
            'summary' => $resume->summary,
            'skills' => $resume->skills,
            'experience' => $resume->experience,
            'education' => $resume->education
        ];

        // Evalute Job Application
        $evaluation = $this->resumeAnalysisService->analyzeResume($jobVacancy, $extractedInfo);

        JobApplication::create([
            'status' => 'pending',
            'jobVacancyId' => $id,
            'resumeId' => $resumeId,
            'userId' => auth()->id(),
            'aiGeneratedScore' => $evaluation['aiGeneratedScore'],
            'aiGeneratedFeedback' => $evaluation['aiGeneratedFeedback'],
        ]);
        
        return response()->json(['success' => true, 'redirect' => route('job-applications.index')]);
    }

 
    public function toggleSave(string $id)
    {
        $user = auth()->user();
        $jobVacancy = JobVacancy::findOrFail($id);

        if ($user->savedJobs()->where('jobVacancyId', $id)->exists()) {
            $user->savedJobs()->detach($id);
            $message = 'Job removed from saved list.';
        } else {
            $user->savedJobs()->attach($id);
            $message = 'Job saved successfully.';
        }

        return back()->with('success', $message);
    }
}
