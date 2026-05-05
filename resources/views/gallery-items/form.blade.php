@php
    $isEditing = isset($galleryItem);
@endphp

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-white-700">Título</label>
        <input id="title" name="title" type="text" value="{{ old('title', $galleryItem->title ?? '') }}" class="mt-1 block w-full rounded-md border-white-900  shadow-sm" required>
    </div>

    <div class="mb-4">
        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Imagen</label>
        <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300" @if(!$isEditing) required @endif>
        @error('image')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

     <div class="flex items-center gap-2">
        <input
            id="is_active"
            name="is_active"
            type="checkbox"
            value="1"
            @checked(old('is_active', $galleryItem->is_active))
            class="rounded border-gray-300"
        >
        <label for="is_active" class="text-sm font-medium text-gray-700">
            Activa (visible en galería)
        </label>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition-colors">{{ $isEditing ? 'Actualizar' : 'Guardar' }}</button>
        <a href="{{ route('gallery-items.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
    </div>

   
</div> 

    



{{-- <div class="mt-6 flex items-center gap-3">
    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition-colors">
        {{ $isEditing ? 'Actualizar' : 'Guardar' }}
    </button>
    <a href="{{ route('gallery-items.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100">Cancelar</a>
</div> --}}


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