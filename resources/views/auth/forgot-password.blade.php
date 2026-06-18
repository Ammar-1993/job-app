<x-guest-layout>
    <div class="text-center mb-fluid-8">
        <h2 class="text-fluid-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ __('Reset Password') }}</h2>
        <p class="text-fluid-sm text-gray-500 dark:text-gray-400 mt-2 font-medium leading-relaxed">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-fluid-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-fluid-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Email') }}</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input id="email" class="pl-11 block w-full rounded-xl shadow-sm transition-all text-fluid-base py-3 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:border-brand-500 focus:ring-brand-500" type="email" name="email" :value="old('email')" required autofocus placeholder="name@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2 flex flex-col gap-4">
            <button type="submit" class="w-full flex justify-center items-center py-4 px-4 rounded-xl shadow-md text-fluid-base font-black text-white bg-gradient-to-r from-brand-600 to-accent-600 hover:from-brand-500 hover:to-accent-500 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900 focus:ring-brand-500 transition-all duration-300 transform active:scale-[0.98]">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                {{ __('Email Password Reset Link') }}
            </button>
            
            <p class="text-center text-fluid-sm text-gray-600 dark:text-gray-400 font-medium">
                <a class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition font-bold inline-flex items-center" href="{{ route('login') }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('Back to login') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
