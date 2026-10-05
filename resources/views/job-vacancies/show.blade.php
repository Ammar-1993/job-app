<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 flex shrink-0 items-center justify-center bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-300 hover:text-brand-600 dark:hover:text-brand-400 rounded-xl transition-colors shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-xl sm:text-2xl text-gray-900 dark:text-white tracking-tight truncate max-w-2xl">
                        {{ $jobVacancy->title }}
                    </h2>
                </div>
            </div>
            <a href="{{ route('job-vacancies.apply', $jobVacancy->id) }}"
               class="hidden sm:inline-flex items-center justify-center text-sm font-bold bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 shadow-sm hover:shadow-md hover:shadow-brand-500/20 transition-all duration-200 transform hover:-translate-y-0.5">
                Apply Now
                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>
    </x-slot>

    @php
        $companyName = $jobVacancy->company ? $jobVacancy->company->name : 'Company';
        $initials = collect(explode(' ', $companyName))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('');
    @endphp

    <div class="py-fluid-8 bg-slate-50/60 dark:bg-zinc-950/40 transition-colors duration-300">
        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-3xl border border-gray-200/80 dark:border-zinc-800 transition-colors duration-300 overflow-hidden relative">

                <!-- Subtle Ambient Banner -->
                <div class="h-20 sm:h-24 w-full bg-gradient-to-r from-brand-500/10 via-indigo-500/10 to-transparent dark:from-brand-500/15 dark:via-indigo-500/10 dark:to-transparent border-b border-gray-100 dark:border-zinc-800/80 relative"></div>

                <div class="p-6 sm:p-8 sm:pt-4 relative">
                    <!-- Company Logo/Initials Badge overlapping the header -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 pb-6 border-b border-gray-100 dark:border-zinc-800 -mt-12 sm:-mt-14 mb-8">
                        <div class="flex items-end gap-4">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white dark:bg-zinc-800 rounded-2xl shadow-md flex items-center justify-center border-2 border-white dark:border-zinc-700 shrink-0">
                                <span class="text-xl sm:text-2xl font-black text-brand-600 dark:text-brand-400">{{ $initials }}</span>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                                    {{ $jobVacancy->title }}
                                </h1>
                                <p class="text-base font-bold text-gray-700 dark:text-zinc-300 mt-1">
                                    {{ $companyName }}
                                </p>
                            </div>
                        </div>

                        <!-- Apply Button on Desktop -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('job-vacancies.apply', $jobVacancy->id) }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center text-sm font-bold bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-8 py-3.5 shadow-md shadow-brand-500/25 transition-all duration-200 transform hover:-translate-y-0.5 active:scale-95">
                                Apply Now
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Metadata Pills Bar -->
                    <div class="flex flex-wrap items-center gap-3 mb-8">
                        <!-- Location -->
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-200/80 dark:border-zinc-700/80 shadow-xs">
                            <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span>{{ $jobVacancy->location }}</span>
                        </div>

                        <!-- Salary -->
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 16v-4m-6-4h12"></path></svg>
                            <span>{{ is_numeric($jobVacancy->salary) ? '$' . number_format($jobVacancy->salary) : ($jobVacancy->salary ?: 'Not specified') }}</span>
                        </div>

                        <!-- Type -->
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/60 shadow-xs">
                            <span>💼 {{ $jobVacancy->type }}</span>
                        </div>

                        @if($jobVacancy->source_platform)
                            <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/60 shadow-xs">
                                <span>🔗 {{ $jobVacancy->source_platform }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Content Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <!-- Main Column: Description -->
                        <div class="lg:col-span-2">
                            <h2 class="text-lg font-black text-gray-900 dark:text-white border-b border-gray-100 dark:border-zinc-800 pb-3 mb-6 tracking-tight">
                                {{ __('Job Description') }}
                            </h2>

                            <div class="text-gray-700 dark:text-zinc-200 leading-relaxed space-y-4 text-sm sm:text-base max-w-none [&>ul]:list-disc [&>ul]:list-outside [&>ul]:pl-5 [&>ul]:mb-6 [&>ul>li]:mb-1 [&>p]:mb-4 [&>p:last-child]:mb-0 [&>h1]:text-xl [&>h1]:font-black [&>h1]:mb-4 [&>h1]:text-gray-900 dark:[&>h1]:text-white [&>h2]:text-lg [&>h2]:font-black [&>h2]:mb-3 [&>h2]:text-gray-900 dark:[&>h2]:text-white [&>h3]:text-base [&>h3]:font-bold [&>h3]:mb-2 [&>h3]:text-gray-900 dark:[&>h3]:text-white [&_strong]:font-bold [&_strong]:text-gray-900 dark:[&_strong]:text-white">
                                {!! Str::markdown($jobVacancy->description ?? '') !!}
                            </div>
                            
                            <!-- Bottom CTA -->
                            <div class="mt-12 bg-gradient-to-br from-brand-50/80 to-indigo-50/40 dark:from-zinc-800/90 dark:to-brand-950/30 border border-brand-200/80 dark:border-zinc-700/80 p-6 sm:p-8 rounded-2xl shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Ready to apply for this role?</h3>
                                    <p class="text-gray-500 dark:text-zinc-400 text-sm mt-0.5">Submit your tailored application in seconds.</p>
                                </div>
                                <a href="{{ route('job-vacancies.apply', $jobVacancy->id) }}"
                                   class="w-full sm:w-auto inline-flex items-center justify-center text-sm font-bold bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-8 py-3.5 shadow-md shadow-brand-500/20 transition-all duration-200 transform hover:-translate-y-0.5">
                                    Apply Now
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Sidebar Column: Overview -->
                        <div class="lg:col-span-1">
                            <div class="lg:sticky lg:top-8">
                                <h2 class="text-lg font-black text-gray-900 dark:text-white border-b border-gray-100 dark:border-zinc-800 pb-3 mb-6 tracking-tight">
                                    {{ __('Job Overview') }}
                                </h2>
                                
                                <div class="bg-gray-50/80 dark:bg-zinc-800/60 rounded-2xl p-6 space-y-4 border border-gray-200/80 dark:border-zinc-700/80 shadow-xs transition-colors duration-300 w-full">
                                    
                                    <div class="flex justify-between items-center border-b border-gray-200/80 dark:border-zinc-700/70 pb-3">
                                        <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Published Date</p>
                                        <p class="text-gray-900 dark:text-zinc-100 text-sm font-semibold">{{ $jobVacancy->created_at->format('M d, Y') }}</p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center border-b border-gray-200/80 dark:border-zinc-700/70 pb-3">
                                        <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Company</p>
                                        <p class="text-gray-900 dark:text-zinc-100 text-sm font-bold">
                                            {{ $companyName }}
                                        </p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center border-b border-gray-200/80 dark:border-zinc-700/70 pb-3">
                                        <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Location</p>
                                        <p class="text-gray-900 dark:text-zinc-100 text-sm font-semibold">{{ $jobVacancy->location }}</p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center border-b border-gray-200/80 dark:border-zinc-700/70 pb-3">
                                        <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Salary</p>
                                        <p class="text-emerald-600 dark:text-emerald-400 text-sm font-black">{{ is_numeric($jobVacancy->salary) ? '$' . number_format($jobVacancy->salary) : ($jobVacancy->salary ?: 'Not specified') }}</p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center border-b border-gray-200/80 dark:border-zinc-700/70 pb-3">
                                        <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Employment Type</p>
                                        <p class="text-gray-900 dark:text-zinc-100 text-sm font-semibold">{{ $jobVacancy->type }}</p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Category</p>
                                        <p class="text-gray-900 dark:text-zinc-100 text-sm font-semibold">
                                            {{ $jobVacancy->jobCategory->name ?? 'Tech' }}
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