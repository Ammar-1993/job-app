<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('app.dashboard.title') }}
        </h2>
    </x-slot>

    <div class="py-fluid-8 transition-colors duration-300">
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
                    <div class="bg-brand-50 dark:bg-brand-900/40 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-brand-500 transition-all duration-300">
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
                    <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-blue-500 transition-all duration-300">
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
                    <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-emerald-500 transition-all duration-300">
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
                    <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm dark:shadow-lg hover:ring-2 ring-amber-500 transition-all duration-300">
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
                    <form action="{{ route('dashboard') }}" method="get" class="flex flex-grow max-w-lg">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-grow p-fluid-2 rounded-l-2xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-brand-500 focus:border-brand-500 border border-gray-200 dark:border-gray-700 transition-colors"
                            placeholder="{{ __('app.dashboard.search_placeholder') }}">
                        <button type="submit"
                            class="bg-brand-600 text-white p-fluid-2 rounded-r-2xl border border-brand-600 hover:bg-brand-700 transition duration-150 shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-2">
                        @php $filters = \App\Enums\JobType::cases(); @endphp
                        @foreach ($filters as $type)
                            @php
                                $isActive = request('filter') === $type->value;
                                $class = $isActive 
                                    ? 'bg-brand-600 text-white font-semibold ring-2 ring-brand-300 shadow-md' 
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-brand-500 hover:text-white dark:hover:bg-brand-600';
                                $route = route('dashboard', ['filter' => $isActive ? null : $type->value, 'search' => request('search')]);
                            @endphp
                            <a href="{{ $route }}" class="{{ $class }} px-fluid-4 py-fluid-2 rounded-full text-fluid-xs transition duration-200 ease-in-out">
                                {{ $type->label() }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Job List -->
                <div class="space-y-fluid-4 mt-fluid-6" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 800)">
                    
                    <!-- Skeleton Loader -->
                    <template x-if="loading">
                        <div class="space-y-4">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 animate-pulse">
                                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-4"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                                </div>
                            @endfor
                        </div>
                    </template>

                    <!-- Actual Content -->
                    <div x-show="!loading" style="display: none;">
                        @forelse ($jobs as $job)
                        <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-6 rounded-2xl shadow-sm dark:shadow-lg hover:shadow-md dark:hover:bg-gray-700/70 transition-all duration-200 border border-gray-100 dark:border-gray-700/50">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                                <div class="mb-4 md:mb-0">
                                    <a href="{{ route('job-vacancies.show', $job->id) }}"
                                        class="text-fluid-lg font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 hover:underline transition duration-150 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1v-3.25M17 14V9.5a4.5 4.5 0 00-9 0V14h9zM12 3v1M20 10l-1 .75M4 10l1 .75"></path></svg>
                                        {{ $job->title }}
                                    </a>
                                    <p class="text-fluid-base text-gray-600 dark:text-gray-300 mt-1">
                                        @if($job->company)
                                            <span class="font-medium">{{ $job->company->name }}</span>
                                            <span class="mx-2 text-gray-400">•</span>
                                            <span class="flex items-center inline-flex mt-1">
                                                <svg class="w-4 h-4 mr-1 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                                {{ $job->location }}
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-fluid-sm text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V9m0 3v2.25M8 12h8m-11 3.5a9 9 0 1118 0M3 12a9 9 0 009 9c1.674 0 3.298-.488 4.657-1.404"></path></svg>
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ '$' . number_format($job->salary) }}</span> {{ __('app.job.salary_year') }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="bg-brand-100 dark:bg-brand-900 text-brand-700 dark:text-brand-200 px-4 py-1.5 rounded-full text-fluid-xs font-semibold">
                                        {{ $job->type }}
                                    </span>
                                    <form action="{{ route('job-vacancies.save', $job->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150">
                                            @if(auth()->user()->savedJobs->contains($job->id))
                                                <svg class="w-6 h-6 text-accent-500 fill-current" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            @else
                                                <svg class="w-6 h-6 text-gray-400 hover:text-accent-500 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <a href="{{ route('job-vacancies.show', $job->id) }}" class="bg-brand-600 text-white px-fluid-4 py-fluid-2 rounded-xl hover:bg-brand-700 transition duration-150 text-fluid-xs font-bold shadow-md">
                                        {{ __('app.dashboard.view_details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-10 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                            <p class="text-brand-600 dark:text-brand-400 text-fluid-xl font-bold">{{ __('app.dashboard.no_jobs') }}</p>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('app.dashboard.no_jobs_subtitle') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-fluid-8">
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>