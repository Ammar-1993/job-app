<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex shrink-0 items-center justify-center bg-brand-100 dark:bg-brand-900/50 rounded-xl shadow-sm">
                <span class="text-xl">📊</span>
            </div>
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight">
                {{ __('app.dashboard.title') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-fluid-8 transition-colors duration-300" 
        x-data="{ 
            search: '{{ request('search') }}', 
            filter: '{{ request('filter') }}',
            sort: '{{ request('sort', 'match') }}',
            scope: '{{ request('scope') }}',
            totalJobs: {{ $jobs->total() ?? 0 }},
            savedJobs: {{ $savedJobsCount ?? 0 }},
            loading: false,
            get hasActiveFilters() {
                return this.search !== '' || this.filter !== '' || this.sort !== 'match' || this.scope !== '';
            },
            clearFilters() {
                this.search = '';
                this.filter = '';
                this.sort = 'match';
                this.scope = '';
                this.updateDashboard();
            },
            updateDashboard() {
                this.loading = true;
                $dispatch('jobs-updating');
                const base = '{{ url()->current() }}';
                const url = new URL(base);
                if (this.search)  url.searchParams.set('search', this.search);
                else              url.searchParams.delete('search');
                if (this.filter)  url.searchParams.set('filter', this.filter);
                else              url.searchParams.delete('filter');
                if (this.sort && this.sort !== 'match') url.searchParams.set('sort', this.sort);
                else url.searchParams.delete('sort');
                if (this.scope)   url.searchParams.set('scope', this.scope);
                else              url.searchParams.delete('scope');

                window.history.pushState({}, '', url);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('job-list-container').innerHTML = data.html;
                    this.totalJobs = data.total;
                    if (data.savedJobsCount !== undefined) {
                        this.savedJobs = data.savedJobsCount;
                    }
                    this.loading = false;
                    $dispatch('jobs-updated');
                })
                .catch(error => {
                    console.error('Error fetching jobs:', error);
                    this.loading = false;
                });
            }
        }"
        @job-saved.window="if ($event.detail && $event.detail.count !== undefined) savedJobs = $event.detail.count; updateDashboard()"
    >
        <!-- Main Content Wrapper -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-xl dark:shadow-2xl rounded-2xl p-fluid-8 border border-gray-200 dark:border-indigo-700/30 transition-colors duration-300">
                
                <!-- Welcome Section -->
                <div class="mb-fluid-8 border-b border-gray-100 dark:border-zinc-800/80 pb-6 transition-colors duration-300">
                    <h3 class="text-gray-900 dark:text-white text-2xl sm:text-3xl font-black tracking-tight flex items-center gap-3">
                       <span class="text-3xl origin-bottom-right hover:rotate-12 transition-transform duration-300 inline-block cursor-default">👋</span> 
                       <span>{{ __('app.dashboard.welcome_back') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600 dark:from-brand-400 dark:to-indigo-400">{{ Auth::user()->name }}</span>!</span>
                    </h3>
                </div>

                <!-- Stats Overview Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-fluid-4 mb-fluid-12">
                    <!-- Stat Card 1: Total Jobs -->
                    <div @click="clearFilters()" class="cursor-pointer border p-6 rounded-2xl shadow-sm transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group bg-white border-gray-200/90 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/10 dark:bg-zinc-800/80 dark:border-zinc-700/80 dark:hover:border-brand-500/50 dark:hover:bg-zinc-800">
                        <!-- Decorative gradient blob -->
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-500/10 dark:bg-brand-500/15 rounded-full blur-2xl group-hover:bg-brand-500/20 transition-all duration-500"></div>
                        
                        <div class="flex items-center space-x-4 relative z-10">
                            <div class="w-14 h-14 flex shrink-0 items-center justify-center bg-brand-50 dark:bg-brand-950/60 border border-brand-100 dark:border-brand-800/60 text-brand-600 dark:text-brand-400 rounded-2xl shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 16v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2m4 0h6m-3 0v-2"></path></svg>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight" x-text="totalJobs">{{ number_format($jobs->total() ?? 0) }}</p>
                                <p class="text-[11px] font-extrabold text-gray-500 dark:text-zinc-400 uppercase tracking-widest mt-1">{{ __('app.dashboard.total_jobs') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stat Card 2: Saved Jobs -->
                    <div @click="scope = (scope === 'saved' ? '' : 'saved'); updateDashboard()" 
                         :class="scope === 'saved' 
                            ? 'ring-2 ring-blue-500 bg-blue-50/70 dark:bg-blue-950/60 border-blue-300 dark:border-blue-700/80 shadow-md shadow-blue-500/10' 
                            : 'bg-white border-gray-200/90 hover:border-blue-300 dark:bg-zinc-800/80 dark:border-zinc-700/80 dark:hover:border-blue-500/50 dark:hover:bg-zinc-800'" 
                         class="cursor-pointer border p-6 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group">
                        
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 dark:bg-blue-500/15 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all duration-500"></div>
                        
                        <div class="flex items-center space-x-4 relative z-10">
                            <div class="w-14 h-14 flex shrink-0 items-center justify-center bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-800/60 text-blue-600 dark:text-blue-400 rounded-2xl shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight" x-text="savedJobs">{{ number_format($savedJobsCount) }}</p>
                                <p class="text-[11px] font-extrabold text-gray-500 dark:text-zinc-400 uppercase tracking-widest mt-1">{{ __('app.dashboard.saved_jobs') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stat Card 3: Applications Sent -->
                    <a href="{{ route('job-applications.index') }}" class="block border p-6 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group bg-white border-gray-200/90 hover:border-emerald-300 dark:bg-zinc-800/80 dark:border-zinc-700/80 dark:hover:border-emerald-500/50 dark:hover:bg-zinc-800">
                        
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 dark:bg-emerald-500/15 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>

                        <div class="flex items-center space-x-4 relative z-10">
                            <div class="w-14 h-14 flex shrink-0 items-center justify-center bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/60 text-emerald-600 dark:text-emerald-400 rounded-2xl shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"></path></svg>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($applicationsSentCount) }}</p>
                                <p class="text-[11px] font-extrabold text-gray-500 dark:text-zinc-400 uppercase tracking-widest mt-1">{{ __('app.dashboard.applications_sent') }}</p>
                            </div>
                        </div>
                    </a>

                    <!-- Stat Card 4: New Today -->
                    <div @click="scope = (scope === 'new_today' ? '' : 'new_today'); updateDashboard()" 
                         :class="scope === 'new_today' 
                            ? 'ring-2 ring-amber-500 bg-amber-50/70 dark:bg-amber-950/60 border-amber-300 dark:border-amber-700/80 shadow-md shadow-amber-500/10' 
                            : 'bg-white border-gray-200/90 hover:border-amber-300 dark:bg-zinc-800/80 dark:border-zinc-700/80 dark:hover:border-amber-500/50 dark:hover:bg-zinc-800'" 
                         class="cursor-pointer border p-6 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden group">
                        
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/10 dark:bg-amber-500/15 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all duration-500"></div>
                        
                        <div class="flex items-center space-x-4 relative z-10">
                            <div class="w-14 h-14 flex shrink-0 items-center justify-center bg-amber-50 dark:bg-amber-950/60 border border-amber-100 dark:border-amber-800/60 text-amber-600 dark:text-amber-400 rounded-2xl shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($newJobsTodayCount) }}</p>
                                <p class="text-[11px] font-extrabold text-gray-500 dark:text-zinc-400 uppercase tracking-widest mt-1">{{ __('app.dashboard.new_today') }}</p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Search & Filters -->
                <div class="flex flex-col gap-3 mb-fluid-8">

                    <!-- Row 1: Search Bar + Sort Dropdown -->
                    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">

                        <!-- Search Bar -->
                        <div class="flex flex-grow max-w-xl">
                            <input type="text" x-model.debounce.500ms="search" @input="updateDashboard()"
                                class="flex-grow p-fluid-2 rounded-l-2xl bg-gray-50 dark:bg-zinc-800/90 text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-brand-500 focus:border-brand-500 border border-gray-200 dark:border-zinc-700 transition-all duration-300"
                                placeholder="{{ __('app.dashboard.search_placeholder') }}">
                            <div class="bg-brand-600 hover:bg-brand-700 text-white p-fluid-2 rounded-r-2xl border border-brand-600 flex items-center justify-center px-5 shadow-sm transition-colors">
                                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center gap-2 shrink-0">
                            <label class="text-fluid-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wide whitespace-nowrap">
                                {{ __('Sort by') }}:
                            </label>
                            <select x-model="sort" @change="updateDashboard()"
                                class="rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/90 text-gray-700 dark:text-zinc-200 text-fluid-xs font-semibold px-3 py-2 pr-8 shadow-xs focus:ring-2 focus:ring-brand-500 focus:border-brand-500 cursor-pointer transition-all duration-200">
                                <option value="newest">🕐 {{ __('Newest First') }}</option>
                                <option value="match">⭐ {{ __('Best Match') }}</option>
                                <option value="salary_desc">💰 {{ __('Highest Salary') }}</option>
                                <option value="salary_asc">📉 {{ __('Lowest Salary') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Filter Pills + Clear Button -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-fluid-xs font-bold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mr-1">{{ __('Filter') }}:</span>
                        @php $filters = \App\Enums\JobType::cases(); @endphp
                        @foreach ($filters as $type)
                            <button @click="filter = (filter === '{{ $type->value }}' ? '' : '{{ $type->value }}'); updateDashboard()"
                                :class="filter === '{{ $type->value }}'
                                    ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-500/25 scale-105 border-brand-600 dark:bg-brand-500 dark:border-brand-500'
                                    : 'bg-white dark:bg-zinc-800/90 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 hover:text-brand-600 dark:hover:bg-zinc-800 dark:hover:text-white border-gray-200 dark:border-zinc-700'"
                                class="flex items-center px-4 py-1.5 rounded-full text-fluid-xs font-semibold transition-all duration-200 ease-in-out transform active:scale-95 border shadow-xs">
                                
                                <!-- Icon/Emoji (hidden when active) -->
                                <span x-show="filter !== '{{ $type->value }}'" class="mr-1.5">
                                    @switch($type->value)
                                        @case('Full-Time') 🏢 @break
                                        @case('Remote') 🌍 @break
                                        @case('Hybrid') 🔄 @break
                                        @case('Contract') 📄 @break
                                    @endswitch
                                </span>

                                <!-- Checkmark (shown when active) -->
                                <svg x-show="filter === '{{ $type->value }}'" style="display: none;" class="w-3.5 h-3.5 mr-1.5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                
                                <span>{{ $type->label() }}</span>
                            </button>
                        @endforeach

                        <!-- Clear All Filters -->
                        <button
                            x-show="hasActiveFilters"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90"
                            @click="clearFilters()"
                            class="ml-auto flex items-center gap-1.5 px-4 py-1.5 rounded-full text-fluid-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-200 dark:border-rose-800/60 transition-all duration-200 active:scale-95"
                            style="display: none;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            {{ __('Clear Filters') }}
                        </button>
                    </div>
                </div>

                <!-- Job List Container -->
                <div id="job-list-container">
                    @include('job-vacancies._list')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>