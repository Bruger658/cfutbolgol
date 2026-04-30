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
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="image-upload-help">
            Si la imagen es muy pesada, se reduce automáticamente antes de subirla para evitar errores de carga.
        </p>
        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400 hidden" id="image-upload-status"></p>
        <div id="image-preview-wrapper" class="mt-3 hidden">
            <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Vista previa:</p>
            <img id="image-preview" alt="Vista previa de la imagen seleccionada"
                class="h-40 w-full max-w-md rounded-md border border-gray-200 object-cover dark:border-gray-700">
        </div>
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


<script>
    (() => {
        const MAX_UPLOAD_SIZE_BYTES = 2 * 1024 * 1024;
        const MAX_DIMENSION = 2200;
        const JPEG_QUALITY = 0.82;

        const fileInput = document.getElementById('image');
        const status = document.getElementById('image-upload-status');
        const previewWrapper = document.getElementById('image-preview-wrapper');
        const preview = document.getElementById('image-preview');

        if (!fileInput || !status || !previewWrapper || !preview) {
            return;
        }

        const showStatus = (message) => {
            status.textContent = message;
            status.classList.remove('hidden');
        };

        const showPreview = (blob) => {
            preview.src = URL.createObjectURL(blob);
            previewWrapper.classList.remove('hidden');
        };

        const convertImage = async (file) => {
            const imageBitmap = await createImageBitmap(file);
            const scale = Math.min(1, MAX_DIMENSION / Math.max(imageBitmap.width, imageBitmap.height));
            const width = Math.round(imageBitmap.width * scale);
            const height = Math.round(imageBitmap.height * scale);

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(imageBitmap, 0, 0, width, height);
            imageBitmap.close();

            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', JPEG_QUALITY));

            if (!blob) {
                return null;
            }

            return new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), {
                type: 'image/jpeg',
                lastModified: Date.now(),
            });
        };

        fileInput.addEventListener('change', async (event) => {
            const selected = event.target.files?.[0];

            if (!selected) {
                return;
            }

            showPreview(selected);

            if (selected.size <= MAX_UPLOAD_SIZE_BYTES) {
                showStatus('Imagen lista para subir.');
                return;
            }

            try {
                const resizedFile = await convertImage(selected);

                if (!resizedFile) {
                    showStatus('No se pudo optimizar automáticamente. Probá con una imagen más liviana.');
                    return;
                }

                if (resizedFile.size > MAX_UPLOAD_SIZE_BYTES) {
                    showStatus('La imagen sigue siendo muy grande. Probá con una imagen de menor resolución.');
                    return;
                }

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(resizedFile);
                fileInput.files = dataTransfer.files;

                showPreview(resizedFile);
                showStatus('Imagen optimizada automáticamente para subir sin errores.');
            } catch (error) {
                showStatus('No se pudo optimizar automáticamente. Probá con una imagen más liviana.');
            }
        });
    })();
</script>


