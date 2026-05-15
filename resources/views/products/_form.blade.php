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
        <label class="block text-sm font-medium mb-1">Descripción</label>
        <textarea name="description" rows="4" class="w-full rounded border px-3 py-2" required>{{ old('description', $product->description) }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">URL de la foto</label>
        <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" class="w-full rounded border px-3 py-2" placeholder="https://...">
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