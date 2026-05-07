@php($categories = ['institucional' => 'Institucional', 'edefi' => 'EDEFI', 'bafi' => 'BAFI', 'futsala' => 'Futsala', 'futsal_femenino' => 'Futsal femenino'])
<div class="space-y-4">

    <div>
        <label>Categoría</label>
        <select name="category" class="w-full border rounded p-2 bg-surface text-on-surface">
            @foreach($categories as $value => $label)
                <option value="{{ $value }}" class="bg-surface text-on-surface" @selected(old('category', $publication->category ?? 'institucional') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Título</label><input name="title" class="w-full border rounded p-2" value="{{ old('title', $publication->title ?? '') }}" required></div>
    <div><label>Mini texto</label><input name="excerpt" class="w-full border rounded p-2" maxlength="255" value="{{ old('excerpt', $publication->excerpt ?? '') }}" required></div>
    <div><label>Noticia completa</label><textarea name="content" rows="6" class="w-full border rounded p-2" required>{{ old('content', $publication->content ?? '') }}</textarea></div>
    <div><label>Foto</label><input type="file" name="image" class="w-full border rounded p-2" @empty($publication) required @endempty></div>
    <div><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $publication->is_active ?? true))> Activa</label></div>
    <button class="px-4 py-2 bg-primary text-white rounded font-bold">Guardar</button>
</div>