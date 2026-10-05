<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex shrink-0 items-center justify-center bg-brand-50 dark:bg-brand-950/60 border border-brand-200/80 dark:border-brand-800/80 rounded-2xl shadow-2xs text-brand-600 dark:text-brand-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <div>
                <h2 class="font-black text-2xl text-gray-900 dark:text-white tracking-tight">
                    {{ __('Activity Feed') }}
                </h2>
                <p class="text-xs font-medium text-gray-500 dark:text-zinc-400 mt-0.5">Track your submitted applications and AI evaluations</p>
            </div>
        </div>
    </x-slot>

    <!-- Success Message -->
    @if (session('success'))
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 p-4 rounded-2xl shadow-2xs border border-emerald-200/80 dark:border-emerald-800/80 flex items-center">
                <svg class="w-5 h-5 mr-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="py-fluid-8 transition-colors duration-300">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative">
                
                <!-- Timeline vertical line (Hidden on small screens for cleaner look) -->
                <div class="hidden md:block absolute left-8 top-8 bottom-0 w-0.5 bg-gray-200 dark:bg-zinc-800"></div>

                <div class="space-y-6">
                    @forelse ($jobApplications as $jobApplication)
                        @php
                            $statusValue = $jobApplication->status instanceof \BackedEnum ? $jobApplication->status->value : (string) $jobApplication->status;
                            $status = strtolower($statusValue);
                            $statusConfig = match ($status) {
                                'pending' => [
                                    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'amber',
                                    'bg' => 'bg-amber-50 dark:bg-amber-950/60',
                                    'text' => 'text-amber-700 dark:text-amber-400',
                                    'border' => 'border-amber-200/80 dark:border-amber-800/80',
                                    'pulse' => true,
                                ],
                                'accepted' => [
                                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'emerald',
                                    'bg' => 'bg-emerald-50 dark:bg-emerald-950/60',
                                    'text' => 'text-emerald-700 dark:text-emerald-400',
                                    'border' => 'border-emerald-200/80 dark:border-emerald-800/80',
                                    'pulse' => false,
                                ],
                                'rejected' => [
                                    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'rose',
                                    'bg' => 'bg-rose-50 dark:bg-rose-950/60',
                                    'text' => 'text-rose-700 dark:text-rose-400',
                                    'border' => 'border-rose-200/80 dark:border-rose-800/80',
                                    'pulse' => false,
                                ],
                                default => [
                                    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'color' => 'amber',
                                    'bg' => 'bg-amber-50 dark:bg-amber-950/60',
                                    'text' => 'text-amber-700 dark:text-amber-400',
                                    'border' => 'border-amber-200/80 dark:border-amber-800/80',
                                    'pulse' => true,
                                ],
                            };
                            
                            $score = (int) ($jobApplication->aiGeneratedScore ?? 0);
                            $scoreColorText = $score >= 80 ? 'text-emerald-500' : ($score >= 50 ? 'text-amber-500' : 'text-rose-500');
                            $scoreColorBg = $score >= 80 ? 'bg-emerald-500' : ($score >= 50 ? 'bg-amber-500' : 'bg-rose-500');

                            $rawFeedback = $jobApplication->aiGeneratedFeedback ?? '';
                            $hasStructuredSections = preg_match('/\*{0,2}🟢?\s*Strengths:?\*{0,2}/i', $rawFeedback) 
                                && preg_match('/\*{0,2}🔴?\s*Gaps/i', $rawFeedback);

                            $strengthsBullets = [];
                            $gapsBullets = [];
                            $verdictText = '';

                            if ($hasStructuredSections) {
                                if (preg_match('/\*{0,2}🟢?\s*Strengths:?\*{0,2}\s*(.*?)(?=\*{0,2}🔴?\s*Gaps|\$)/is', $rawFeedback, $mStrengths)) {
                                    $strengthsBullets = array_values(array_filter(array_map('trim', preg_split('/^\s*[\*\-•]\s*/m', trim($mStrengths[1] ?? ''), -1, PREG_SPLIT_NO_EMPTY))));
                                }
                                if (preg_match('/\*{0,2}🔴?\s*Gaps[^:]*:?\*{0,2}\s*(.*?)(?=\*{0,2}💡?\s*Verdict|\$)/is', $rawFeedback, $mGaps)) {
                                    $gapsBullets = array_values(array_filter(array_map('trim', preg_split('/^\s*[\*\-•]\s*/m', trim($mGaps[1] ?? ''), -1, PREG_SPLIT_NO_EMPTY))));
                                }
                                if (preg_match('/\*{0,2}💡?\s*Verdict:?\*{0,2}\s*(.*)/is', $rawFeedback, $mVerdict)) {
                                    $verdictText = trim($mVerdict[1] ?? '');
                                }
                            }
                        @endphp

                        <div class="relative md:pl-24">
                            <!-- Timeline Dot -->
                            <div class="hidden md:flex absolute left-4 top-6 w-8 h-8 rounded-full items-center justify-center border-4 border-white dark:border-zinc-950 {{ $statusConfig['bg'] }} z-10 shadow-sm">
                                <svg class="w-4 h-4 {{ $statusConfig['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $statusConfig['icon'] }}"></path>
                                </svg>
                            </div>

                            <!-- Modern Application Card -->
                            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-gray-200/90 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden">
                                
                                @if($statusConfig['pulse'])
                                    <div class="absolute top-6 right-6 group cursor-help">
                                        <span class="flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-{{ $statusConfig['color'] }}-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-{{ $statusConfig['color'] }}-500"></span>
                                        </span>
                                        <div class="absolute right-0 top-6 opacity-0 group-hover:opacity-100 transition-opacity bg-zinc-900 dark:bg-zinc-800 text-white dark:text-zinc-200 border border-transparent dark:border-zinc-700/60 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-lg whitespace-nowrap pointer-events-none z-20 shadow-lg">
                                            Active Application
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-col md:flex-row gap-6 items-start">
                                    
                                    <!-- Primary Info -->
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-2.5 mb-2.5 flex-wrap">
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-zinc-500">
                                                {{ $jobApplication->created_at->diffForHumans() }}
                                            </span>
                                            <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }} border px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                                {{ $statusValue }}
                                            </span>
                                            @if($jobApplication->isHunter())
                                                <span class="bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/80 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow-2xs">
                                                    🎯 Job Hunter
                                                </span>
                                                @if($jobApplication->hunter_status)
                                                    <span class="{{ $jobApplication->hunter_status->badgeClasses() }} border px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-2xs">
                                                        {{ $jobApplication->hunter_status->label() }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        
                                        <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">
                                            {{ $jobApplication->jobVacancy?->title ?? 'Job Removed' }}
                                        </h3>
                                        
                                        <div class="text-sm font-semibold text-gray-600 dark:text-zinc-400 flex items-center flex-wrap gap-y-1">
                                            @if(isset($jobApplication->jobVacancy) && $jobApplication->jobVacancy && isset($jobApplication->jobVacancy->company) && $jobApplication->jobVacancy->company)
                                                <span class="flex items-center text-gray-800 dark:text-zinc-300 font-bold">
                                                    <svg class="w-4 h-4 mr-1.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                    {{ $jobApplication->jobVacancy->company->name }}
                                                </span>
                                            @else
                                                <span class="text-rose-500">Company Deleted</span>
                                            @endif
                                            <span class="mx-3 text-gray-300 dark:text-zinc-700">•</span>
                                            <span class="flex items-center text-gray-500 dark:text-zinc-400 font-medium">
                                                <svg class="w-4 h-4 mr-1 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                                {{ $jobApplication->jobVacancy->location ?? 'Remote' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- AI Score & Actions -->
                                    <div class="flex md:flex-col items-center md:items-end justify-between gap-4 md:gap-3 shrink-0 border-t md:border-t-0 md:border-l border-gray-200/80 dark:border-zinc-800 pt-4 md:pt-0 md:pl-6 w-full md:w-auto">
                                        <div class="text-left md:text-right w-full md:w-36">
                                            <div class="flex items-center justify-between md:justify-end gap-2 mb-1.5">
                                                <p class="text-[10px] font-black text-gray-400 dark:text-zinc-500 uppercase tracking-widest">AI Match</p>
                                                <div class="inline-flex items-baseline text-lg sm:text-xl font-black {{ $scoreColorText }} leading-none">
                                                    {{ $score }}<span class="text-xs font-bold text-gray-400 dark:text-zinc-500 ml-0.5">%</span>
                                                </div>
                                            </div>
                                            <!-- AI Score Progress Bar -->
                                            <div class="w-full bg-gray-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                                                <div class="{{ $scoreColorBg }} h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $score }}%"></div>
                                            </div>
                                        </div>

                                        @php
                                            $cloudDisk = Storage::disk('cloud');
                                        @endphp
                                        @if(isset($jobApplication->resume) && $jobApplication->resume && $jobApplication->resume->fileUri)
                                            <a href="{{ $cloudDisk->url($jobApplication->resume->fileUri) }}" target="_blank" 
                                               class="group inline-flex items-center justify-center w-full md:w-auto text-xs font-bold text-brand-700 dark:text-brand-400 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 border border-brand-200/80 dark:border-brand-800/80 px-3.5 py-2 rounded-xl transition-all duration-200 shadow-2xs hover:shadow-xs mt-1 md:mt-0">
                                                <svg class="w-4 h-4 mr-1.5 text-brand-500 dark:text-brand-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                View Resume
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if(!empty($jobApplication->aiGeneratedFeedback))
                                    <!-- AI Feedback Expandable Section -->
                                    <div x-data="{ expanded: false }" class="mt-5 border-t border-gray-100 dark:border-zinc-800/80 pt-4">
                                        <button @click="expanded = !expanded" 
                                                class="group inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 shadow-2xs"
                                                :class="expanded 
                                                    ? 'bg-brand-50 text-brand-700 border border-brand-200 dark:bg-zinc-800 dark:text-brand-400 dark:border-zinc-700' 
                                                    : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 dark:bg-zinc-900 dark:text-zinc-300 dark:border-zinc-800 dark:hover:bg-zinc-800'">
                                            <svg class="w-4 h-4 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span x-text="expanded ? 'Hide AI Analysis Feedback' : 'View AI Analysis Feedback'"></span>
                                            <svg class="w-3.5 h-3.5 ml-1 transform transition-transform duration-300 text-brand-400" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        
                                        <!-- Expanded Deep Container (Slate in Light, Zinc-950 in Dark) -->
                                        <div x-show="expanded" 
                                             x-transition:enter="transition ease-out duration-300" 
                                             x-transition:enter-start="opacity-0 -translate-y-2" 
                                             x-transition:enter-end="opacity-100 translate-y-0" 
                                             class="mt-4 p-5 sm:p-6 bg-slate-50 dark:bg-zinc-950/90 rounded-2xl border border-slate-200/90 dark:border-zinc-800/90 space-y-5 shadow-inner overflow-hidden" 
                                             style="display: none;">
                                            
                                            <!-- Top Header in Expanded Panel -->
                                            <div class="flex items-center justify-between pb-3.5 border-b border-slate-200/90 dark:border-zinc-800">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center text-white shadow-sm shrink-0">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-zinc-100">AI Evaluation Breakdown</h4>
                                                        <p class="text-[11px] font-medium text-slate-500 dark:text-zinc-400">Deep match analysis against job requirements</p>
                                                    </div>
                                                </div>

                                                <!-- Score Pill with refined dark contrast -->
                                                @if($score >= 80)
                                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 shadow-2xs">
                                                        {{ $score }}% AI Match
                                                    </span>
                                                @elseif($score >= 50)
                                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 shadow-2xs">
                                                        {{ $score }}% AI Match
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20 shadow-2xs">
                                                        {{ $score }}% AI Match
                                                    </span>
                                                @endif
                                            </div>

                                            @if($hasStructuredSections)
                                                <!-- Strengths -->
                                                @if(!empty($strengthsBullets))
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-2.5">
                                                            <span class="px-2.5 py-1 rounded-lg text-xs font-black inline-flex items-center gap-1.5 shadow-2xs bg-emerald-50 text-emerald-800 border border-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                                                                Strengths ({{ count($strengthsBullets) }})
                                                            </span>
                                                        </div>
                                                        <div class="space-y-2">
                                                            @foreach($strengthsBullets as $bullet)
                                                                <div class="p-3.5 rounded-xl flex items-start gap-3 transition-colors shadow-2xs bg-white text-slate-800 border border-slate-200/90 dark:bg-zinc-900/90 dark:text-zinc-100 dark:border-zinc-800 dark:hover:border-emerald-500/30">
                                                                    <div class="w-5 h-5 rounded-lg flex items-center justify-center shrink-0 mt-0.5 font-black bg-emerald-50 text-emerald-600 border border-emerald-200/60 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                    </div>
                                                                    <p class="text-xs sm:text-sm font-medium leading-relaxed">{{ $bullet }}</p>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Gaps & Areas for Improvement -->
                                                @if(!empty($gapsBullets))
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-2.5">
                                                            <span class="px-2.5 py-1 rounded-lg text-xs font-black inline-flex items-center gap-1.5 shadow-2xs bg-rose-50 text-rose-800 border border-rose-200/80 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 dark:bg-rose-400"></span>
                                                                Gaps & Areas for Improvement ({{ count($gapsBullets) }})
                                                            </span>
                                                        </div>
                                                        <div class="space-y-2">
                                                            @foreach($gapsBullets as $bullet)
                                                                <div class="p-3.5 rounded-xl flex items-start gap-3 transition-colors shadow-2xs bg-white text-slate-800 border border-slate-200/90 dark:bg-zinc-900/90 dark:text-zinc-100 dark:border-zinc-800 dark:hover:border-rose-500/30">
                                                                    <div class="w-5 h-5 rounded-lg flex items-center justify-center shrink-0 mt-0.5 font-black bg-rose-50 text-rose-600 border border-rose-200/60 dark:bg-rose-500/15 dark:text-rose-400 dark:border-rose-500/30">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                    </div>
                                                                    <p class="text-xs sm:text-sm font-medium leading-relaxed">{{ $bullet }}</p>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Verdict -->
                                                @if(!empty($verdictText))
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-2.5">
                                                            <span class="px-2.5 py-1 rounded-lg text-xs font-black inline-flex items-center gap-1.5 shadow-2xs bg-amber-50 text-amber-800 border border-amber-200/80 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                                                                <span>💡</span>
                                                                Verdict & Recommendation
                                                            </span>
                                                        </div>
                                                        <div class="p-4 rounded-xl text-xs sm:text-sm font-medium leading-relaxed shadow-2xs bg-white text-slate-800 border border-amber-200/80 dark:bg-zinc-900/90 dark:text-zinc-100 dark:border-amber-500/20">
                                                            {{ $verdictText }}
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <!-- Fallback unstructured presentation -->
                                                <div class="p-4 rounded-xl bg-white dark:bg-zinc-900/90 border border-slate-200 dark:border-zinc-800 text-xs sm:text-sm text-slate-800 dark:text-zinc-100 leading-relaxed font-medium space-y-2 [&>ul]:list-disc [&>ul]:list-inside [&>ul>li]:mb-1.5 [&_strong]:font-black [&_strong]:text-slate-900 dark:[&_strong]:text-white">
                                                    {!! Str::markdown($rawFeedback) !!}
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                @endif

                                @if($jobApplication->tailored_cover_letter || $jobApplication->suggested_subject_line || $jobApplication->notes)
                                    <!-- AI Tailored Materials Expandable Section (Job Hunter) -->
                                    <div x-data="{ openMaterials: false, copiedItem: null }" class="mt-3.5 border-t border-gray-100 dark:border-zinc-800/80 pt-3">
                                        <button @click="openMaterials = !openMaterials" 
                                                class="group inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 shadow-2xs"
                                                :class="openMaterials 
                                                    ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950 dark:text-indigo-300 dark:border-indigo-800' 
                                                    : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 dark:hover:bg-zinc-700'">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span x-text="openMaterials ? 'Hide Tailored Application Materials' : 'View AI Tailored Application (Cover Letter & Pitch)'"></span>
                                            <svg class="w-3.5 h-3.5 ml-1 transform transition-transform duration-300 text-indigo-400" :class="{'rotate-180': openMaterials}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        
                                        <div x-show="openMaterials" 
                                             x-transition:enter="transition ease-out duration-300" 
                                             x-transition:enter-start="opacity-0 -translate-y-2" 
                                             x-transition:enter-end="opacity-100 translate-y-0" 
                                             class="mt-4 p-5 sm:p-6 bg-slate-50 dark:bg-zinc-950/80 rounded-2xl border border-slate-200/90 dark:border-zinc-800 space-y-5 shadow-inner" 
                                             style="display: none;">
                                            
                                            @if($jobApplication->suggested_subject_line)
                                                <div>
                                                    <div class="flex items-center justify-between mb-1.5">
                                                        <h4 class="text-[11px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                                                            <span>📌</span> Suggested Email Subject
                                                        </h4>
                                                        <button @click="navigator.clipboard.writeText('{{ addslashes($jobApplication->suggested_subject_line) }}'); copiedItem = 'subj'; setTimeout(() => copiedItem = null, 2000)" 
                                                                class="text-[11px] font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors flex items-center gap-1">
                                                            <span x-show="copiedItem !== 'subj'">Copy</span>
                                                            <span x-show="copiedItem === 'subj'" class="text-emerald-500 font-black">Copied! ✓</span>
                                                        </button>
                                                    </div>
                                                    <p class="text-xs font-semibold text-slate-900 dark:text-zinc-100 bg-white dark:bg-zinc-900 p-3.5 rounded-xl border border-slate-200/90 dark:border-zinc-800 font-mono select-all shadow-2xs">{{ $jobApplication->suggested_subject_line }}</p>
                                                </div>
                                            @endif

                                            @if($jobApplication->tailored_key_points && is_array($jobApplication->tailored_key_points))
                                                <div>
                                                    <h4 class="text-[11px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-2 flex items-center gap-1.5">
                                                        <span>⚡</span> Key Selling Points
                                                    </h4>
                                                    <div class="space-y-2">
                                                        @foreach($jobApplication->tailored_key_points as $point)
                                                            <div class="text-xs text-slate-800 dark:text-zinc-100 bg-white dark:bg-zinc-900 p-3.5 rounded-xl border border-slate-200/90 dark:border-zinc-800 flex items-start gap-2.5 shadow-2xs">
                                                                <span class="w-5 h-5 rounded-md bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-300 flex items-center justify-center font-black text-[11px] shrink-0 mt-0.5">•</span>
                                                                <span class="leading-relaxed">{{ $point }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($jobApplication->tailored_cover_letter)
                                                <div>
                                                    <div class="flex items-center justify-between mb-1.5">
                                                        <h4 class="text-[11px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                                                            <span>📄</span> Tailored Cover Letter
                                                        </h4>
                                                        <button @click="navigator.clipboard.writeText(@js($jobApplication->tailored_cover_letter)); copiedItem = 'letter'; setTimeout(() => copiedItem = null, 2000)" 
                                                                class="text-[11px] font-bold text-slate-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors flex items-center gap-1">
                                                            <span x-show="copiedItem !== 'letter'">Copy Letter</span>
                                                            <span x-show="copiedItem === 'letter'" class="text-emerald-500 font-black">Copied! ✓</span>
                                                        </button>
                                                    </div>
                                                    <div class="text-xs text-slate-800 dark:text-zinc-100 bg-white dark:bg-zinc-900 p-4 rounded-xl border border-slate-200/90 dark:border-zinc-800 whitespace-pre-line leading-relaxed select-all shadow-2xs font-sans">
                                                        {{ $jobApplication->tailored_cover_letter }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if($jobApplication->notes)
                                                <div>
                                                    <h4 class="text-[11px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-2 flex items-center gap-1.5">
                                                        <span>📝</span> Personal Notes & Timeline
                                                    </h4>
                                                    <div class="space-y-1.5">
                                                        @foreach(explode("\n", $jobApplication->notes) as $noteLine)
                                                            @if(trim($noteLine))
                                                                <div class="text-xs text-slate-800 dark:text-zinc-100 bg-amber-50/70 dark:bg-amber-950/30 p-3 rounded-xl border border-amber-200/70 dark:border-amber-900/50 font-mono text-[11px] shadow-2xs" dir="auto">
                                                                    {{ $noteLine }}
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="text-center p-fluid-12 bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200/90 dark:border-zinc-800 shadow-sm">
                            <div class="w-20 h-20 mx-auto mb-6 bg-brand-50 dark:bg-brand-950/60 border border-brand-200/80 dark:border-brand-800/80 rounded-3xl flex items-center justify-center text-brand-600 dark:text-brand-400 shadow-2xs">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">No Activity Yet</h3>
                            <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 mb-6 max-w-md mx-auto leading-relaxed">Your application timeline is empty. Head over to the dashboard to find matching jobs and kickstart your career journey.</p>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg shadow-brand-500/25 transition-all transform hover:-translate-y-0.5">
                                Explore Jobs
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
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