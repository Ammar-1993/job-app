<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex shrink-0 items-center justify-center bg-rose-100 dark:bg-rose-900/50 rounded-xl shadow-sm">
                    <span class="text-xl">🎯</span>
                </div>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight">
                    {{ __('Job Hunter Pipeline') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="hunterInterface()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header Subtitle & Stats -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-black bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 border border-brand-200/70 dark:border-brand-800/70 mb-2">
                    ⚡ Personal Career Agent
                </span>
                <p class="text-base text-gray-600 dark:text-zinc-400 max-w-2xl leading-relaxed">
                    Review externally imported jobs, match them with your profile embeddings, and generate tailored applications with 1 click.
                </p>
            </div>
            
            <div class="flex gap-4 shrink-0">
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-800 text-center min-w-[120px]">
                    <div class="text-xl font-black text-brand-600 dark:text-brand-400">{{ $totalImported }}</div>
                    <div class="text-[11px] text-gray-500 dark:text-zinc-400 uppercase tracking-widest mt-1 font-extrabold">Imported</div>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-800 text-center min-w-[120px]">
                    <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $totalApplied }}</div>
                    <div class="text-[11px] text-gray-500 dark:text-zinc-400 uppercase tracking-widest mt-1 font-extrabold">Applied</div>
                </div>
            </div>
        </div>

        @if(!$latestResume)
            <!-- Warning Banner if no resume -->
            <div class="mb-8 p-6 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-3xl flex items-start gap-4 shadow-sm">
                <span class="text-xl">⚠️</span>
                <div>
                    <h3 class="text-lg font-black text-amber-900 dark:text-amber-300">No Resume Uploaded Yet</h3>
                    <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                        To calculate semantic match scores and generate hyper-tailored cover letters, please add or upload your resume first.
                    </p>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition-colors shadow-sm">
                        Go to Profile & Resumes &rarr;
                    </a>
                </div>
            </div>
        @endif

        <!-- Filters & Controls -->
        <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-800 mb-8 flex flex-wrap items-center justify-between gap-4">
            
            <form action="{{ route('hunter.index') }}" method="GET" class="flex flex-col gap-3 w-full">
                <!-- Row 1: Search Input & Filters -->
                <div class="flex flex-wrap items-center gap-3 w-full">
                    <div class="relative flex-grow max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, location or company..." 
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-200 dark:border-zinc-700 rounded-2xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-xs transition-all">
                    </div>

                    <!-- Hidden fields to preserve active filters when submitting -->
                    @if(request('platform')) <input type="hidden" name="platform" value="{{ request('platform') }}"> @endif
                    @if(request('region')) <input type="hidden" name="region" value="{{ request('region') }}"> @endif
                    @if(request('match_filter')) <input type="hidden" name="match_filter" value="{{ request('match_filter') }}"> @endif

                    <!-- Application Status Filter Dropdown -->
                    <select name="applied_filter" onchange="this.form.submit()" 
                            class="bg-gray-50 dark:bg-zinc-800 text-gray-800 dark:text-zinc-200 border border-gray-200 dark:border-zinc-700 rounded-2xl text-xs font-bold py-2 pl-3.5 pr-9 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        <option value="all" {{ request('applied_filter') === 'all' ? 'selected' : '' }}>All Application Statuses</option>
                        <option value="not_applied" {{ request('applied_filter') === 'not_applied' ? 'selected' : '' }}>Not Applied Yet</option>
                        <option value="applied" {{ request('applied_filter') === 'applied' ? 'selected' : '' }}>Hunter Applied</option>
                    </select>

                    <!-- Clear Filters Button -->
                    @if(request()->hasAny(['search', 'platform', 'applied_filter', 'region', 'match_filter']) && (request('search') || request('platform') || request('applied_filter') !== 'all' || request('region') || request('match_filter') !== '80'))
                        <a href="{{ route('hunter.index') }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 transition-colors flex items-center bg-rose-50 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/60 px-3 py-1.5 rounded-2xl">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Clear All Filters
                        </a>
                    @endif
                </div>

                <!-- Row 1.5: Strict Match Threshold Filter (>= 80% Elite Match Default) -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 pb-1 border-t border-gray-100 dark:border-zinc-800/80">
                    <div class="flex flex-wrap items-center gap-1.5 bg-gray-100 dark:bg-zinc-800/90 p-1.5 rounded-2xl border border-gray-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-500 dark:text-zinc-400 px-2 flex items-center">
                            <span class="mr-1 text-sm">🎯</span> Match Threshold:
                        </span>
                        
                        <a href="{{ route('hunter.index', array_merge(request()->except(['match_filter', 'page']), ['match_filter' => '80'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-black transition-all flex items-center {{ ($matchFilter ?? '80') === '80' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20' : 'text-gray-700 dark:text-zinc-300 hover:text-gray-900 dark:hover:text-white' }}">
                            ⭐ Elite Only &ge; 80% ({{ $count80Plus }})
                        </a>

                        <a href="{{ route('hunter.index', array_merge(request()->except(['match_filter', 'page']), ['match_filter' => '70'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center {{ ($matchFilter ?? '80') === '70' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20 font-black' : 'text-gray-700 dark:text-zinc-300 hover:text-gray-900 dark:hover:text-white' }}">
                            ⚡ Strong &ge; 70% ({{ $count70Plus }})
                        </a>

                        <a href="{{ route('hunter.index', array_merge(request()->except(['match_filter', 'page']), ['match_filter' => 'all'])) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center {{ ($matchFilter ?? '80') === 'all' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-500 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            All Scored ({{ $countAll }})
                        </a>
                    </div>

                    @if(($matchFilter ?? '80') === '80' && ($countAll - $count80Plus) > 0)
                        <div class="flex items-center text-[11px] font-bold text-gray-500 dark:text-zinc-400 bg-gray-50 dark:bg-zinc-800/50 px-3 py-1.5 rounded-xl border border-gray-200/50 dark:border-zinc-700/50">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                            Strict filter active: {{ $count80Plus }} matches shown &bull; {{ $countAll - $count80Plus }} non-matching jobs hidden (&lt; 80%)
                        </div>
                    @endif
                </div>

                <!-- Row 2: Region Pills & Platform Pills -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 border-t border-gray-100 dark:border-zinc-800/80">
                    <!-- GCC Region Filter Switcher -->
                    <div class="flex flex-wrap items-center gap-1 bg-gray-100 dark:bg-zinc-800/90 p-1 rounded-2xl border border-gray-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-zinc-500 px-2">Location:</span>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['region', 'page']), ['region' => ''])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all {{ !request('region') ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            All Locations
                        </a>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['region', 'page']), ['region' => 'gcc'])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('region') === 'gcc' ? 'bg-white dark:bg-zinc-900 text-emerald-600 dark:text-emerald-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            🌴 GCC Total ({{ $totalGccAll }})
                        </a>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['region', 'page']), ['region' => 'sa'])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('region') === 'sa' ? 'bg-white dark:bg-zinc-900 text-emerald-600 dark:text-emerald-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="mr-1">🇸🇦</span> Saudi ({{ $totalSa }})
                        </a>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['region', 'page']), ['region' => 'ae'])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('region') === 'ae' ? 'bg-white dark:bg-zinc-900 text-amber-600 dark:text-amber-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="mr-1">🇦🇪</span> UAE ({{ $totalAe }})
                        </a>
                        @if($totalGccOther > 0)
                            <a href="{{ route('hunter.index', array_merge(request()->except(['region', 'page']), ['region' => 'gcc_other'])) }}" 
                               class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('region') === 'gcc_other' ? 'bg-white dark:bg-zinc-900 text-cyan-600 dark:text-cyan-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                                <span class="mr-1">🇰🇼 🇶🇦</span> Other Gulf ({{ $totalGccOther }})
                            </a>
                        @endif
                        <a href="{{ route('hunter.index', array_merge(request()->except(['region', 'page']), ['region' => 'worldwide'])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('region') === 'worldwide' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="mr-1">🌍</span> Worldwide ({{ $totalWorldwide }})
                        </a>
                    </div>

                    <!-- Platform Filter Pill Switcher -->
                    <div class="flex flex-wrap items-center gap-1 bg-gray-100 dark:bg-zinc-800/90 p-1 rounded-2xl border border-gray-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-zinc-500 px-2">Source:</span>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['platform', 'page']), ['platform' => ''])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all {{ !request('platform') ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            All
                        </a>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['platform', 'page']), ['platform' => 'greenhouse'])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('platform') === 'greenhouse' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> Greenhouse ({{ $totalGreenhouse }})
                        </a>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['platform', 'page']), ['platform' => 'weworkremotely'])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('platform') === 'weworkremotely' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></span> WWR ({{ $totalWwr }})
                        </a>
                        <a href="{{ route('hunter.index', array_merge(request()->except(['platform', 'page']), ['platform' => 'remotive'])) }}" 
                           class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('platform') === 'remotive' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <span class="w-2 h-2 rounded-full bg-violet-500 mr-1.5"></span> Remotive ({{ $totalRemotive }})
                        </a>
                        @if($totalAdzuna > 0)
                            <a href="{{ route('hunter.index', array_merge(request()->except(['platform', 'page']), ['platform' => 'adzuna'])) }}" 
                               class="px-3 py-1 rounded-xl text-xs font-bold transition-all flex items-center {{ request('platform') === 'adzuna' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                                <span class="w-2 h-2 rounded-full bg-sky-500 mr-1.5"></span> Adzuna ({{ $totalAdzuna }})
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Job Cards Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 relative">
            @forelse ($jobs as $job)
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-zinc-800 flex flex-col h-full relative overflow-hidden group">
                    
                    <!-- Top Ribbon (Platform) -->
                    @php
                        $ribbonConfig = match($job->source_platform) {
                            'greenhouse' => ['bg' => 'bg-emerald-600', 'label' => 'Greenhouse API'],
                            'adzuna'     => ['bg' => 'bg-sky-600',     'label' => 'Adzuna GCC'],
                            'remotive'   => ['bg' => 'bg-violet-600',  'label' => 'Remotive'],
                            default      => ['bg' => 'bg-rose-600',    'label' => 'WeWorkRemotely'],
                        };
                    @endphp
                    <div class="absolute top-0 right-0 px-4 py-1.5 rounded-bl-2xl font-bold text-[10px] uppercase tracking-wider text-white shadow-sm {{ $ribbonConfig['bg'] }}">
                        {{ $ribbonConfig['label'] }}
                    </div>

                    <div class="flex justify-between items-start mb-4 mt-2">
                        <div class="pr-28">
                            <h2 class="text-xl font-black text-gray-900 dark:text-white leading-tight group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                                {{ $job->title }}
                            </h2>
                            <p class="text-gray-500 dark:text-zinc-400 mt-1 flex items-center font-medium text-sm">
                                @if($job->company)
                                    <span class="font-bold text-gray-700 dark:text-zinc-300 mr-2">{{ $job->company->name }}</span>
                                @endif
                                <svg class="w-4 h-4 mr-1 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                {{ $job->location ?: 'Remote' }}
                            </p>
                        </div>
                        
                        <!-- Circular Match Score (Seamless in Light and Dark) -->
                        <div class="relative shrink-0 flex flex-col items-center justify-center bg-gray-50 dark:bg-zinc-800/60 rounded-2xl p-2 border border-gray-200/60 dark:border-zinc-700/60 w-16 h-16 shadow-inner">
                            <svg class="w-12 h-12 transform -rotate-90 drop-shadow-sm" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.9155" fill="none" class="stroke-current text-gray-200 dark:text-zinc-700" stroke-width="3"></circle>
                                <circle cx="18" cy="18" r="15.9155" fill="none" 
                                    class="stroke-current transition-all duration-1000 ease-out {{ $job->matchScore >= 75 ? 'text-emerald-500' : ($job->matchScore >= 45 ? 'text-amber-500' : 'text-rose-500') }}" 
                                    stroke-width="3" stroke-dasharray="100" stroke-dashoffset="{{ 100 - $job->matchScore }}" stroke-linecap="round"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center flex-col mt-1">
                                <span class="text-xs font-black text-gray-900 dark:text-zinc-100 leading-none">{{ $job->matchScore }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <div class="flex flex-wrap gap-2 mb-3">
                            <!-- Country / Region Badge -->
                            @if(isset($job->region))
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold flex items-center border {{ $job->region['badge_class'] }}">
                                    <span class="mr-1.5 text-sm">{{ $job->region['flag'] }}</span> {{ $job->region['label'] }}
                                </span>
                            @endif
                            @if(!empty($job->seniorityLabel))
                                <span class="bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60 px-2.5 py-1 rounded-xl text-xs font-bold flex items-center">
                                    {{ $job->seniorityLabel }}
                                </span>
                            @endif
                            @if($job->type)
                                <span class="bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 border border-gray-200/60 dark:border-zinc-700/60 px-2.5 py-1 rounded-xl text-xs font-bold uppercase tracking-wider">{{ $job->type }}</span>
                            @endif
                            @if($job->salary)
                                <span class="bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 px-2.5 py-1 rounded-xl text-xs font-bold flex items-center border border-emerald-200/60 dark:border-emerald-800/60">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V9m0 3v2.25M8 12h8m-11 3.5a9 9 0 1118 0M3 12a9 9 0 009 9c1.674 0 3.298-.488 4.657-1.404"></path></svg>
                                    {{ is_numeric($job->salary) ? '$' . number_format($job->salary) : $job->salary }}
                                </span>
                            @endif
                            <span class="bg-gray-50 dark:bg-zinc-800/50 text-gray-500 dark:text-zinc-400 px-2.5 py-1 rounded-xl text-xs font-medium border border-gray-200/50 dark:border-zinc-700/50 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Imported {{ $job->imported_at ? $job->imported_at->diffForHumans() : 'Recently' }}
                            </span>
                        </div>

                        <!-- Explainable Hybrid Match Breakdown (Semantic + Explicit Skills) -->
                        <div class="p-3 bg-gray-50/80 dark:bg-zinc-800/40 rounded-2xl border border-gray-100 dark:border-zinc-800/80 mb-2">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-black text-gray-800 dark:text-zinc-200 flex items-center">
                                        <span class="inline-block w-2 h-2 rounded-full mr-1.5 {{ $job->matchScore >= 75 ? 'bg-emerald-500' : ($job->matchScore >= 45 ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                        {{ $job->matchScore >= 75 ? 'High Alignment' : ($job->matchScore >= 45 ? 'Moderate Match' : 'Low Match') }}
                                    </span>
                                    <span class="text-gray-300 dark:text-zinc-700">•</span>
                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-zinc-400">
                                        AI Semantic: <strong class="text-gray-900 dark:text-white font-bold">{{ $job->vectorScore ?? 0 }}%</strong>
                                    </span>
                                    <span class="text-gray-300 dark:text-zinc-700">•</span>
                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-zinc-400">
                                        Skills: <strong class="text-gray-900 dark:text-white font-bold">{{ $job->skillsScore ?? 0 }}%</strong>
                                    </span>
                                </div>
                                @if($job->isAudited)
                                    <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md border border-indigo-200/60 dark:border-indigo-800/60 uppercase tracking-wider hidden sm:inline">🎯 Audited Score</span>
                                @elseif($job->stackMismatch ?? false)
                                    <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-md border border-rose-200/60 dark:border-rose-800/60 uppercase tracking-wider hidden sm:inline">⚡ Stack Mismatch</span>
                                @elseif($job->seniorityMismatch ?? false)
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-200/60 dark:border-amber-800/60 uppercase tracking-wider hidden sm:inline">⏳ Seniority Mismatch</span>
                                @elseif($job->trackMismatch ?? false)
                                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-200/60 dark:border-amber-800/60 uppercase tracking-wider hidden sm:inline">⚠️ Track Mismatch</span>
                                @else
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-zinc-500 uppercase tracking-wider hidden sm:inline">Hybrid Match</span>
                                @endif
                            </div>

                            <!-- Matched Skills Badges -->
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if(!empty($job->matchedSkills))
                                    @foreach(array_slice($job->matchedSkills, 0, 4) as $mSkill)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/60 shadow-2xs">
                                            <svg class="w-3 h-3 mr-1 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            {{ $mSkill }}
                                        </span>
                                    @endforeach
                                    @if(count($job->matchedSkills) > 4)
                                        <span class="text-[10px] font-bold text-gray-500 dark:text-zinc-400 px-1.5 py-0.5 bg-gray-100 dark:bg-zinc-800 rounded-md">
                                            +{{ count($job->matchedSkills) - 4 }} more
                                        </span>
                                    @endif
                                @else
                                    <span class="text-[11px] italic text-gray-400 dark:text-zinc-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        No direct keyword matches found
                                    </span>
                                @endif
                            </div>

                            @php
                                $warningReason = ($job->stackMismatch ?? false) ? $job->stackReason : (($job->seniorityMismatch ?? false) ? $job->seniorityReason : (($job->trackMismatch ?? false) ? $job->trackReason : null));
                            @endphp
                            @if(!empty($warningReason))
                                <div class="mt-2 text-[11px] text-amber-700 dark:text-amber-400/90 bg-amber-50/80 dark:bg-amber-950/30 px-2.5 py-1 rounded-lg border border-amber-200/50 dark:border-amber-900/50 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span class="font-medium">{{ $warningReason }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-zinc-800 flex flex-col sm:flex-row gap-3 items-center justify-between">
                        <a href="{{ $job->source_url }}" target="_blank" class="text-sm font-bold text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-white transition-colors flex items-center">
                            View Original 
                            <svg class="w-4 h-4 ml-1 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        
                        <div class="flex gap-2 w-full sm:w-auto">
                            @if($job->hunterApplication)
                                <!-- Already Applied: Crisp, high-contrast button in Light and Dark -->
                                <button @click="loadExistingApplication('{{ $job->id }}', {
                                            id: @js($job->id),
                                            title: @js($job->title),
                                            company: @js($job->company?->name ?? 'External Company'),
                                            location: @js($job->location ?: 'Remote'),
                                            platform: @js($job->source_platform),
                                            matchScore: {{ $job->matchScore ?? 0 }},
                                            vectorScore: {{ $job->vectorScore ?? 0 }},
                                            skillsScore: {{ $job->skillsScore ?? 0 }},
                                            matchedSkills: @js($job->matchedSkills ?? []),
                                            missingSkills: @js($job->missingSkills ?? []),
                                            matchSummary: @js($job->matchDetails['summary'] ?? ''),
                                            sourceUrl: @js($job->source_url)
                                        })" 
                                        :disabled="isGenerating"
                                        class="flex-grow sm:flex-grow-0 px-5 py-2.5 rounded-xl text-xs font-black transition-all duration-200 flex items-center justify-center shadow-md transform active:scale-95 disabled:opacity-50
                                               bg-gray-900 hover:bg-black text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950">
                                    <span class="mr-1.5">🎯</span> View Saved App
                                </button>
                            @else
                                <!-- Not Applied Yet - Generate Action -->
                                <button @click="generateApplication('{{ $job->id }}', {
                                            id: @js($job->id),
                                            title: @js($job->title),
                                            company: @js($job->company?->name ?? 'External Company'),
                                            location: @js($job->location ?: 'Remote'),
                                            platform: @js($job->source_platform),
                                            matchScore: {{ $job->matchScore ?? 0 }},
                                            vectorScore: {{ $job->vectorScore ?? 0 }},
                                            skillsScore: {{ $job->skillsScore ?? 0 }},
                                            matchedSkills: @js($job->matchedSkills ?? []),
                                            missingSkills: @js($job->missingSkills ?? []),
                                            matchSummary: @js($job->matchDetails['summary'] ?? ''),
                                            sourceUrl: @js($job->source_url)
                                        })" 
                                        :disabled="isGenerating"
                                        class="flex-grow sm:flex-grow-0 px-5 py-2.5 rounded-xl text-xs font-black text-white bg-gradient-to-r from-brand-600 via-brand-500 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 transition-all duration-300 flex items-center justify-center shadow-lg shadow-brand-500/25 transform hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group/btn">
                                    <template x-if="generatingJobId === '{{ $job->id }}'">
                                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    <template x-if="generatingJobId !== '{{ $job->id }}'">
                                        <svg class="w-4 h-4 mr-2 animate-pulse group-hover/btn:animate-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </template>
                                    <span x-text="generatingJobId === '{{ $job->id }}' ? 'Generating...' : 'AI Generate App'"></span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center p-20 bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-zinc-800 shadow-sm">
                    <div class="text-xl sm:text-2xl mb-4">🏜️</div>
                    <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white mb-2">No imported jobs found</h3>
                    <p class="text-gray-500 dark:text-zinc-400">Try adjusting your filters or wait for the scheduler to import new jobs.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
    </div>

    <!-- Loading Overlay (Fixed Fullscreen) -->
    <div x-show="isGenerating" x-cloak class="fixed inset-0 z-[150] bg-gray-950/80 backdrop-blur-md flex flex-col items-center justify-center p-4" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div class="bg-white dark:bg-zinc-900 p-8 rounded-3xl shadow-2xl border border-gray-200 dark:border-zinc-800 max-w-sm text-center transform transition-all">
            <div class="relative w-24 h-24 mx-auto mb-6">
                <svg class="animate-spin w-full h-full text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl animate-bounce">🧠</span>
                </div>
            </div>
            <h3 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white mb-2">AI is analyzing & writing...</h3>
            <p class="text-sm text-gray-500 dark:text-zinc-400 leading-relaxed">
                Crafting a hyper-personalized cover letter and extracting key selling points based on your profile.
            </p>
        </div>
    </div>

    <!-- AI Result Modal (World-Class AI Application Package) -->
    <div x-show="showResultModal" x-cloak 
         @keydown.escape.window="closeModal()"
         class="fixed inset-0 z-[160] overflow-y-auto"
         style="display: none;">
         
        <!-- Backdrop with rich blur -->
        <div x-show="showResultModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-950/70 dark:bg-black/85 backdrop-blur-md" 
             @click="closeModal()"></div>

        <!-- Centerer -->
        <div class="flex min-h-full items-center justify-center p-3 sm:p-6 text-center w-full">
            
            <!-- Modal Panel -->
            <div x-show="showResultModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-4xl my-auto bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl border border-gray-200/90 dark:border-zinc-800 flex flex-col max-h-[88vh] overflow-hidden text-left z-10 transition-all">
                
                <!-- STICKY HEADER -->
                <div class="shrink-0 px-6 sm:px-8 py-5 border-b border-gray-200/90 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-brand-500/20 shrink-0">
                                <span class="text-xl">⚡</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight leading-tight" x-text="activeJob.title || 'AI Generated Application'"></h3>
                                
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="text-sm font-bold text-gray-600 dark:text-zinc-400 flex items-center" x-show="activeJob.company">
                                        <svg class="w-4 h-4 mr-1 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span x-text="activeJob.company"></span>
                                    </span>
                                    <span class="text-gray-300 dark:text-zinc-700">•</span>
                                    
                                    <!-- Match Score Pill: High contrast in both light and dark -->
                                    <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/80" 
                                          x-text="(activeJob.matchScore || 0) + '% Match'"></span>

                                    <!-- AI Semantic Pill -->
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-800/80" 
                                          x-text="'Semantic: ' + (activeJob.vectorScore || 0) + '%'"></span>

                                    <!-- Skills Pill -->
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200/70 dark:border-purple-800/80" 
                                          x-text="'Skills: ' + (activeJob.skillsScore || 0) + '%'"></span>
                                          
                                    <!-- Platform Pill: High contrast in both light and dark -->
                                    <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 border border-brand-200/70 dark:border-brand-800/80" 
                                          x-text="activeJob.platform === 'greenhouse' ? 'Greenhouse API' : (activeJob.platform === 'adzuna' ? 'Adzuna GCC' : (activeJob.platform === 'remotive' ? 'Remotive' : 'WeWorkRemotely RSS'))"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button @click="closeModal()" class="shrink-0 text-gray-400 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-white bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 p-2.5 rounded-2xl transition-all duration-200" title="Close (ESC)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Navigation Tabs Switcher -->
                    <div class="flex items-center gap-1.5 p-1 bg-gray-100 dark:bg-zinc-800 rounded-2xl border border-gray-200/60 dark:border-zinc-700/60 overflow-x-auto">
                        <button @click="activeTab = 'cover_letter'" 
                                :class="activeTab === 'cover_letter' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-bold'"
                                class="flex items-center px-4 py-2 rounded-xl text-xs transition-all duration-200 whitespace-nowrap">
                            <span class="mr-1.5">📄</span> Cover Letter
                        </button>

                        <button @click="activeTab = 'selling_points'" 
                                :class="activeTab === 'selling_points' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-bold'"
                                class="flex items-center px-4 py-2 rounded-xl text-xs transition-all duration-200 whitespace-nowrap">
                            <span class="mr-1.5">⚡</span> Key Selling Points
                            <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px] bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-black border border-indigo-200 dark:border-indigo-800/60" x-text="(resultData.key_selling_points || []).length"></span>
                        </button>

                        <button @click="activeTab = 'email_draft'" 
                                :class="activeTab === 'email_draft' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-bold'"
                                class="flex items-center px-4 py-2 rounded-xl text-xs transition-all duration-200 whitespace-nowrap">
                            <span class="mr-1.5">📧</span> Email Outreach
                        </button>

                        <button @click="activeTab = 'all'" 
                                :class="activeTab === 'all' ? 'bg-white dark:bg-zinc-900 text-gray-900 dark:text-white shadow-sm font-black' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white font-bold'"
                                class="flex items-center px-4 py-2 rounded-xl text-xs transition-all duration-200 whitespace-nowrap">
                            <span class="mr-1.5">📑</span> Full Package
                        </button>
                    </div>
                </div>

                <!-- SCROLLABLE BODY -->
                <div class="overflow-y-auto flex-grow p-6 sm:p-8 space-y-6 text-gray-900 dark:text-zinc-100">
                    
                    <!-- Status Notification Banner -->
                    <div class="bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                ✓
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-900 dark:text-white">Saved in Personal Pipeline (Draft)</h4>
                                <p class="text-xs text-emerald-800 dark:text-emerald-300/80 mt-0.5">Track interviews, notes, and stages from the Backoffice portal.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
                            <a href="{{ route('job-applications.index') }}" target="_blank" 
                               class="w-full sm:w-auto px-3.5 py-2 bg-white dark:bg-emerald-900/60 hover:bg-emerald-50 dark:hover:bg-emerald-900/80 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 text-xs font-bold rounded-xl transition-all shadow-xs flex items-center justify-center">
                                View in My Feed
                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </a>
                            <a :href="'{{ config('app.backoffice_url') }}/job-applications/' + resultData.application_id" target="_blank" 
                               class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center justify-center">
                                Open in Backoffice
                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Transparent Hybrid Match Breakdown Card -->
                    <div class="bg-gray-50/90 dark:bg-zinc-800/60 border border-gray-200/90 dark:border-zinc-700/70 rounded-2xl p-4 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <h4 class="text-xs font-black uppercase tracking-wider text-gray-800 dark:text-zinc-200 flex items-center">
                                <span class="mr-1.5">🎯</span> Explainable Match Breakdown
                            </h4>
                            <span class="text-[11px] font-semibold text-gray-500 dark:text-zinc-400">
                                55% AI Semantic Vector + 45% Explicit Skills Matching
                            </span>
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex items-start gap-2 flex-wrap sm:flex-nowrap">
                                <span class="text-[11px] font-bold text-gray-600 dark:text-zinc-400 shrink-0 mt-0.5">Matched Skills:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="skill in (activeJob.matchedSkills || [])" :key="skill">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold bg-emerald-100/80 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-300/70 dark:border-emerald-800">
                                            <svg class="w-3 h-3 mr-1 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <span x-text="skill"></span>
                                        </span>
                                    </template>
                                    <span x-show="!activeJob.matchedSkills || activeJob.matchedSkills.length === 0" class="text-xs text-gray-400 italic">
                                        No direct keyword matches identified
                                    </span>
                                </div>
                            </div>

                            <template x-if="activeJob.missingSkills && activeJob.missingSkills.length > 0">
                                <div class="flex items-start gap-2 pt-2 border-t border-gray-200/50 dark:border-zinc-700/50 flex-wrap sm:flex-nowrap">
                                    <span class="text-[11px] font-bold text-gray-500 dark:text-zinc-400 shrink-0 mt-0.5">Other Keywords in Vacancy:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="skill in (activeJob.missingSkills || []).slice(0, 8)" :key="skill">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 border border-gray-200 dark:border-zinc-700" x-text="skill"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 1: COVER LETTER -->
                    <div x-show="activeTab === 'cover_letter' || activeTab === 'all'" class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black uppercase tracking-wider text-brand-600 dark:text-brand-400 flex items-center">
                                    <span class="mr-1.5">📄</span> Tailored Cover Letter
                                </span>
                                <span class="text-xs text-gray-400 dark:text-zinc-600">•</span>
                                <span class="text-[11px] text-gray-500 dark:text-zinc-400 font-medium" x-text="getWordCount(resultData.cover_letter) + ' words (~1.5 min read)'"></span>
                            </div>
                            
                            <button @click="copyToClipboard(resultData.cover_letter, 'letter')" 
                                    class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center shadow-sm"
                                    :class="copiedField === 'letter' ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/80'">
                                <template x-if="copiedField === 'letter'">
                                    <span class="flex items-center font-black">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Copied to Clipboard! ✓
                                    </span>
                                </template>
                                <template x-if="copiedField !== 'letter'">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                        Copy Full Letter
                                    </span>
                                </template>
                            </button>
                        </div>

                        <!-- Letter Presentation Card -->
                        <div class="bg-gray-50/90 dark:bg-zinc-800/60 rounded-3xl p-6 sm:p-8 border border-gray-200/90 dark:border-zinc-700/70 border-l-4 border-l-brand-500 relative group transition-all shadow-sm">
                            <div class="text-xs text-gray-500 dark:text-zinc-400 font-mono mb-4 pb-3 border-b border-gray-200/80 dark:border-zinc-700/70 flex justify-between items-center">
                                <span>RE: <strong class="text-gray-900 dark:text-zinc-200" x-text="activeJob.title || 'Job Application'"></strong></span>
                                <span x-text="new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span>
                            </div>
                            
                            <!-- Pure high-contrast font colors in both themes -->
                            <div class="text-gray-900 dark:text-zinc-100 text-sm sm:text-base leading-relaxed whitespace-pre-wrap font-sans select-all font-normal" 
                                 x-text="resultData.cover_letter">
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-200/80 dark:border-zinc-700/70 text-xs text-gray-500 dark:text-zinc-400 font-medium">
                                Sincerely,<br>
                                <strong class="text-gray-900 dark:text-white text-sm">{{ auth()->user()->name }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: KEY SELLING POINTS -->
                    <div x-show="activeTab === 'selling_points' || activeTab === 'all'" class="space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center">
                                    <span class="mr-1.5">⚡</span> Key Selling Points
                                </span>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Strengths from your profile matched specifically to this position.</p>
                            </div>

                            <button @click="copyAllPoints()" 
                                    class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center shadow-sm"
                                    :class="copiedField === 'all_points' ? 'bg-emerald-600 text-white' : 'bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/80'">
                                <template x-if="copiedField === 'all_points'">
                                    <span class="flex items-center font-black">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Copied All Points! ✓
                                    </span>
                                </template>
                                <template x-if="copiedField !== 'all_points'">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                        Copy All as Bullets
                                    </span>
                                </template>
                            </button>
                        </div>

                        <!-- Selling Points Cards Grid -->
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="(point, idx) in (resultData.key_selling_points || [])" :key="idx">
                                <div class="p-4 rounded-2xl bg-gray-50/90 dark:bg-zinc-800/60 border border-gray-200/80 dark:border-zinc-700/70 flex items-start justify-between gap-4 group hover:border-indigo-300 dark:hover:border-indigo-500/70 transition-all shadow-sm">
                                    <div class="flex items-start gap-3.5">
                                        <div class="w-7 h-7 rounded-xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/70 flex items-center justify-center font-black text-xs shrink-0 mt-0.5" x-text="idx + 1">
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-zinc-100 leading-relaxed select-all" x-text="point"></p>
                                    </div>
                                    <button @click="copyToClipboard(point, 'point_' + idx)" 
                                            class="shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:text-zinc-400 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-zinc-700 transition-colors"
                                            :title="copiedField === 'point_' + idx ? 'Copied!' : 'Copy this point'">
                                        <template x-if="copiedField === 'point_' + idx">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </template>
                                        <template x-if="copiedField !== 'point_' + idx">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                        </template>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 3: EMAIL OUTREACH DRAFT -->
                    <div x-show="activeTab === 'email_draft' || activeTab === 'all'" class="space-y-6">
                        
                        <!-- Subject Line Block -->
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <label class="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-zinc-300 flex items-center">
                                    <span class="mr-1.5">📌</span> Suggested Email Subject Line
                                </label>
                                <button @click="copyToClipboard(resultData.subject_line, 'subject')" 
                                        class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all flex items-center shadow-sm"
                                        :class="copiedField === 'subject' ? 'bg-emerald-600 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200/80 dark:border-zinc-700'">
                                    <span x-show="copiedField === 'subject'" class="flex items-center font-black">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Copied! ✓
                                    </span>
                                    <span x-show="copiedField !== 'subject'" class="flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                        Copy Subject
                                    </span>
                                </button>
                            </div>

                            <div class="bg-gray-50/90 dark:bg-zinc-800/60 rounded-2xl p-4 border border-gray-200/90 dark:border-zinc-700/70 font-mono text-sm text-gray-900 dark:text-zinc-100 select-all shadow-inner flex items-center justify-between">
                                <span x-text="resultData.subject_line"></span>
                                <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-zinc-500 ml-2" x-text="(resultData.subject_line || '').length + ' chars'"></span>
                            </div>
                        </div>

                        <!-- Ready-to-Send Cold Outreach Draft -->
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <div>
                                    <label class="text-xs font-black uppercase tracking-wider text-gray-700 dark:text-zinc-300 flex items-center">
                                        <span class="mr-1.5">✉️</span> Complete Outreach Email Draft
                                    </label>
                                    <p class="text-[11px] text-gray-500 dark:text-zinc-400 mt-0.5">Ready to paste into Gmail or your preferred email client.</p>
                                </div>

                                <button @click="copyToClipboard(getOutreachEmail(), 'outreach')" 
                                        class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all flex items-center shadow-sm"
                                        :class="copiedField === 'outreach' ? 'bg-emerald-600 text-white' : 'bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/80'">
                                    <span x-show="copiedField === 'outreach'" class="flex items-center font-black">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Copied Email Draft! ✓
                                    </span>
                                    <span x-show="copiedField !== 'outreach'" class="flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                        Copy Ready Draft
                                    </span>
                                </button>
                            </div>

                            <div class="bg-gray-50/90 dark:bg-zinc-800/60 rounded-2xl p-5 border border-gray-200/90 dark:border-zinc-700/70 font-sans text-xs sm:text-sm text-gray-900 dark:text-zinc-100 whitespace-pre-wrap leading-relaxed select-all shadow-inner" 
                                 x-text="getOutreachEmail()">
                            </div>
                        </div>

                    </div>

                </div>

                <!-- STICKY FOOTER -->
                <div class="shrink-0 px-6 sm:px-8 py-4 border-t border-gray-200/90 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a :href="activeJob.sourceUrl || resultData.source_url" target="_blank" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 text-gray-800 dark:text-zinc-200 border border-gray-200 dark:border-zinc-700 text-xs font-bold rounded-xl transition-all shadow-sm">
                            Apply on Source Website
                            <svg class="w-3.5 h-3.5 ml-1.5 text-gray-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        <a href="{{ route('job-applications.index') }}" target="_blank" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700 text-xs font-bold rounded-xl transition-all">
                            My Applications Feed
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </a>
                        <a :href="'{{ config('app.backoffice_url') }}/job-applications/' + resultData.application_id" target="_blank" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800/80 text-xs font-bold rounded-xl transition-all">
                            Backoffice Pipeline
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>

                    <button @click="closeModal()" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-gray-900 hover:bg-black text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-black rounded-xl transition-all shadow-md">
                        Done / Close
                    </button>
                </div>

            </div>
        </div>
    </div>

    </div>

    @push('scripts')
    <script>
    function hunterInterface() {
        return {
            isGenerating: false,
            generatingJobId: null,
            showResultModal: false,
            isExisting: false,
            copiedField: null,
            activeTab: 'cover_letter',
            activeJob: {
                id: '',
                title: '',
                company: '',
                location: '',
                platform: '',
                matchScore: 0,
                vectorScore: 0,
                skillsScore: 0,
                matchedSkills: [],
                missingSkills: [],
                matchSummary: '',
                sourceUrl: '#'
            },
            resultData: {
                application_id: '',
                subject_line: '',
                key_selling_points: [],
                cover_letter: '',
                source_url: '#'
            },

            async generateApplication(jobId, jobMeta = null) {
                if (this.isGenerating) return;
                this.isGenerating = true;
                this.generatingJobId = jobId;
                if (jobMeta) {
                    this.activeJob = Object.assign({}, this.activeJob, jobMeta);
                }

                try {
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '{{ csrf_token() }}';
                    const resumeId = @json($latestResume?->id);

                    const response = await fetch(`/hunter/${jobId}/generate`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            resume_id: resumeId
                        })
                    });

                    const data = await response.json().catch(() => null);

                    if (!response.ok || !data || !data.success) {
                        const message = data?.message || `Request failed with status ${response.status}`;
                        alert(message);
                        return;
                    }

                    // Process selling points safely
                    let points = data.key_selling_points;
                    if (typeof points === 'string') {
                        try { points = JSON.parse(points); } catch(e) { points = [points]; }
                    }
                    if (!Array.isArray(points)) {
                        points = points ? [points] : [];
                    }

                    // Populate hybrid match breakdown if returned
                    if (data.match_score !== undefined) this.activeJob.matchScore = data.match_score;
                    if (data.vector_score !== undefined) this.activeJob.vectorScore = data.vector_score;
                    if (data.skills_score !== undefined) this.activeJob.skillsScore = data.skills_score;
                    if (data.matched_skills !== undefined) this.activeJob.matchedSkills = data.matched_skills;
                    if (data.missing_skills !== undefined) this.activeJob.missingSkills = data.missing_skills;
                    if (data.match_summary !== undefined) this.activeJob.matchSummary = data.match_summary;

                    this.resultData = {
                        application_id: data.application_id || '',
                        subject_line: data.subject_line || '',
                        key_selling_points: points,
                        cover_letter: data.cover_letter || '',
                        source_url: data.source_url || this.activeJob.sourceUrl || '#'
                    };
                    this.isExisting = Boolean(data.already_existed);
                    this.activeTab = 'cover_letter';
                    this.showResultModal = true;

                } catch (error) {
                    console.error('Error generating AI application:', error);
                    alert('Communication error with AI server: ' + (error.message || 'Unknown error'));
                } finally {
                    this.isGenerating = false;
                    this.generatingJobId = null;
                }
            },

            async loadExistingApplication(jobId, jobMeta = null) {
                await this.generateApplication(jobId, jobMeta);
            },

            closeModal() {
                this.showResultModal = false;
                window.location.reload();
            },

            getWordCount(text) {
                if (!text) return 0;
                return text.trim().split(/\s+/).filter(Boolean).length;
            },

            copyAllPoints() {
                const points = this.resultData.key_selling_points || [];
                const formatted = points.map(p => `• ${p}`).join('\n');
                this.copyToClipboard(formatted, 'all_points');
            },

            getOutreachEmail() {
                const companyName = this.activeJob.company || 'Hiring Team';
                const jobTitle = this.activeJob.title || 'the role';
                const subject = this.resultData.subject_line || `Application for ${jobTitle}`;
                const points = (this.resultData.key_selling_points || []).map(p => `• ${p}`).join('\n');

                return `Subject: ${subject}

Hi ${companyName} Team,

I am reaching out to submit my application for the ${jobTitle} position. With my background in engineering and track record of building robust systems, I am excited about the opportunity to contribute to your team.

Key reasons my profile aligns with this role:
${points}

I have attached my tailored cover letter and resume for your review. I would welcome the opportunity to discuss how my skill set can support your team's objectives.

Best regards,
{{ auth()->user()->name }}`;
            },

            copyToClipboard(text, fieldName = null) {
                if (!text) {
                    alert('Nothing to copy!');
                    return;
                }

                const self = this;
                const setCopied = () => {
                    if (fieldName) {
                        self.copiedField = fieldName;
                        setTimeout(() => {
                            if (self.copiedField === fieldName) self.copiedField = null;
                        }, 2000);
                    } else {
                        alert('Copied to clipboard!');
                    }
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(setCopied).catch(err => {
                        console.warn('Navigator clipboard failed, using fallback:', err);
                        self.fallbackCopy(text, setCopied);
                    });
                } else {
                    this.fallbackCopy(text, setCopied);
                }
            },

            fallbackCopy(text, onSuccess) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-9999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        onSuccess();
                    } else {
                        alert('Copy command was unsuccessful. Please copy manually.');
                    }
                } catch (err) {
                    console.error('Fallback copy error:', err);
                    alert('Could not copy automatically. Please copy manually.');
                }
                document.body.removeChild(textArea);
            }
        };
    }

    // Ensure hunterInterface is available globally and in Alpine registry
    window.hunterInterface = hunterInterface;
    if (window.Alpine) {
        window.Alpine.data('hunterInterface', hunterInterface);
    } else {
        document.addEventListener('alpine:init', () => {
            window.Alpine.data('hunterInterface', hunterInterface);
        });
    }
    </script>
    @endpush
</x-app-layout>
