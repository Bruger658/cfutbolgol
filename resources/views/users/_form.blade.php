<div class="space-y-5">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium" for="name">Nombre</label>
            <input id="name" name="name" class="mt-1 w-full rounded border p-2" value="{{ old('name', $user->name) }}" required>
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium" for="email">Email</label>
            <input id="email" name="email" type="email" class="mt-1 w-full rounded border p-2" value="{{ old('email', $user->email) }}" required>
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium" for="role_id">Rol</label>
        <select id="role_id" name="role_id" class="mt-1 w-full rounded border p-2 bg-primary text-on-primary" style="background-color:#0b1730;color:#ffffff;border-color:#94a3b8;" required>
            <option value="" style="background-color:#0b1730;color:#ffffff;">Seleccionar rol</option>
            @foreach($roles as $role)
                 <option value="{{ $role->id }}" style="background-color:#0b1730;color:#ffffff;" @selected((int) old('role_id', $user->role_id) === $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
        @error('role_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-sm font-medium" for="password">Contraseña</label>
            <input id="password" name="password" type="password" class="mt-1 w-full rounded border p-2" @required(! $isEditing)>
            @if($isEditing)<p class="mt-1 text-xs text-on-surface-variant">Dejala vacía para conservar la contraseña actual.</p>@endif
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium" for="password_confirmation">Confirmar contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full rounded border p-2" @required(! $isEditing)>
        </div>
    </div>

    <div class="flex gap-3">
        <button class="rounded bg-blue-600 px-4 py-2 text-white">Guardar</button>
        <a href="{{ route('users.index') }}" class="text-sm text-on-surface-variant hover:text-on-surface">Cancelar</a>
    </div>
</div>