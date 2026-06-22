<div class="space-y-5">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div><label for="name" class="block text-sm font-medium">Nombre</label><input id="name" name="name"
                class="mt-1 w-full rounded border p-2" value="{{ old('name', $permission->name) }}" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div><label for="slug" class="block text-sm font-medium">Slug</label><input id="slug" name="slug"
                class="mt-1 w-full rounded border p-2" value="{{ old('slug', $permission->slug) }}"
                placeholder="ej: manage-content">
            @error('slug')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div><label for="description" class="block text-sm font-medium">Descripción</label>
        <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded border p-2">{{ old('description', $permission->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex gap-3"><button class="rounded bg-blue-600 px-4 py-2 text-white">Guardar</button><a
            href="{{ route('permissions.index') }}"
            class="text-sm text-on-surface-variant hover:text-on-surface">Cancelar</a></div>
</div>
