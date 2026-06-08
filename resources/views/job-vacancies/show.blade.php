<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ $jobVacancy->title }}
        </h2>
    </x-slot>

    @php
        // Simulate Employer Branding by generating a consistent color based on company name
        $companyName = $jobVacancy->company ? $jobVacancy->company->name : 'Unknown';
        $colors = ['from-brand-500 to-indigo-600', 'from-accent-500 to-rose-600', 'from-emerald-500 to-teal-600', 'from-amber-500 to-orange-600', 'from-violet-500 to-purple-600'];
        $hash = crc32($companyName);
        $brandGradient = $colors[$hash % count($colors)];
        $initials = collect(explode(' ', $companyName))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('');
    @endphp

    <div class="py-fluid-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl dark:shadow-3xl rounded-2xl border border-gray-200 dark:border-gray-700/50 transition-colors duration-300 overflow-hidden relative">

                <!-- Dynamic Branded Header Banner -->
                <div class="h-32 sm:h-48 w-full bg-gradient-to-r {{ $brandGradient }} relative">
                    <div class="absolute inset-0 bg-black/10"></div> <!-- Slight overlay -->
                </div>

                <div class="p-6 sm:p-fluid-8 relative">
                    <!-- Company Logo/Initials Badge overlapping the header -->
                    <div class="absolute -top-16 sm:-top-20 left-6 sm:left-fluid-8 w-20 h-20 sm:w-24 sm:h-24 bg-white dark:bg-gray-900 rounded-2xl shadow-xl flex items-center justify-center border-4 border-white dark:border-gray-800 transform rotate-3 hover:rotate-0 transition-transform duration-300 z-10">
                        <span class="text-2xl sm:text-3xl font-black bg-clip-text text-transparent bg-gradient-to-br {{ $brandGradient }}">{{ $initials }}</span>
                    </div>

                    <!-- Back Link (Pushed down slightly to account for the logo) -->
                    <div class="mt-8 sm:mt-12 flex justify-between items-center mb-fluid-8">
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium transition duration-150 inline-flex items-center text-fluid-sm bg-gray-100 dark:bg-gray-700/50 px-4 py-2 rounded-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Back to Job Listings
                        </a>
                    </div>

                <!-- Job Header -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center border-b border-gray-100 dark:border-gray-700 pb-fluid-6 mb-fluid-8">
                    <div>
                        <h1 class="text-fluid-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">{{ $jobVacancy->title }}</h1>

                        <p class="text-fluid-lg text-gray-600 dark:text-gray-400 mt-2">
                            @if($jobVacancy->company)
                                <span class="font-bold text-gray-900 dark:text-white">{{ $jobVacancy->company->name }}</span>
                            @endif
                        </p>

                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-4 text-gray-500 dark:text-gray-300">
                            <div class="flex items-center text-fluid-base">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                <span class="font-medium">{{ $jobVacancy->location }}</span>
                            </div>
                            <div class="flex items-center text-fluid-base">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 16v-4m-6-4h12"></path></svg>
                                <span class="font-medium">{{ '$' . number_format($jobVacancy->salary) }}</span>
                            </div>
                            <span class="px-4 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-fluid-xs font-bold">{{ $jobVacancy->type }}</span>
                        </div>
                    </div>

                    <div class="mt-6 lg:mt-0 flex-shrink-0">
                        <a href="{{ route('job-vacancies.apply', $jobVacancy->id) }}"
                           class="inline-flex items-center justify-center text-fluid-base font-bold bg-gradient-to-r {{ $brandGradient }} text-white rounded-2xl px-10 py-4 shadow-xl transition duration-500 ease-in-out transform hover:scale-[1.02] active:scale-[0.98]">
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
                            
                            <div class="bg-gray-50 dark:bg-gray-900/70 rounded-2xl p-fluid-6 space-y-fluid-4 border border-gray-100 dark:border-gray-700/50 shadow-sm transition-colors duration-300 relative overflow-hidden">
                                <!-- Subtle brand gradient accent line -->
                                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r {{ $brandGradient }}"></div>

                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Published Date</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-base font-medium">{{ $jobVacancy->created_at->format('M d, Y') }}</p>
                                </div>
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Company</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-base font-bold">
                                        @if($jobVacancy->company)
                                            {{ $jobVacancy->company->name }}
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Location</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-base font-medium">{{ $jobVacancy->location }}</p>
                                </div>
                                
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                    <p class="text-fluid-xs font-bold text-gray-400 uppercase">Salary</p>
                                    <p class="text-gray-900 dark:text-white text-fluid-lg font-black">{{ '$' . number_format($jobVacancy->salary) }}</p>
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
                </div> <!-- End Inner Content padding wrapper -->
            </div>
        </div>
    </div>
</x-app-layout>