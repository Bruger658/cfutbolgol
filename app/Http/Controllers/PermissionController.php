<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::withCount('roles')->orderBy('name')->paginate(12);

        return view('permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('permissions.create', ['permission' => new Permission()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePermission($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        Permission::create($data);

        return redirect()->route('permissions.index')->with('status', 'Permiso creado correctamente.');
    }

    public function edit(Permission $permission): View
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $this->validatePermission($request, $permission);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $permission->update($data);

        return redirect()->route('permissions.index')->with('status', 'Permiso actualizado correctamente.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('permissions.index')->with('status', 'Permiso eliminado correctamente.');
    }

    private function validatePermission(Request $request, ?Permission $permission = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('permissions', 'slug')->ignore($permission)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}