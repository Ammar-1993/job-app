<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Alpine.js Cloak -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white min-h-screen flex items-center justify-center relative overflow-hidden transition-colors duration-300">
    <!-- Floating Background Shapes -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
            class="absolute top-[15%] left-[-10%] md:left-[-5%] w-[600px] h-[140px] rotate-12 bg-brand-500/10 dark:bg-brand-500/15 blur-3xl rounded-full transition-colors duration-300">
        </div>
        <div
            class="absolute top-[70%] right-[-5%] md:right-[0%] w-[500px] h-[120px] -rotate-15 bg-accent-500/10 dark:bg-accent-500/15 blur-3xl rounded-full transition-colors duration-300">
        </div>
        <div
            class="absolute bottom-[5%] left-[5%] md:left-[10%] w-[300px] h-[80px] -rotate-8 bg-indigo-500/10 dark:bg-indigo-500/15 blur-3xl rounded-full transition-colors duration-300">
        </div>
        <div
            class="absolute top-[10%] right-[15%] md:right-[20%] w-[200px] h-[60px] rotate-20 bg-emerald-500/10 dark:bg-emerald-500/15 blur-3xl rounded-full transition-colors duration-300">
        </div>
    </div>

    <!-- Bottom Gradient -->
    <div class="absolute inset-0 bg-gradient-to-t from-gray-50 dark:from-gray-900 via-transparent to-gray-50/80 dark:to-gray-900/80 pointer-events-none transition-colors duration-300"></div>

    <!-- Main Content Container -->
    <div class="relative z-10 w-full min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0"
        x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
        <!-- Application Logo -->
        <div class="mb-8" x-cloak x-show="show" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" />
            </a>
        </div>

        <!-- Content Card -->
        <div x-cloak x-show="show" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
            class="w-full sm:max-w-md px-6 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border border-gray-100 dark:border-gray-700/50 rounded-3xl shadow-xl dark:shadow-2xl transition-all duration-300 hover:shadow-2xl relative z-20">
            {{ $slot }}
        </div>
    </div>
</body>

</html>