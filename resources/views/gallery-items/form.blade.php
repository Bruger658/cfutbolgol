@php
    $isEditing = isset($galleryItem);
@endphp

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
        <input id="title" name="title" type="text" value="{{ old('title', $galleryItem->title ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
            required>
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- <div>
        <label for="image_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL de imagen</label>
        <input id="image_url" name="image_url" type="url" value="{{ old('image_url', $galleryItem->image_url ?? '') }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
            required>
        @error('image_url')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div> --}}

    <div class="mb-4">
    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Imagen</label>
    <input
        type="file"
        name="image"
        id="image"
        accept="image/*"
        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
        @if(!$isEditing) required @endif
    >
    @error('image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
        <textarea id="description" name="description" rows="4"
            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $galleryItem->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition-colors">
        {{ $isEditing ? 'Actualizar' : 'Guardar' }}
    </button>
    <a href="{{ route('gallery-items.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">Cancelar</a>
</div>