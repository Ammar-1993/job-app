<div class="space-y-fluid-4 mt-fluid-6" x-data="{ loading: false }" 
    @jobs-updating.window="loading = true" 
    @jobs-updated.window="loading = false">
    
    <!-- Skeleton Loader (Visible during async updates) -->
    <div x-show="loading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-4">
        @for ($i = 0; $i < 3; $i++)
            <div class="bg-gray-50 dark:bg-gray-800/60 p-fluid-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 animate-pulse">
                <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-4"></div>
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
            </div>
        @endfor
    </div>

    <!-- Actual Content -->
    <div x-show="!loading" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        @forelse ($jobs as $job)
            <div class="bg-white dark:bg-gray-800/40 p-fluid-6 rounded-2xl shadow-sm hover:shadow-xl dark:hover:bg-gray-700/50 transition-all duration-300 border border-gray-100 dark:border-gray-700/50 group">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex-grow">
                        <a href="{{ route('job-vacancies.show', $job->id) }}"
                            class="text-fluid-lg font-bold text-brand-600 dark:text-brand-400 group-hover:text-brand-700 dark:group-hover:text-brand-300 transition-colors duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1v-3.25M17 14V9.5a4.5 4.5 0 00-9 0V14h9zM12 3v1M20 10l-1 .75M4 10l1 .75"></path></svg>
                            {{ $job->title }}
                        </a>
                        <p class="text-fluid-base text-gray-600 dark:text-gray-300 mt-1">
                            @if($job->company)
                                <span class="font-bold">{{ $job->company->name }}</span>
                                <span class="mx-2 text-gray-400">•</span>
                                <span class="flex items-center mt-1">
                                    <svg class="w-4 h-4 mr-1 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $job->location }}
                                </span>
                            @endif
                        </p>
                        <div class="flex flex-wrap items-center gap-4 mt-3">
                            <p class="text-fluid-sm text-gray-500 dark:text-gray-400 flex items-center bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1 rounded-lg">
                                <svg class="w-4 h-4 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V9m0 3v2.25M8 12h8m-11 3.5a9 9 0 1118 0M3 12a9 9 0 009 9c1.674 0 3.298-.488 4.657-1.404"></path></svg>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ '$' . number_format($job->salary) }}</span>
                                <span class="ml-1">/ year</span>
                            </p>
                            <span class="bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 px-3 py-1 rounded-lg text-fluid-xs font-bold uppercase tracking-wider">
                                {{ $job->type }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 shrink-0">
                        <form action="{{ route('job-vacancies.save', $job->id) }}" method="POST" class="inline-block" @submit.prevent="
                            fetch($el.action, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                body: new FormData($el)
                            }).then(() => {
                                // Logic for updating heart icon could go here
                                // For now, we reload the dashboard data
                                $dispatch('job-saved');
                            })
                        ">
                            @csrf
                            <button type="submit" class="p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 transform active:scale-95 group/save">
                                @if(auth()->user()->savedJobs->contains($job->id))
                                    <svg class="w-6 h-6 text-accent-500 fill-current animate-pulse" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                @else
                                    <svg class="w-6 h-6 text-gray-400 group-hover/save:text-accent-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                @endif
                            </button>
                        </form>
                        <a href="{{ route('job-vacancies.show', $job->id) }}" class="bg-brand-600 text-white px-6 py-3 rounded-xl hover:bg-brand-700 active:scale-95 transition-all duration-200 text-fluid-xs font-black shadow-lg shadow-brand-500/20 uppercase tracking-widest">
                            {{ __('app.dashboard.view_details') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-20 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="bg-gray-100 dark:bg-gray-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-brand-600 dark:text-brand-400 text-fluid-xl font-black tracking-tight">{{ __('app.dashboard.no_jobs') }}</p>
                <p class="text-gray-500 dark:text-gray-400 mt-2 text-fluid-base">{{ __('app.dashboard.no_jobs_subtitle') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-fluid-12">
        {{ $jobs->links() }}
    </div>
</div>