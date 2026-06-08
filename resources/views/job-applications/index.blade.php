<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('Activity Feed') }}
        </h2>
    </x-slot>

    <!-- Success Message -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-fluid-6">
            <div class="bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 p-4 rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="py-fluid-8 transition-colors duration-300">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative">
                
                <!-- Timeline vertical line (Hidden on small screens for cleaner look) -->
                <div class="hidden sm:block absolute left-8 top-8 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-800"></div>

                <div class="space-y-fluid-6">
                    @forelse ($jobApplications as $jobApplication)
                        @php
                            $status = $jobApplication->status;
                            $statusConfig = match ($status) {
                                'pending' => [
                                    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'amber',
                                    'bg' => 'bg-amber-100 dark:bg-amber-900/40',
                                    'text' => 'text-amber-700 dark:text-amber-400',
                                    'border' => 'border-amber-200 dark:border-amber-800',
                                    'pulse' => true,
                                ],
                                'accepted' => [
                                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'emerald',
                                    'bg' => 'bg-emerald-100 dark:bg-emerald-900/40',
                                    'text' => 'text-emerald-700 dark:text-emerald-400',
                                    'border' => 'border-emerald-200 dark:border-emerald-800',
                                    'pulse' => false,
                                ],
                                'rejected' => [
                                    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'red',
                                    'bg' => 'bg-red-100 dark:bg-red-900/40',
                                    'text' => 'text-red-700 dark:text-red-400',
                                    'border' => 'border-red-200 dark:border-red-800',
                                    'pulse' => false,
                                ],
                                default => [
                                    'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'gray',
                                    'bg' => 'bg-gray-100 dark:bg-gray-800',
                                    'text' => 'text-gray-700 dark:text-gray-300',
                                    'border' => 'border-gray-200 dark:border-gray-700',
                                    'pulse' => false,
                                ],
                            };
                        @endphp

                        <div class="relative sm:pl-24">
                            <!-- Timeline Dot -->
                            <div class="hidden sm:flex absolute left-4 top-6 w-8 h-8 rounded-full items-center justify-center border-4 border-white dark:border-gray-900 {{ $statusConfig['bg'] }} z-10 shadow-sm">
                                <svg class="w-4 h-4 {{ $statusConfig['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $statusConfig['icon'] }}"></path>
                                </svg>
                            </div>

                            <!-- Glassmorphic Notification Card -->
                            <div class="glass-panel rounded-3xl p-fluid-6 hover:scale-[1.01] transition-transform duration-300 relative overflow-hidden">
                                
                                @if($statusConfig['pulse'])
                                    <div class="absolute top-0 right-0 p-4">
                                        <span class="flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                        </span>
                                    </div>
                                @endif

                                <div class="flex flex-col md:flex-row gap-fluid-6">
                                    
                                    <!-- Primary Info -->
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-fluid-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                                {{ $jobApplication->created_at->diffForHumans() }}
                                            </span>
                                            <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }} border px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm">
                                                {{ $status }}
                                            </span>
                                        </div>
                                        
                                        <h3 class="text-fluid-xl font-black text-gray-900 dark:text-white mb-1">
                                            {{ $jobApplication->jobVacancy?->title ?? 'Job Removed' }}
                                        </h3>
                                        
                                        <p class="text-fluid-base text-gray-600 dark:text-gray-400 font-medium flex items-center">
                                            @if(isset($jobApplication->jobVacancy) && $jobApplication->jobVacancy && isset($jobApplication->jobVacancy->company) && $jobApplication->jobVacancy->company)
                                                <svg class="w-4 h-4 mr-1.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                {{ $jobApplication->jobVacancy->company->name }}
                                            @else
                                                <span class="text-red-500">Company Deleted</span>
                                            @endif
                                            <span class="mx-3 text-gray-300 dark:text-gray-600">•</span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                                {{ $jobApplication->jobVacancy->location ?? 'Unknown' }}
                                            </span>
                                        </p>
                                    </div>

                                    <!-- AI Score & Actions -->
                                    <div class="flex md:flex-col items-center md:items-end justify-between gap-4 md:gap-2 shrink-0 border-t md:border-t-0 md:border-l border-gray-200 dark:border-gray-700 pt-4 md:pt-0 md:pl-fluid-6">
                                        <div class="text-center md:text-right">
                                            <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">AI Match</p>
                                            <div class="inline-flex items-baseline text-fluid-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-accent-600 dark:from-brand-400 dark:to-accent-400">
                                                {{ $jobApplication->aiGeneratedScore }}<span class="text-sm font-bold text-gray-500 dark:text-gray-400 ml-1">%</span>
                                            </div>
                                        </div>

                                        @php
                                            $cloudDisk = Storage::disk('cloud');
                                        @endphp
                                        @if(isset($jobApplication->resume) && $jobApplication->resume && $jobApplication->resume->fileUri)
                                            <a href="{{ $cloudDisk->url($jobApplication->resume->fileUri) }}" target="_blank" 
                                               class="inline-flex items-center text-fluid-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 px-4 py-2 rounded-xl transition-colors shadow-sm">
                                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                View Resume
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- AI Feedback Expandable Section -->
                                <div x-data="{ expanded: false }" class="mt-fluid-4 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                                    <button @click="expanded = !expanded" class="flex items-center text-fluid-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        View AI Analysis Feedback
                                        <svg class="w-4 h-4 ml-1 transform transition-transform duration-300" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div x-show="expanded" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-4 p-fluid-4 bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800" style="display: none;">
                                        <p class="text-fluid-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                            {{ $jobApplication->aiGeneratedFeedback }}
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="text-center p-fluid-12 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="w-24 h-24 mx-auto mb-6 bg-brand-50 dark:bg-brand-900/30 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h3 class="text-fluid-2xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">No Activity Yet</h3>
                            <p class="text-fluid-base text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Your application timeline is empty. Head over to the dashboard to find matching jobs and kickstart your career journey.</p>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center bg-brand-600 hover:bg-brand-700 text-white font-bold text-fluid-base px-8 py-3 rounded-xl shadow-lg shadow-brand-500/30 transition-all transform hover:-translate-y-1">
                                Explore Jobs
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    @endforelse

                </div>

                <!-- Pagination -->
                <div class="mt-fluid-10">
                    {{ $jobApplications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>