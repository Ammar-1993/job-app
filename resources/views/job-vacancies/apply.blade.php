<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('app.job.apply_for', ['title' => $jobVacancy->title]) }}
        </h2>
    </x-slot>

    <div class="py-fluid-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-300" x-data="{ 
        isProcessing: {{ $errors->any() ? 'false' : 'false' }}, 
        showConnectionError: false,
        feedbackMessage: '{{ __('app.job.analyzing') }}' 
    }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Back Link -->
            <a href="{{ route('job-vacancies.show', $jobVacancy->id) }}"
                class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-medium transition duration-150 inline-flex items-center mb-fluid-8 text-fluid-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ __('app.job.back_to_details') }}
            </a>

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
                <form action="{{ route('job-vacancies.process-application', $jobVacancy->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-fluid-10" 
                    @submit="
                        if (!navigator.onLine) {
                            $event.preventDefault();
                            showConnectionError = true;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            return;
                        }
                        isProcessing = true;
                    ">
                    @csrf

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-600 text-red-700 dark:text-red-100 p-6 rounded-2xl shadow-lg">
                            <p class="font-bold mb-2">Please Select or Upload Your Resume:</p>
                            <ul class="list-disc list-inside text-fluid-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Connection Error Message -->
                    <div x-show="showConnectionError" x-transition 
                        class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-600 text-red-700 dark:text-red-100 p-6 rounded-2xl shadow-lg" 
                        style="display: none;">
                        <p class="font-bold mb-1">{{ __('app.job.connection_error') }}</p>
                        <p class="text-fluid-sm">{{ __('app.job.connection_error_desc') }}</p>
                    </div>


                    <!-- Resume Selection Section -->
                    <div class="space-y-fluid-6">
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
                            hasError: {{ $errors->has('resume_file') ? 'true' : 'false' }},
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


                    <!-- Submit Button -->
                    <div class="pt-fluid-8 border-t border-gray-100 dark:border-gray-700 transition-colors duration-300">
                        <button type="submit" 
                            class="w-full relative flex items-center justify-center gap-4 py-5 rounded-2xl text-fluid-lg font-black text-white shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl overflow-hidden group disabled:cursor-not-allowed disabled:transform-none disabled:opacity-70"
                            x-bind:disabled="isProcessing"
                            x-bind:class="{ 
                                'bg-gradient-to-r from-brand-600 to-accent-600 hover:from-brand-500 hover:to-accent-500 ring-2 ring-brand-500/20 ring-offset-4 dark:ring-offset-gray-900': !isProcessing, 
                                'bg-gray-200 dark:bg-gray-700 cursor-not-allowed': isProcessing 
                            }">
                            
                            <div x-show="!isProcessing" class="absolute top-0 -left-full w-full h-full bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 group-hover:animate-shine"></div>

                            <span x-show="!isProcessing" class="flex items-center gap-3">
                                {{ __('app.job.submit_application') }}
                                <svg class="w-6 h-6 transition-transform group-hover:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                            
                            <span x-show="isProcessing" class="flex items-center gap-3">
                                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('app.job.processing') }}
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