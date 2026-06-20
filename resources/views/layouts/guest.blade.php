<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Dark Mode Script to prevent FOUC -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Alpine.js Cloak -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white min-h-screen flex items-center justify-center relative overflow-x-hidden overflow-y-auto transition-colors duration-300">
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
    <div class="relative z-10 w-full min-h-screen flex flex-col justify-center items-center py-4 px-4"
        x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">

        <!-- Application Logo & Title -->
        <div class="z-10 mb-4 transform transition-transform hover:scale-105 duration-300" x-cloak x-show="show" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <a href="/" class="flex flex-col items-center">
                <div class="w-14 h-14 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl shadow-lg flex items-center justify-center p-2 mb-2 border border-gray-100 dark:border-gray-700/50">
                    <x-application-logo class="w-full h-full fill-current text-brand-600 dark:text-brand-400" />
                </div>
                <span class="text-2xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-accent-600 dark:from-brand-400 dark:to-accent-400 tracking-tight">
                    Job Vacancies
                </span>
            </a>
        </div>

        <!-- Content Card -->
        <div x-cloak x-show="show" x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
            class="w-full sm:max-w-md px-6 py-6 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border border-gray-100 dark:border-gray-700/50 rounded-3xl shadow-xl dark:shadow-2xl transition-all duration-300 hover:shadow-2xl relative z-20">
            {{ $slot }}
        </div>
    </div>
</body>

</html>