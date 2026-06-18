<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl px-6 py-3 shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900']) }}>
    {{ $slot }}
</button>
