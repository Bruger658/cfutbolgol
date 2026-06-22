<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->paginate(12);

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.create', [
            'role' => new Role(),
            'permissions' => Permission::orderBy('name')->get(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRole($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        $role = Role::create($data);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.index')->with('status', 'Rol creado correctamente.');
    }

    public function edit(Role $role): View
    {
        return view('roles.edit', [
            'role' => $role,
            'permissions' => Permission::orderBy('name')->get(),
            'selectedPermissions' => $role->permissions()->pluck('permissions.id')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $this->validateRole($request, $role);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        $role->update($data);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('roles.index')->with('status', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with('status', 'No se puede borrar un rol asignado a usuarios.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('status', 'Rol eliminado correctamente.');
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('roles', 'slug')->ignore($role)],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);
    }
}