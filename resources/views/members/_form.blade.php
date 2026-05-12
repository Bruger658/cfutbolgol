<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label>Nombre</label>
            <input name="first_name" 
            class="w-full border rounded p-2" 
            value="{{ old('first_name', $member->first_name ?? '') }}" required>
        </div>
        <div><label>Apellido</label><input name="last_name" class="w-full border rounded p-2" value="{{ old('last_name', $member->last_name ?? '') }}" required></div>
    </div>

    {{-- <div><label>Categoría</label><input name="category" class="w-full border rounded p-2" value="{{ old('category', $member->category ?? '') }}" placeholder="Ej: Edefi" required></div> --}}

    

            <div class="space-y-4">
                <label >
                    Categoría
                </label>
                <select id="category" name="category"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                    <option value="" disabled selected>Elegir categoría</option>
                    <option value="edefi">Edefi</option>
                    <option value="bafi">Bafi</option>
                    <option value="futsala">Futsala</option>
                    <option value="femenino">Femenino</option>
                </select>
            </div>
        

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label>Documento</label><input name="document_number" class="w-full border rounded p-2" value="{{ old('document_number', $member->document_number ?? '') }}" required></div>
        <div><label>Teléfono</label><input name="phone" class="w-full border rounded p-2" value="{{ old('phone', $member->phone ?? '') }}" required></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label>Dirección</label><input name="address" class="w-full border rounded p-2" value="{{ old('address', $member->address ?? '') }}" required></div>
        <div><label>Localidad</label><input name="city" class="w-full border rounded p-2" value="{{ old('city', $member->city ?? '') }}" required></div>
    </div>

    <div><label>Teléfono adulto responsable (opcional)</label><input name="responsible_adult_phone" class="w-full border rounded p-2" value="{{ old('responsible_adult_phone', $member->responsible_adult_phone ?? '') }}"></div>

    <div><label><input type="checkbox" name="is_up_to_date" value="1" @checked(old('is_up_to_date', $member->is_up_to_date ?? true))> Está al día con las cuotas</label></div>

    <div class="flex items-center gap-3">
        <button class="rounded bg-blue-600 px-4 py-2 text-white">Guardar</button>
        <a href="{{ route('members.index') }}" class="text-sm text-on-surface-variant hover:text-on-surface">Cancelar</a>
    </div>
</div>