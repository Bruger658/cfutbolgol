<div class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium">Nombre y apellido</label>
            <input id="name" name="name" type="text" class="mt-1 w-full rounded border p-2" value="{{ old('name', $staff->name ?? '') }}" required>
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="role" class="block text-sm font-medium">Cargo / rol</label>
            <input id="role" name="role" type="text" class="mt-1 w-full rounded border p-2" value="{{ old('role', $staff->role ?? '') }}" placeholder="Ej: Director técnico" required>
            @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="category" class="block text-sm font-medium">Categoría / área</label>
            <input id="category" name="category" type="text" class="mt-1 w-full rounded border p-2" value="{{ old('category', $staff->category ?? '') }}" placeholder="Ej: EDEFI, Futsala, Coordinación">
            @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="display_order" class="block text-sm font-medium">Orden de visualización</label>
            <input id="display_order" name="display_order" type="number" min="0" class="mt-1 w-full rounded border p-2" value="{{ old('display_order', $staff->display_order ?? 0) }}">
            <p class="mt-1 text-xs text-on-surface-variant">Los números más bajos aparecen primero.</p>
            @error('display_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="bio" class="block text-sm font-medium">Biografía / descripción</label>
        <textarea id="bio" name="bio" rows="4" class="mt-1 w-full rounded border p-2" placeholder="Resumen de experiencia, funciones o trayectoria">{{ old('bio', $staff->bio ?? '') }}</textarea>
        @error('bio')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input id="email" name="email" type="email" class="mt-1 w-full rounded border p-2" value="{{ old('email', $staff->email ?? '') }}">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium">Teléfono</label>
            <input id="phone" name="phone" type="text" class="mt-1 w-full rounded border p-2" value="{{ old('phone', $staff->phone ?? '') }}">
            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="photo" class="block text-sm font-medium">Foto</label>
        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 w-full rounded border p-2">
        <p class="mt-1 text-xs text-on-surface-variant">Formatos de imagen hasta 4 MB.</p>
        @if(!empty($staff->photo_path))
            <img src="{{ asset('storage/' . $staff->photo_path) }}" class="mt-3 h-28 w-28 rounded-2xl object-cover" alt="Foto de {{ $staff->name }}">
        @endif
        @error('photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-2">
        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300" @checked(old('is_active', $staff->is_active ?? true))>
        <label for="is_active" class="text-sm font-medium">Activo (visible en la web pública)</label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Guardar</button>
        <a href="{{ route('staff.index') }}" class="text-sm text-on-surface-variant hover:text-on-surface">Cancelar</a>
    </div>
</div>