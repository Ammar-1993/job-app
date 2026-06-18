<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Create Account') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">{{ __('Join our platform today') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-fluid-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-fluid-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Name') }}</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <input id="name" class="pl-11 block w-full rounded-xl shadow-sm transition-all text-fluid-base py-3 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:border-brand-500 focus:ring-brand-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-fluid-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Email') }}</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input id="email" class="pl-11 block w-full rounded-xl shadow-sm transition-all text-fluid-base py-3 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:border-brand-500 focus:ring-brand-500" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-fluid-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Password') }}</label>
            <div class="relative group" x-data="{ showPassword: false }">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" class="pl-11 pr-11 block w-full rounded-xl shadow-sm transition-all text-fluid-base py-3 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:border-brand-500 focus:ring-brand-500" x-bind:type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="••••••••" />

                <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-brand-500 dark:hover:text-brand-400 transition-colors" @click="showPassword = !showPassword" tabindex="-1">
                    <svg x-show="showPassword" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 5 8.268 7.943 9.542 12-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="!showPassword" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-fluid-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Confirm Password') }}</label>
            <div class="relative group" x-data="{ showPassword: false }">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password_confirmation" class="pl-11 pr-11 block w-full rounded-xl shadow-sm transition-all text-fluid-base py-3 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:border-brand-500 focus:ring-brand-500" x-bind:type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />

                <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-brand-500 dark:hover:text-brand-400 transition-colors" @click="showPassword = !showPassword" tabindex="-1">
                    <svg x-show="showPassword" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 5 8.268 7.943 9.542 12-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="!showPassword" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2 flex flex-col gap-4">
            <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-accent-600 hover:from-brand-700 hover:to-accent-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900 focus:ring-brand-500 transition-all duration-300 active:scale-[0.98]">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                {{ __('Register') }}
            </button>
            
            <p class="text-center text-sm text-gray-600 dark:text-gray-400 font-medium">
                {{ __('Already registered?') }}
                <a class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition font-bold ml-1" href="{{ route('login') }}">
                    {{ __('Sign in instead') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>