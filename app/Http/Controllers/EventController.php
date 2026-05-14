<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $viewMode = $request->query('view', 'month');
        $baseDate = Carbon::parse($request->query('date', now()->toDateString()));

        [$start, $end] = match ($viewMode) {
            'day' => [$baseDate->copy()->startOfDay(), $baseDate->copy()->endOfDay()],
            'week' => [$baseDate->copy()->startOfWeek(), $baseDate->copy()->endOfWeek()],
            default => [$baseDate->copy()->startOfMonth(), $baseDate->copy()->endOfMonth()],
        };

        $showCompleted = $request->boolean('show_completed');

        $events = Event::query()
            ->when(! $showCompleted, fn ($query) => $query->where('is_completed', false))
            ->whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get();

        return view('events.index', compact('events', 'viewMode', 'baseDate', 'start', 'end', 'showCompleted'));
    }

    public function create(): View
    {
        return view('events.create', ['event' => new Event(['starts_at' => now()->format('Y-m-d\\TH:i')])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Event::create($this->validateEvent($request));

        return redirect()->route('events.index')->with('status', 'Evento creado correctamente.');
    }

    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validateEvent($request);
        $data['is_completed'] = $request->boolean('is_completed');
        $data['completed_at'] = $data['is_completed'] ? now() : null;

        $event->update($data);

        return redirect()->route('events.index')->with('status', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events.index')->with('status', 'Evento eliminado correctamente.');
    }

    public function toggle(Event $event): RedirectResponse
    {
        $isCompleted = ! $event->is_completed;
        $event->update([
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        return back()->with('status', $isCompleted ? 'Evento marcado como realizado.' : 'Evento reactivado.');
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_completed' => ['nullable', 'boolean'],
        ]);
    }
}


