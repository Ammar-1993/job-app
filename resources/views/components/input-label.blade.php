@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-xs text-gray-700 dark:text-zinc-300 uppercase tracking-wider mb-1']) }}>
    {{ $value ?? $slot }}
</label>
