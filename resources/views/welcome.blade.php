
<x-main-layout title="Hire Me - Find your dream job">
    <!-- Application Logo Presentation -->
    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        <div class="flex justify-center mb-fluid-6" x-cloak x-show="show"
            x-transition:enter="transition ease-out duration-1000 transform" x-transition:enter-start="opacity-0 -translate-y-8 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            <div class="relative group cursor-default">
                <!-- Glowing effect behind logo -->
                <div class="absolute -inset-2 bg-gradient-to-r from-brand-500 to-accent-500 rounded-2xl blur-xl opacity-20 group-hover:opacity-40 transition duration-500"></div>
                <!-- Logo container -->
                <div class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm p-4 rounded-2xl border border-gray-100 dark:border-white/10 shadow-xl dark:shadow-2xl transform group-hover:-translate-y-1 transition duration-500">
                    <x-application-logo class="w-16 h-16 sm:w-20 sm:h-20 fill-current text-brand-600 dark:text-brand-400" />
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
        <div class="inline-flex items-center mb-fluid-2" x-cloak x-show="show"
            x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100">
            <h4 class="text-fluid-xs text-gray-600 dark:text-white/60 rounded-full bg-gray-100 dark:bg-white/10 px-4 py-1.5 font-medium tracking-wide uppercase">{{ __('app.welcome.hire_me') }}</h4>
        </div>
    </div>

    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
        <div x-cloak x-show="show" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <h1 class="text-fluid-3xl sm:text-fluid-4xl font-black mb-fluid-6 tracking-tight leading-none">
                <span class="text-gray-900 dark:text-white">{{ __('app.welcome.hero_title1') }}</span><br />
                <span class="text-gray-500 dark:text-white/60 font-serif italic">{{ __('app.welcome.hero_title2') }}</span>
            </h1>
        </div>
    </div>

    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
        <div class="mb-fluid-8" x-cloak x-show="show" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <p class="text-gray-600 dark:text-white/60 text-fluid-base max-w-2xl mx-auto leading-relaxed"> {{ __('app.welcome.hero_subtitle') }}</p>
        </div>
    </div>

    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
        <div x-cloak x-show="show" class="flex flex-col sm:flex-row items-center justify-center gap-4" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <a href="{{ route('register') }}" class="w-full sm:w-auto rounded-xl bg-gray-100 dark:bg-white/10 px-8 py-3 text-gray-900 dark:text-white font-bold hover:bg-gray-200 dark:hover:bg-white/20 transition-all duration-300">{{ __('app.welcome.create_account') }}</a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto rounded-xl bg-gradient-to-r from-brand-600 to-accent-600 px-8 py-3 text-white font-bold shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 transform hover:-translate-y-1 transition-all duration-300">{{ __('app.welcome.login') }}</a>
        </div>
    </div>
</x-main-layout>