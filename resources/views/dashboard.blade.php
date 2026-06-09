<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('app.dashboard.title') }}
        </h2>
    </x-slot>

    <div class="py-fluid-8 transition-colors duration-300" 
        x-data="{ 
            search: '{{ request('search') }}', 
            filter: '{{ request('filter') }}',
            sort: '{{ request('sort', 'newest') }}',
            totalJobs: {{ $jobs->total() ?? 0 }},
            loading: false,
            get hasActiveFilters() {
                return this.search !== '' || this.filter !== '' || this.sort !== 'newest';
            },
            clearFilters() {
                this.search = '';
                this.filter = '';
                this.sort = 'newest';
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
                if (this.sort && this.sort !== 'newest') url.searchParams.set('sort', this.sort);
                else url.searchParams.delete('sort');

                window.history.pushState({}, '', url);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('job-list-container').innerHTML = data.html;
                    this.totalJobs = data.total;
                    this.loading = false;
                    $dispatch('jobs-updated');
                })
                .catch(error => {
                    console.error('Error fetching jobs:', error);
                    this.loading = false;
                });
            }
        }"
        @job-saved.window="updateDashboard()"
    >
        <!-- Main Content Wrapper -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-xl dark:shadow-2xl rounded-2xl p-fluid-8 border border-gray-200 dark:border-indigo-700/30 transition-colors duration-300">
                
                <!-- Welcome Section -->
                <h3 class="text-gray-900 dark:text-white text-fluid-xl font-extrabold mb-fluid-8 border-b border-gray-100 dark:border-indigo-500/50 pb-fluid-4 transition-colors duration-300">
                   👋  {{ __('app.dashboard.welcome_back') }} <span class="text-brand-600 dark:text-brand-400">{{ Auth::user()->name }}</span>!
                </h3>

                <!-- Stats Overview Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-fluid-4 mb-fluid-12">
                    <!-- Stat Card 1: Total Jobs -->
                    <div class="bg-brand-50 dark:bg-brand-900/40 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-brand-500 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-brand-500 rounded-2xl text-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 16v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2m4 0h6m-3 0v-2"></path></svg>
                            </div>
                            <div>
                                <p class="text-fluid-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.dashboard.total_jobs') }}</p>
                                <p class="text-fluid-xl font-bold text-gray-900 dark:text-white" x-text="totalJobs">{{ number_format($jobs->total() ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stat Card 2: Saved Jobs -->
                    <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-blue-500 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-blue-500 rounded-2xl text-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-fluid-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.dashboard.saved_jobs') }}</p>
                                <p class="text-fluid-xl font-bold text-gray-900 dark:text-white">{{ number_format($savedJobsCount) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stat Card 3: Applications Sent -->
                    <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-emerald-500 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-emerald-500 rounded-2xl text-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </div>
                            <div>
                                <p class="text-fluid-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.dashboard.applications_sent') }}</p>
                                <p class="text-fluid-xl font-bold text-gray-900 dark:text-white">{{ number_format($applicationsSentCount) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stat Card 4: New Today -->
                    <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-amber-500 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-amber-500 rounded-2xl text-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-fluid-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.dashboard.new_today') }}</p>
                                <p class="text-fluid-xl font-bold text-gray-900 dark:text-white">{{ number_format($newJobsTodayCount) }}</p>
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
                                class="flex-grow p-fluid-2 rounded-l-2xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-brand-500 focus:border-brand-500 border border-gray-200 dark:border-gray-700 transition-all duration-300"
                                placeholder="{{ __('app.dashboard.search_placeholder') }}">
                            <div class="bg-brand-600 text-white p-fluid-2 rounded-r-2xl border border-brand-600 flex items-center justify-center px-5 shadow-md">
                                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center gap-2 shrink-0">
                            <label class="text-fluid-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">
                                {{ __('Sort by') }}:
                            </label>
                            <select x-model="sort" @change="updateDashboard()"
                                class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-fluid-xs font-medium px-3 py-2 pr-8 shadow-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 cursor-pointer transition-all duration-200">
                                <option value="newest">🕐 {{ __('Newest First') }}</option>
                                <option value="match">⭐ {{ __('Best Match') }}</option>
                                <option value="salary_desc">💰 {{ __('Highest Salary') }}</option>
                                <option value="salary_asc">📉 {{ __('Lowest Salary') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Filter Pills + Clear Button -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-fluid-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mr-1">{{ __('Filter') }}:</span>
                        @php $filters = \App\Enums\JobType::cases(); @endphp
                        @foreach ($filters as $type)
                            <button @click="filter = (filter === '{{ $type->value }}' ? '' : '{{ $type->value }}'); updateDashboard()"
                                :class="filter === '{{ $type->value }}'
                                    ? 'bg-brand-600 text-white font-bold ring-2 ring-brand-400 shadow-lg scale-105'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-brand-100 hover:text-brand-700 dark:hover:bg-brand-900/40 dark:hover:text-brand-300'"
                                class="px-4 py-1.5 rounded-full text-fluid-xs transition-all duration-200 ease-in-out transform active:scale-95 border border-transparent"
                                :style="filter === '{{ $type->value }}' ? 'border-color: rgb(var(--color-brand-400, 99 102 241) / 0.5)' : ''">
                                @switch($type->value)
                                    @case('Full-Time') 🏢 @break
                                    @case('Contract') 📄 @break
                                    @case('Remote') 🌍 @break
                                    @case('Hybrid') 🔄 @break
                                @endswitch
                                {{ $type->label() }}
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
                            class="ml-auto flex items-center gap-1.5 px-4 py-1.5 rounded-full text-fluid-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 border border-red-200 dark:border-red-800/50 transition-all duration-200 active:scale-95"
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