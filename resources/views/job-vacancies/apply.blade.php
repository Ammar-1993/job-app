<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('job-vacancies.show', $jobVacancy->id) }}" class="w-10 h-10 flex shrink-0 items-center justify-center bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 hover:text-brand-600 dark:hover:text-brand-400 rounded-xl transition-colors shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-xl sm:text-2xl text-gray-900 dark:text-white tracking-tight truncate max-w-2xl">
                    {{ __('app.job.apply_for', ['title' => $jobVacancy->title]) }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-fluid-8 bg-slate-50/60 dark:bg-zinc-950/40 transition-colors duration-300" x-data="{ 
        step: 1,
        resumeData: null,
        finalResumeId: null,
        isProcessing: false, 
        showConnectionError: false,
        feedbackMessage: '{{ __('app.job.analyzing') }}',
        selectedOption: '',
        fileName: '',
        hasError: false,
        errorMessage: '',
        get isStep1Valid() {
            return this.selectedOption && (this.selectedOption !== 'new_resume' || this.fileName !== '');
        },
        get isButtonDisabled() {
            if (this.isProcessing) return true;
            if (this.step === 1) return !this.isStep1Valid;
            return false;
        },
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

            <!-- Back Links -->
            <div class="mb-6">
                <a href="{{ route('job-vacancies.show', $jobVacancy->id) }}"
                    class="inline-flex items-center text-xs font-bold text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-white bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 px-3.5 py-1.5 rounded-xl shadow-xs transition-colors"
                    x-show="step === 1">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('app.job.back_to_details') }}
                </a>
                
                <button @click="step = 1; resumeData = null;" type="button"
                    class="inline-flex items-center text-xs font-bold text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-white bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 px-3.5 py-1.5 rounded-xl shadow-xs transition-colors"
                    x-show="step === 2" style="display: none;">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Resume Selection
                </button>
            </div>

            <!-- Application Card -->
            <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-3xl p-6 sm:p-10 border border-gray-200/80 dark:border-zinc-800 transition-colors duration-300">

                <!-- Job Summary Header -->
                <div class="border-b border-gray-100 dark:border-zinc-800 pb-6 mb-8">
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                        {{ $jobVacancy->title }}
                    </h1>
                    <p class="text-base font-bold text-gray-700 dark:text-zinc-300 mt-1">
                        @if($jobVacancy->company)
                            <span>{{ $jobVacancy->company->name }}</span>
                        @endif
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-2.5 mt-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-200/80 dark:border-zinc-700/80 shadow-xs">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                            {{ $jobVacancy->location }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 16v-4m-6-4h12"></path></svg>
                            {{ is_numeric($jobVacancy->salary) ? '$' . number_format($jobVacancy->salary) : ($jobVacancy->salary ?: 'Not specified') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/60 shadow-xs">
                            💼 {{ $jobVacancy->type }}
                        </span>
                    </div>
                </div>

                <!-- Form -->
                <form x-ref="form" class="space-y-8" 
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
                        class="bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 p-5 rounded-2xl shadow-sm" 
                        style="display: none;">
                        <p class="font-bold mb-1">{{ __('app.job.connection_error') }}</p>
                        <p class="text-sm">{{ __('app.job.connection_error_desc') }}</p>
                    </div>


                    <!-- STEP 1: Resume Selection -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" class="space-y-6">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white border-b border-gray-100 dark:border-zinc-800 pb-3 tracking-tight uppercase">
                            {{ __('app.job.select_resume') }}
                        </h3>

                        <!-- Existing Resumes -->
                        <fieldset class="space-y-4">
                            <legend class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
                                {{ __('app.job.choose_existing') }}
                            </legend>
                            
                            <div class="space-y-3">
                                @forelse($resumes as $resume)
                                    <label class="flex items-center cursor-pointer group p-4 rounded-2xl border transition-all duration-200"
                                        x-bind:class="selectedOption == '{{ $resume->id }}' 
                                            ? 'border-brand-500 bg-brand-50/70 dark:bg-brand-950/40 ring-2 ring-brand-500/20 shadow-xs' 
                                            : 'border-gray-200/80 dark:border-zinc-700/80 bg-gray-50/70 dark:bg-zinc-800/50 hover:border-brand-300 dark:hover:border-zinc-600'">
                                        <input type="radio" name="resume_option" x-model="selectedOption" id="existing_{{ $resume->id }}" value="{{ $resume->id }}"
                                            class="form-radio h-5 w-5 text-brand-600 bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 focus:ring-brand-500" />
                                        <div class="ml-4 flex-1">
                                            <p class="text-gray-900 dark:text-white font-bold group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                                                {{ $resume->filename }}
                                            </p>
                                            <p class="text-gray-500 dark:text-zinc-400 text-xs mt-0.5">
                                                Updated: {{ $resume->updated_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-gray-500 dark:text-zinc-400 text-sm p-4 bg-gray-50 dark:bg-zinc-800/40 rounded-2xl border border-gray-200 dark:border-zinc-700 italic">
                                        {{ __('app.job.no_existing_resumes') }}
                                    </p>
                                @endforelse
                            </div>
                        </fieldset>
                        
                        <!-- Divider -->
                        <div class="relative flex justify-center py-3">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-zinc-800"></div>
                            </div>
                            <div class="relative flex justify-center text-xs uppercase font-black tracking-widest">
                                <span class="px-4 bg-white dark:bg-zinc-900 text-gray-400 dark:text-zinc-500">{{ __('app.job.or') }}</span>
                            </div>
                        </div>

                        <!-- Upload New Resume -->
                        <div>
                            <div class="flex items-center mb-3">
                                <input x-ref="newResumeRadio" type="radio" name="resume_option" x-model="selectedOption" id="new_resume" value="new_resume"
                                    class="form-radio h-5 w-5 text-brand-600 bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 focus:ring-brand-500" />
                                <label class="ml-3 text-sm font-bold text-gray-700 dark:text-zinc-300 cursor-pointer" for="new_resume">
                                    {{ __('app.job.upload_new') }}
                                </label>
                            </div>
                            
                            <label for="new_resume_file" class="block cursor-pointer">
                                <div class="border-2 border-dashed rounded-2xl p-6 sm:p-8 text-center transition-all duration-200 relative"
                                    @dragover.prevent="$el.classList.add('border-brand-500', 'bg-brand-50/50', 'dark:bg-brand-950/30')"
                                    @dragleave.prevent="$el.classList.remove('border-brand-500', 'bg-brand-50/50', 'dark:bg-brand-950/30')"
                                    @drop.prevent="
                                        $el.classList.remove('border-brand-500', 'bg-brand-50/50', 'dark:bg-brand-950/30');
                                        const file = $event.dataTransfer.files[0];
                                        if (file) {
                                            const input = $refs.newResumeFile;
                                            const dt = new DataTransfer();
                                            dt.items.add(file);
                                            input.files = dt.files;
                                            input.dispatchEvent(new Event('change'));
                                        }
                                    "
                                    x-bind:class="{ 
                                        'border-brand-500 bg-brand-50/30 dark:bg-brand-950/20 shadow-xs': $refs.newResumeRadio.checked && !hasError, 
                                        'border-gray-200/90 dark:border-zinc-700/80 bg-gray-50/50 dark:bg-zinc-800/30 hover:border-brand-400 dark:hover:border-zinc-600': !$refs.newResumeRadio.checked && !hasError,
                                        'border-red-500 bg-red-50/50 dark:bg-red-950/20': hasError 
                                    }">
                                    
                                    <input x-ref="newResumeFile" @change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            $refs.newResumeRadio.checked = true;
                                            selectedOption = 'new_resume';
                                            if (file.type !== 'application/pdf') {
                                                hasError = true;
                                                errorMessage = 'Invalid format! Only PDF files are supported.';
                                                fileName = '';
                                                $event.target.value = '';
                                            } else if (file.size > 5 * 1024 * 1024) {
                                                hasError = true;
                                                errorMessage = 'File too large! Maximum size is 5MB.';
                                                fileName = '';
                                                $event.target.value = '';
                                            } else {
                                                fileName = file.name;
                                                hasError = false;
                                                errorMessage = '';
                                            }
                                        } else {
                                            fileName = '';
                                        }
                                    " 
                                        type="file" name="resume_file" id="new_resume_file" class="hidden" accept="application/pdf" />
                                    
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center border border-brand-100 dark:border-brand-800/60 shadow-xs">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 014 4v16a2 2 0 01-2 2H5a2 2 0 01-2-2v-5l4-4zM16 12l-4-4m4 4l-4 4m4-4h-8"></path></svg>
                                    </div>

                                    <template x-if="!fileName">
                                        <div>
                                            <p class="text-gray-700 dark:text-zinc-300 font-medium text-sm">
                                                {{ __('app.job.drag_drop') }} <span class="text-brand-600 dark:text-brand-400 font-bold underline">{{ __('app.job.browse') }}</span>
                                            </p>
                                            <p class="text-xs text-gray-400 dark:text-zinc-500 mt-1 uppercase tracking-wider font-semibold">
                                                {{ __('app.job.max_size') }}
                                            </p>
                                            
                                            <div x-show="hasError" x-transition class="mt-4 flex items-center justify-center text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 py-2 px-4 rounded-xl inline-flex font-bold text-xs border border-rose-200 dark:border-rose-800/50">
                                                <svg class="w-4 h-4 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span x-text="errorMessage"></span>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="fileName">
                                        <div>
                                            <p x-text="fileName" class="text-base text-brand-600 dark:text-brand-400 font-bold"></p>
                                            <p class="text-gray-500 dark:text-zinc-400 text-xs mt-1 uppercase tracking-wider font-semibold">
                                                {{ __('app.job.file_ready') }}
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- STEP 2: Resume Visualization / Preview -->
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" style="display: none;" class="space-y-6">
                        
                        <!-- AI Header Banner -->
                        <div class="bg-gradient-to-br from-brand-50/80 to-indigo-50/40 dark:from-zinc-800/90 dark:to-brand-950/40 border border-brand-200/80 dark:border-zinc-700/80 rounded-2xl p-6 shadow-xs flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-brand-600 text-white flex items-center justify-center shadow-md shadow-brand-500/20 shrink-0">
                                <span class="text-xl">⚡</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">AI Extraction Successful</h3>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Review your parsed details before final submission.</p>
                            </div>
                        </div>
                        
                        <template x-if="resumeData">
                            <div class="space-y-4">
                                <!-- Summary -->
                                <div class="bg-gray-50/80 dark:bg-zinc-800/60 rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-700/80 shadow-xs">
                                    <h4 class="text-xs font-black text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                        <span>📝</span> Professional Summary
                                    </h4>
                                    <p class="text-gray-700 dark:text-zinc-200 text-sm leading-relaxed" x-text="resumeData.summary || 'No summary extracted.'"></p>
                                </div>
                                
                                <!-- Skills Grid -->
                                <div class="bg-gray-50/80 dark:bg-zinc-800/60 rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-700/80 shadow-xs">
                                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                        <span>⚡</span> Extracted Skills
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="skill in (resumeData.skills || [])">
                                            <span class="px-3 py-1 bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 rounded-xl text-xs font-bold border border-brand-200/70 dark:border-brand-800/60 shadow-xs cursor-default" x-text="skill"></span>
                                        </template>
                                        <template x-if="!(resumeData.skills && resumeData.skills.length)">
                                            <span class="text-gray-500 italic text-xs">No skills extracted.</span>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Experience -->
                                <div class="bg-gray-50/80 dark:bg-zinc-800/60 rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-700/80 shadow-xs">
                                    <h4 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                        <span>💼</span> Professional Experience
                                    </h4>
                                    <template x-if="resumeData.experience && resumeData.experience.length > 0">
                                        <div class="space-y-4">
                                            <template x-for="exp in resumeData.experience">
                                                <div class="border-l-2 border-emerald-500 pl-4 py-1">
                                                    <h5 class="font-bold text-gray-900 dark:text-white text-sm" x-text="exp.job_title"></h5>
                                                    <p class="text-emerald-600 dark:text-emerald-400 text-xs font-semibold mt-0.5" x-text="(exp.company || '') + ' • ' + (exp.duration || '')"></p>
                                                    <p class="text-gray-600 dark:text-zinc-300 text-xs mt-1.5 leading-relaxed whitespace-pre-wrap" x-text="exp.description"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!(resumeData.experience && resumeData.experience.length > 0)">
                                        <p class="text-gray-500 italic text-xs">No experience details extracted.</p>
                                    </template>
                                </div>

                                <!-- Education -->
                                <div class="bg-gray-50/80 dark:bg-zinc-800/60 rounded-2xl p-5 border border-gray-200/80 dark:border-zinc-700/80 shadow-xs">
                                    <h4 class="text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                        <span>🎓</span> Education
                                    </h4>
                                    <template x-if="resumeData.education && resumeData.education.length > 0">
                                        <div class="space-y-3">
                                            <template x-for="edu in resumeData.education">
                                                <div class="border-l-2 border-amber-500 pl-4 py-1">
                                                    <h5 class="font-bold text-gray-900 dark:text-white text-sm" x-text="edu.degree"></h5>
                                                    <p class="text-amber-600 dark:text-amber-400 text-xs font-semibold mt-0.5" x-text="(edu.institution || '') + ' • ' + (edu.graduation_year || '')"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!(resumeData.education && resumeData.education.length > 0)">
                                        <p class="text-gray-500 italic text-xs">No education details extracted.</p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>


                    <!-- Submit Buttons -->
                    <div class="pt-6 border-t border-gray-100 dark:border-zinc-800 transition-colors duration-300">
                        <button type="submit" 
                            class="w-full flex items-center justify-center gap-3 py-4 rounded-xl text-base font-bold shadow-md transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95 disabled:cursor-not-allowed disabled:transform-none"
                            x-bind:disabled="isButtonDisabled"
                            x-bind:class="{ 
                                'bg-brand-600 hover:bg-brand-500 text-white shadow-brand-500/25': !isButtonDisabled, 
                                'bg-gray-100 dark:bg-zinc-800 text-gray-400 dark:text-zinc-500 border border-gray-200 dark:border-zinc-700 shadow-none hover:shadow-none': isButtonDisabled 
                            }">

                            <span x-show="!isProcessing && step === 1" class="flex items-center gap-2">
                                Preview Application
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                            
                            <span x-show="!isProcessing && step === 2" class="flex items-center gap-2" style="display: none;">
                                {{ __('app.job.submit_application') }}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                            
                            <span x-show="isProcessing" class="flex items-center gap-2" style="display: none;">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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

        <!-- Loading Overlay -->
        <div x-show="isProcessing" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-white/95 dark:bg-zinc-950/95 backdrop-blur-md"
            style="display: none;">
            
            <div class="ai-loader-container mb-8">
                <div class="ai-ring"></div>
                <div class="ai-ring"></div>
                <div class="ai-ring"></div>
                <div class="ai-core"></div>
            </div>

            <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
                <span x-text="feedbackMessage"></span>
            </h3>
            
            <p class="mt-2 text-xs font-bold text-gray-500 dark:text-zinc-400 tracking-wider uppercase">
                {{ __('app.job.analyzing') }}
            </p>
            
            <div class="w-64 h-1.5 bg-gray-100 dark:bg-zinc-800 rounded-full mt-6 overflow-hidden">
                <div class="h-full bg-brand-600 dark:bg-brand-500 w-1/2 animate-[progress_2s_ease-in-out_infinite]"></div>
            </div>

        </div>
    </div>
</x-app-layout>