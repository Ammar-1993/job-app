@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block mt-1 w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 focus:border-brand-500 focus:ring-brand-500 rounded-xl transition-colors duration-300 shadow-sm']) }}>