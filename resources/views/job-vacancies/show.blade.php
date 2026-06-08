<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ $jobVacancy->title }}
        </h2>
    </x-slot>

    <div class="py-fluid-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl dark:shadow-3xl rounded-2xl p-6 sm:p-fluid-8 border border-gray-200 dark:border-indigo-700/50 transition-colors duration-300">

                <!-- Back Link -->
                <a href="{{ route('dashboard') }}" class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-medium transition duration-150 inline-flex items-center mb-fluid-8 text-fluid-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Job Listings
                </a>

                <!-- Job Header -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center border-b border-gray-100 dark:border-gray-700 pb-fluid-6 mb-fluid-8">
                    <div>
                        <h1 class="text-fluid-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ $jobVacancy->title }}</h1>

                        <p class="text-fluid-lg text-gray-600 dark:text-gray-400 mt-2">
                            @if($jobVacancy->company)
                                <span class="font-bold text-brand-600 dark:text-brand-400">{{ $jobVacancy->company->name }}</span>
                            @endif
                        </p>

                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-4 text-gray-500 dark:text-gray-300">
                            <div class="flex items-center text-fluid-base">
                                <svg class="w-4 h-4 mr-1.5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                <span class="font-medium">{{ $jobVacancy->location }}</span>
                            </div>
                            <div class="flex items-center text-fluid-base">
                                <svg class="w-4 h-4 mr-1.5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 16v-4m-6-4h12"></path></svg>
                                <span class="font-medium">{{ '$' . number_format($jobVacancy->salary) }}</span>
                            </div>
                            <span class="px-4 py-1 bg-brand-600 text-white rounded-full text-fluid-xs font-bold shadow-md ring-2 ring-offset-2 ring-brand-500 ring-offset-white dark:ring-offset-gray-800">{{ $jobVacancy->type }}</span>
                        </div>
                    </div>

                    <div class="mt-6 lg:mt-0 flex-shrink-0">
                        <a href="{{ route('job-vacancies.apply', $jobVacancy->id) }}"
                           class="inline-flex items-center justify-center text-fluid-base font-bold bg-gradient-to-r from-brand-600 to-accent-600 text-white rounded-2xl px-10 py-4 shadow-xl shadow-brand-500/20 transition duration-500 ease-in-out hover:shadow-brand-500/40 transform hover:scale-[1.02] active:scale-[0.98]">
                            Apply Now
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Content Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-fluid-8 mt-fluid-8">
                    
                    <div class="lg:col-span-2">
                        <h2 class="text-fluid-xl font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3 mb-6 uppercase tracking-wider">Job Description</h2>
                        <div class="text-gray-600 dark:text-gray-300 leading-relaxed space-y-4 text-fluid-base max-w-none">
                            <p>{!! nl2br(e($jobVacancy->description)) !!}</p>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-1">
                        <div class="lg:sticky lg:top-8">
                            <h2 class="text-fluid-xl font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3 mb-6 uppercase tracking-wider">Job Overview</h2>
                            
                            <div class="bg-gray-50 dark:bg-gray-900/70 rounded-2xl p-fluid-6 space-y-fluid-4 border border-gray-100 dark:border-indigo-700/50 shadow-sm transition-colors duration-300">
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Published Date</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-base font-medium">{{ $jobVacancy->created_at->format('M d, Y') }}</p>
                                </div>
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Company</p>
                                    <p class="text-brand-600 dark:text-brand-400 text-fluid-base font-bold">
                                        @if($jobVacancy->company)
                                            <a href="#" class="hover:underline">{{ $jobVacancy->company->name }}</a>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Location</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-base font-medium">{{ $jobVacancy->location }}</p>
                                </div>
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Salary</p>
                                    <p class="text-emerald-600 dark:text-emerald-400 text-fluid-lg font-black">{{ '$' . number_format($jobVacancy->salary) }}</p>
                                </div>
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Employment Type</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-base font-medium">{{ $jobVacancy->type }}</p>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Category</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-base font-medium">
                                        {{ $jobVacancy->jobCategory->name ?? 'Uncategorized' }}
                                    </p>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>