<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $staffMembers = Staff::query()
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(12);

        return view('staff.index', compact('staffMembers'));
    }

    public function create(): View
    {
        return view('staff.create', [
            'staff' => new Staff([
                'display_order' => 0,
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateStaff($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('staff', 'public');
        }

        Staff::create($data);

        return redirect()->route('staff.index')->with('status', 'Integrante del staff creado correctamente.');
    }

    public function edit(Staff $staff): View
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $data = $this->validateStaff($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;

        if ($request->hasFile('photo')) {
            if ($staff->photo_path) {
                Storage::disk('public')->delete($staff->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('staff', 'public');
        }

        $staff->update($data);

        return redirect()->route('staff.index')->with('status', 'Integrante del staff actualizado correctamente.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        if ($staff->photo_path) {
            Storage::disk('public')->delete($staff->photo_path);
        }

        $staff->delete();

        return redirect()->route('staff.index')->with('status', 'Integrante del staff eliminado correctamente.');
    }

    private function validateStaff(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}