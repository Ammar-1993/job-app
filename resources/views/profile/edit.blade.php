<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-fluid-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('app.profile.settings') }}
        </h2>
    </x-slot>

    <div class="py-fluid-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-fluid-8">
            
            <!-- Update Profile Information Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl dark:shadow-2xl rounded-3xl p-6 sm:p-fluid-8 border border-gray-100 dark:border-gray-700/50 transition-colors duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-500 to-indigo-600"></div>
                <div class="max-w-xl relative z-10">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl dark:shadow-2xl rounded-3xl p-6 sm:p-fluid-8 border border-gray-100 dark:border-gray-700/50 transition-colors duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
                <div class="max-w-xl relative z-10">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete User Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl dark:shadow-2xl rounded-3xl p-6 sm:p-fluid-8 border border-gray-100 dark:border-gray-700/50 transition-colors duration-300 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-rose-500 to-red-600"></div>
                <div class="max-w-xl relative z-10">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>