@props(['label', 'name', 'value' => null])

<label for="{{ $name }}"
    {{ $attributes->merge(['class' => 'flex items-center text-sm text-slate-700 dark:text-slate-200']) }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}"
        {{ $attributes }} class="h-4 w-4 text-brand-blue focus:ring-brand-blue/40 border-slate-300 dark:border-slate-600 rounded mr-1">
    {{ $label }}
</label>

@error($name)
    <span class="text-red-600">{{ $message }}</span>
@enderror
