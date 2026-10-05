<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex shrink-0 items-center justify-center bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400 rounded-xl shadow-xs">
                <span class="text-xl">⚙️</span>
            </div>
            <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white tracking-tight">
                {{ __('app.profile.settings') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-fluid-8 bg-slate-50/60 dark:bg-zinc-950/40 transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-fluid-8">
            
            <!-- Update Profile Information Card -->
            <div class="bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md rounded-3xl p-6 sm:p-fluid-8 border border-gray-200/80 dark:border-zinc-800 transition-all duration-300 relative overflow-hidden">
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md rounded-3xl p-6 sm:p-fluid-8 border border-gray-200/80 dark:border-zinc-800 transition-all duration-300 relative overflow-hidden">
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete User Card -->
            <div class="bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md rounded-3xl p-6 sm:p-fluid-8 border border-gray-200/80 dark:border-zinc-800 transition-all duration-300 relative overflow-hidden">
                <div class="max-w-2xl relative z-10">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>