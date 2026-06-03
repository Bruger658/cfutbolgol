<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1">Nombre del producto</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded border px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Categoría</label>
        <select name="category" class="w-full rounded border px-3 py-2" required>
            @php
                $categories = ['Indumentaria de partido', 'Entrenamiento', 'Buzo y pantalón largo', 'Camperas y camperones', 'Medias', 'Canilleras'];
            @endphp
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>
            @endforeach
        </select>
    </div>

   <div>
        <label class="block text-sm font-medium mb-1">Talle</label>
        <select name="size" class="w-full rounded border px-3 py-2" required>
            <option value="" disabled @selected(! old('size', $product->size ?? null))>Elegir talle</option>
            @php
                $sizes = ['8', '10', '12', '14', '16', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
            @endphp
            @foreach ($sizes as $size)
                <option value="{{ $size }}" @selected(old('size', $product->size ?? null) === $size)>{{ $size }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Foto del producto</label>
        <input type="file" name="image" accept="image/*" class="w-full rounded border px-3 py-2">
        @if ($product->image_url)
            <p class="mt-2 text-xs text-gray-600">Imagen actual:</p>
            <img src="{{ $product->image_url }}" alt="Imagen actual de {{ $product->name }}" class="mt-1 h-24 w-24 rounded object-cover">
        @endif
    </div>

     <div>
        <label class="block text-sm font-medium mb-1">Galería de imágenes</label>
        <textarea name="gallery_images_text" rows="3" class="w-full rounded border px-3 py-2" placeholder="Pegá una URL por línea para sumar fotos del producto">{{ old('gallery_images_text', collect($product->gallery_images ?? [])->implode("\n")) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">La foto principal se carga arriba; estas URLs se muestran como galería adicional en la tienda.</p>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Precio</label>
            <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="w-full rounded border px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Stock</label>
            <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full rounded border px-3 py-2" required>
        </div>
    </div>
</div>