@props([
    'label',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'error' => false,
    'class' => '',
    'labelClass' => '',
])

@if ($label)
    <label for="{{ $name }}"
        {{ $attributes->merge(['class' => 'block ml-1 text-sm font-medium text-slate-700 dark:text-slate-200 mb-1 ' . $labelClass]) }}>
        {{ $label }}
    </label>
@endif

<input type="{{ $type }}" id="{{ $name }}" placeholder="{{ $placeholder }}" name="{{ $name }}"
    {{ $attributes->merge(['class' => 'w-full px-4 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue']) }}>
@error($name)
    <span class="text-red-600">{{ $message }}</span>
@enderror
