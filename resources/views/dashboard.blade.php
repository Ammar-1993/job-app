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
            loading: false,
            updateDashboard() {
                this.loading = true;
                $dispatch('jobs-updating');
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.search);
                if (this.filter) {
                    url.searchParams.set('filter', this.filter);
                } else {
                    url.searchParams.delete('filter');
                }
                
                window.history.pushState({}, '', url);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('job-list-container').innerHTML = html;
                    this.loading = false;
                    $dispatch('jobs-updated');
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
                                <p class="text-fluid-xl font-bold text-gray-900 dark:text-white">{{ number_format($jobs->total() ?? 0) }}</p>
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
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-fluid-4 mb-fluid-8">
                    
                    <!-- Search Bar -->
                    <div class="flex flex-grow max-w-lg">
                        <input type="text" x-model.debounce.500ms="search" @input="updateDashboard()"
                            class="flex-grow p-fluid-2 rounded-l-2xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-brand-500 focus:border-brand-500 border border-gray-200 dark:border-gray-700 transition-all duration-300"
                            placeholder="{{ __('app.dashboard.search_placeholder') }}">
                        <div class="bg-brand-600 text-white p-fluid-2 rounded-r-2xl border border-brand-600 flex items-center justify-center px-6 shadow-md">
                            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-2">
                        @php $filters = \App\Enums\JobType::cases(); @endphp
                        @foreach ($filters as $type)
                            <button @click="filter = (filter === '{{ $type->value }}' ? '' : '{{ $type->value }}'); updateDashboard()"
                                :class="filter === '{{ $type->value }}' 
                                    ? 'bg-brand-600 text-white font-semibold ring-2 ring-brand-300 shadow-md' 
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-brand-500 hover:text-white dark:hover:bg-brand-600'"
                                class="px-fluid-4 py-fluid-2 rounded-full text-fluid-xs transition-all duration-200 ease-in-out transform active:scale-95">
                                {{ $type->label() }}
                            </button>
                        @endforeach
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