<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1">Título</label>
        <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full rounded border px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Fecha y hora</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\\TH:i') ?? $event->starts_at) }}" class="w-full rounded border px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Descripción</label>
        <textarea name="description" rows="4" class="w-full rounded border px-3 py-2">{{ old('description', $event->description) }}</textarea>
    </div>
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_completed" value="1" @checked(old('is_completed', $event->is_completed))>
        <span>Realizado (si está tildado no aparece por defecto).</span>
    </label>
</div>