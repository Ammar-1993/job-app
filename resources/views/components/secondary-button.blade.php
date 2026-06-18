<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl px-6 py-3 shadow-sm hover:shadow transition-all duration-300 transform active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900']) }}>
    {{ $slot }}
</button>
