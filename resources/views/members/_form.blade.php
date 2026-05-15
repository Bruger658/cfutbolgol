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
                    <option value="" disabled @selected(old('category', $member->category ?? '') === '')>Elegir categoría</option>
                    <option value="edefi" @selected(old('category', $member->category ?? '') === 'edefi')>Edefi</option>
                    <option value="bafi" @selected(old('category', $member->category ?? '') === 'bafi')>Bafi</option>
                    <option value="futsala" @selected(old('category', $member->category ?? '') === 'futsala')>Futsala</option>
                    <option value="femenino" @selected(old('category', $member->category ?? '') === 'femenino')>Femenino</option>
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

    {{-- <div><label><input type="checkbox" name="is_up_to_date" value="1" @checked(old('is_up_to_date', $member->is_up_to_date ?? true))> Está al día con las cuotas</label></div> --}}
    @php
        $selectedPaidMonths = collect(old('paid_months', $member->paid_months ?? []))
            ->map(fn ($month) => (int) $month)
            ->all();
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
    @endphp

    <div>
        <label class="block mb-2">Meses pagos</label>
        <p class="text-sm text-gray-500 mb-2">Marcá los meses abonados. El sistema calcula automáticamente si está al día.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            @foreach($months as $monthNumber => $monthName)
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="paid_months[]" value="{{ $monthNumber }}" @checked(in_array($monthNumber, $selectedPaidMonths, true))>
                    <span>{{ $monthName }}</span>
                </label>
            @endforeach
        </div>
    </div>
    
    <div class="flex items-center gap-3">
        <button class="rounded bg-blue-600 px-4 py-2 text-white">Guardar</button>
        <a href="{{ route('members.index') }}" class="text-sm text-on-surface-variant hover:text-on-surface">Cancelar</a>
    </div>
</div>