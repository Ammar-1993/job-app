<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('app.job.apply_for', ['title' => $jobVacancy->title]) }}
        </h2>
    </x-slot>

    <div class="py-fluid-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-300" x-data="{ 
        step: 1,
        resumeData: null,
        finalResumeId: null,
        isProcessing: false, 
        showConnectionError: false,
        feedbackMessage: '{{ __('app.job.analyzing') }}',
        previewResume() {
            this.isProcessing = true;
            this.feedbackMessage = 'Extracting resume insights...';
            
            let formData = new FormData(this.$refs.form);
            fetch('{{ route('job-vacancies.preview-resume', $jobVacancy->id) }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                this.isProcessing = false;
                if (data.success) {
                    this.resumeData = data.extracted;
                    this.finalResumeId = data.resume_id;
                    this.step = 2;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert(data.message || 'Error parsing resume');
                }
            })
            .catch(err => {
                this.isProcessing = false;
                this.showConnectionError = true;
            });
        },
        submitFinal() {
            this.isProcessing = true;
            this.feedbackMessage = 'Evaluating job fit and calculating score...';
            
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('resume_option', this.finalResumeId);
            
            fetch('{{ route('job-vacancies.process-application', $jobVacancy->id) }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    this.isProcessing = false;
                    alert(data.message || 'Error submitting application');
                }
            })
            .catch(err => {
                this.isProcessing = false;
                this.showConnectionError = true;
            });
        }
    }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Back Link -->
            <a href="{{ route('job-vacancies.show', $jobVacancy->id) }}"
                class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-medium transition duration-150 inline-flex items-center mb-fluid-8 text-fluid-sm"
                x-show="step === 1">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.job.back_to_details') }}
            </a>
            
            <button @click="step = 1; resumeData = null;" type="button"
                class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-medium transition duration-150 inline-flex items-center mb-fluid-8 text-fluid-sm"
                x-show="step === 2" style="display: none;">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Resume Selection
            </button>

            <!-- Application Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl dark:shadow-3xl rounded-2xl p-fluid-8 border border-gray-200 dark:border-indigo-700/30 transition-colors duration-300">

                <!-- Job Summary Header -->
                <div class="border-b border-gray-100 dark:border-gray-700 pb-fluid-6 mb-fluid-8">
                    <h1 class="text-fluid-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ $jobVacancy->title }}</h1>
                    <p class="text-fluid-lg text-gray-600 dark:text-gray-400 mt-2">
                        @if($jobVacancy->company)
                            <span class="font-bold text-brand-600 dark:text-brand-400">{{ $jobVacancy->company->name }}</span>
                        @endif
                    </p>
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-4 text-gray-500 dark:text-gray-300">
                        <span class="text-fluid-sm flex items-center font-medium">
                            <svg class="w-4 h-4 mr-1.5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                            {{ $jobVacancy->location }}
                        </span>
                        <span class="text-fluid-sm flex items-center font-medium">
                            <svg class="w-4 h-4 mr-1.5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 16v-4m-6-4h12"></path></svg>
                            {{ '$' . number_format($jobVacancy->salary) }}
                        </span>
                        <span class="px-4 py-1 bg-brand-600 text-white rounded-full text-fluid-xs font-bold shadow-md">{{ $jobVacancy->type }}</span>
                    </div>
                </div>

                <!-- Form -->
                <form x-ref="form" class="space-y-fluid-10" 
                    @submit.prevent="
                        if (!navigator.onLine) {
                            showConnectionError = true;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            return;
                        }
                        if (step === 1) {
                            previewResume();
                        } else {
                            submitFinal();
                        }
                    ">
                    @csrf

                    <!-- Connection Error Message -->
                    <div x-show="showConnectionError" x-transition 
                        class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-600 text-red-700 dark:text-red-100 p-6 rounded-2xl shadow-lg" 
                        style="display: none;">
                        <p class="font-bold mb-1">{{ __('app.job.connection_error') }}</p>
                        <p class="text-fluid-sm">{{ __('app.job.connection_error_desc') }}</p>
                    </div>


                    <!-- STEP 1: Resume Selection -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 delay-150" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-fluid-6">
                        <h3 class="text-fluid-xl font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 uppercase tracking-wide">{{ __('app.job.select_resume') }}</h3>

                        <!-- Existing Resumes -->
                        <fieldset class="space-y-fluid-4">
                            <legend class="text-fluid-base font-bold text-gray-700 dark:text-gray-300 mb-3">{{ __('app.job.choose_existing') }}</legend>
                            <div class="space-y-3 p-fluid-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                                @forelse($resumes as $resume)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio" name="resume_option" id="existing_{{ $resume->id }}" value="{{ $resume->id }}"
                                            class="form-radio h-5 w-5 text-brand-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-brand-500 transition-colors" />
                                        <div class="ml-4 flex-1">
                                            <p class="text-gray-900 dark:text-white font-bold group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $resume->filename }}</p>
                                            <p class="text-gray-500 dark:text-gray-400 text-fluid-xs">Updated: {{ $resume->updated_at->format('M d, Y') }}</p>
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 text-fluid-sm p-2 italic">{{ __('app.job.no_existing_resumes') }}</p>
                                @endforelse
                            </div>
                        </fieldset>
                        
                        <!-- Divider -->
                        <div class="relative flex justify-center py-fluid-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center text-fluid-xs uppercase font-black tracking-widest">
                                <span class="px-4 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500">{{ __('app.job.or') }}</span>
                            </div>
                        </div>

                        <!-- Upload New Resume -->
                        <div x-data="{ 
                            fileName: '', 
                            hasError: false,
                            errorMessage: ''
                        }">
                            <div class="flex items-center mb-fluid-4">
                                <input x-ref="newResumeRadio" type="radio" name="resume_option" id="new_resume" value="new_resume"
                                    class="form-radio h-5 w-5 text-brand-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-brand-500" />
                                <label class="ml-4 text-fluid-base font-bold text-gray-700 dark:text-gray-300 cursor-pointer" for="new_resume">{{ __('app.job.upload_new') }}</label>
                            </div>
                            
                            <label for="new_resume_file" class="block cursor-pointer">
                                <div class="border-2 border-dashed rounded-2xl p-fluid-8 text-center transition-all duration-300"
                                    x-bind:class="{ 
                                        'border-brand-500 bg-brand-50/30 dark:bg-brand-900/10 shadow-lg shadow-brand-500/10': $refs.newResumeRadio.checked, 
                                        'border-gray-200 dark:border-gray-700 hover:border-brand-400 dark:hover:border-brand-600': !$refs.newResumeRadio.checked,
                                        'border-red-500 bg-red-50 dark:bg-red-900/10 shadow-lg shadow-red-500/10': hasError 
                                    }">
                                    
                                    <input @change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            if (file.type !== 'application/pdf') {
                                                hasError = true;
                                                errorMessage = 'Only PDF files are allowed.';
                                                fileName = '';
                                                $event.target.value = '';
                                            } else if (file.size > 5 * 1024 * 1024) {
                                                hasError = true;
                                                errorMessage = 'File size must be less than 5MB.';
                                                fileName = '';
                                                $event.target.value = '';
                                            } else {
                                                fileName = file.name;
                                                $refs.newResumeRadio.checked = true;
                                                hasError = false;
                                                errorMessage = '';
                                            }
                                        } else {
                                            fileName = '';
                                        }
                                    " 
                                        type="file" name="resume_file" id="new_resume_file" class="hidden" accept="application/pdf" />
                                    
                                    <svg class="w-12 h-12 mx-auto mb-fluid-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 014 4v16a2 2 0 01-2 2H5a2 2 0 01-2-2v-5l4-4zM16 12l-4-4m4 4l-4 4m4-4h-8"></path></svg>

                                    <template x-if="!fileName">
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400 font-medium">{{ __('app.job.drag_drop') }} <span class="text-brand-600 dark:text-brand-400 font-black">{{ __('app.job.browse') }}</span>.</p>
                                            <p class="text-fluid-xs text-gray-400 dark:text-gray-500 mt-2 uppercase tracking-widest font-bold">{{ __('app.job.max_size') }}</p>
                                            <p x-show="hasError" x-text="errorMessage" class="text-red-600 dark:text-red-400 text-fluid-sm mt-3 font-bold"></p>
                                        </div>
                                    </template>

                                    <template x-if="fileName">
                                        <div class="animate-bounce">
                                            <p x-text="fileName" class="text-fluid-base text-brand-600 dark:text-brand-400 font-black"></p>
                                            <p class="text-gray-500 dark:text-gray-400 text-fluid-xs mt-1 uppercase tracking-widest font-bold">{{ __('app.job.file_ready') }}</p>
                                        </div>
                                    </template>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- STEP 2: Resume Visualization / Preview -->
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-500 delay-150" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="space-y-fluid-8">
                        <div class="bg-gradient-to-r from-brand-600/10 to-accent-600/10 dark:from-brand-900/30 dark:to-accent-900/30 border border-brand-200 dark:border-brand-800 rounded-3xl p-fluid-8 shadow-inner">
                            <div class="flex items-center gap-4 mb-fluid-6">
                                <div class="bg-brand-500 text-white p-3 rounded-2xl shadow-md">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-fluid-xl font-black text-gray-900 dark:text-white">AI Extraction Successful</h3>
                                    <p class="text-fluid-sm text-gray-600 dark:text-gray-400">Review your parsed details before final submission.</p>
                                </div>
                            </div>
                            
                            <template x-if="resumeData">
                                <div class="space-y-fluid-6">
                                    <!-- Summary -->
                                    <div class="bg-white dark:bg-gray-800/80 rounded-2xl p-fluid-6 shadow-sm border border-gray-100 dark:border-gray-700/50">
                                        <h4 class="text-fluid-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Professional Summary</h4>
                                        <div class="border-l-2 border-indigo-500 pl-4 py-1">
                                            <p class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed" x-text="resumeData.summary || 'No summary extracted.'"></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Skills Grid -->
                                    <div class="bg-white dark:bg-gray-800/80 rounded-2xl p-fluid-6 shadow-sm border border-gray-100 dark:border-gray-700/50">
                                        <h4 class="text-fluid-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Extracted Skills</h4>
                                        <div class="border-l-2 border-teal-500 pl-4 py-1 flex flex-wrap gap-3">
                                            <template x-for="skill in (resumeData.skills || [])">
                                                <span class="px-4 py-2 bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 rounded-xl text-fluid-sm font-bold border border-brand-100 dark:border-brand-800/50 shadow-sm transition-transform hover:-translate-y-1 cursor-default" x-text="skill"></span>
                                            </template>
                                            <template x-if="!(resumeData.skills && resumeData.skills.length)">
                                                <span class="text-gray-500 italic text-sm">No skills extracted.</span>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Experience -->
                                    <div class="bg-white dark:bg-gray-800/80 rounded-2xl p-fluid-6 shadow-sm border border-gray-100 dark:border-gray-700/50">
                                        <h4 class="text-fluid-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Professional Experience</h4>
                                        <template x-if="resumeData.experience && resumeData.experience.length > 0">
                                            <div class="space-y-4">
                                                <template x-for="exp in resumeData.experience">
                                                    <div class="border-l-2 border-brand-500 pl-4 py-1">
                                                        <h5 class="font-bold text-gray-900 dark:text-white" x-text="exp.job_title"></h5>
                                                        <p class="text-brand-600 dark:text-brand-400 text-sm font-medium" x-text="(exp.company || '') + ' • ' + (exp.duration || '')"></p>
                                                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-2 leading-relaxed whitespace-pre-wrap" x-text="exp.description"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!(resumeData.experience && resumeData.experience.length > 0)">
                                            <p class="text-gray-500 italic text-fluid-sm">No experience details extracted.</p>
                                        </template>
                                    </div>

                                    <!-- Education -->
                                    <div class="bg-white dark:bg-gray-800/80 rounded-2xl p-fluid-6 shadow-sm border border-gray-100 dark:border-gray-700/50">
                                        <h4 class="text-fluid-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Education</h4>
                                        <template x-if="resumeData.education && resumeData.education.length > 0">
                                            <div class="space-y-4">
                                                <template x-for="edu in resumeData.education">
                                                    <div class="border-l-2 border-accent-500 pl-4 py-1">
                                                        <h5 class="font-bold text-gray-900 dark:text-white" x-text="edu.degree"></h5>
                                                        <p class="text-accent-600 dark:text-accent-400 text-sm font-medium" x-text="(edu.institution || '') + ' • ' + (edu.graduation_year || '')"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!(resumeData.education && resumeData.education.length > 0)">
                                            <p class="text-gray-500 italic text-fluid-sm">No education details extracted.</p>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>


                    <!-- Submit Buttons -->
                    <div class="pt-fluid-8 border-t border-gray-100 dark:border-gray-700 transition-colors duration-300 flex flex-col sm:flex-row gap-4">
                        <button type="submit" 
                            class="w-full relative flex items-center justify-center gap-4 py-5 rounded-2xl text-fluid-lg font-black text-white shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl overflow-hidden group disabled:cursor-not-allowed disabled:transform-none disabled:opacity-70"
                            x-bind:disabled="isProcessing"
                            x-bind:class="{ 
                                'bg-gradient-to-r from-brand-600 to-accent-600 hover:from-brand-500 hover:to-accent-500 ring-2 ring-brand-500/20 ring-offset-4 dark:ring-offset-gray-900': !isProcessing, 
                                'bg-gray-200 dark:bg-gray-700 cursor-not-allowed': isProcessing 
                            }">
                            
                            <div x-show="!isProcessing" class="absolute top-0 -left-full w-full h-full bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 group-hover:animate-shine"></div>

                            <span x-show="!isProcessing && step === 1" class="flex items-center gap-3">
                                Preview Application
                                <svg class="w-6 h-6 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </span>
                            
                            <span x-show="!isProcessing && step === 2" class="flex items-center gap-3" style="display: none;">
                                {{ __('app.job.submit_application') }}
                                <svg class="w-6 h-6 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                            
                            <span x-show="isProcessing" class="flex items-center gap-3" style="display: none;">
                                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="step === 1 ? 'Extracting...' : 'Evaluating...'"></span>
                            </span>
                        </button>
                    </div>
                </form>

            </div>

        </div>

        <!-- Full-Screen Attractive Loading Overlay -->
        <div x-show="isProcessing" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-white/90 dark:bg-gray-900/95 backdrop-blur-xl"
            style="display: none;">
            
            <div class="ai-loader-container mb-fluid-12">
                <div class="ai-ring"></div>
                <div class="ai-ring"></div>
                <div class="ai-ring"></div>
                <div class="ai-core"></div>
            </div>

            <h3 class="text-fluid-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-accent-600 dark:from-brand-400 dark:to-accent-400 animate-pulse tracking-tight">
                <span x-text="feedbackMessage"></span>
            </h3>
            
            <p class="mt-fluid-4 text-gray-500 dark:text-gray-400 text-fluid-lg font-medium tracking-wide uppercase">
                {{ __('app.job.analyzing') }}
            </p>
            
            <div class="w-80 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full mt-fluid-8 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-brand-600 to-accent-600 dark:from-brand-500 dark:to-accent-500 w-1/2 animate-[progress_2s_ease-in-out_infinite]"></div>
            </div>

        </div>
    </div>
</x-app-layout>