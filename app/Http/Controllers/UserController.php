<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Support\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roleModel')->orderBy('name')->paginate(12);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', [
            'user' => new User(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUser($request);
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $this->legacyRoleFor($data['role_id'] ?? null);

        User::create($data);

        return redirect()->route('users.index')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validateUser($request, $user);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['role'] = $this->legacyRoleFor($data['role_id'] ?? null);
        $user->update($data);

        return redirect()->route('users.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('status', 'No podés eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'Usuario eliminado correctamente.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => $passwordRules,
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
        ]);
    }

    private function legacyRoleFor(?int $roleId): string
    {
        $role = Role::find($roleId);

        return $role?->slug && in_array($role->slug, UserRole::values(), true)
            ? $role->slug
            : UserRole::SOCIO;
    }
}