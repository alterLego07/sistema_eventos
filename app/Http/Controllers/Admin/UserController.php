<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Manage the users that belong to the current company.
 *
 * Users are scoped manually (not via a global scope) to keep the auth
 * session-resolution path free of tenant filtering. A company admin only
 * ever sees and edits users within their own company.
 */
class UserController extends Controller
{
    protected function isSuperAdmin(): bool
    {
        return Auth::user()->hasRole('super-admin');
    }

    /**
     * The company_id the current request operates within.
     * Super-admin no tiene empresa propia; gestiona a través del form.
     */
    protected function companyId(): ?int
    {
        return Auth::user()->company_id;
    }

    /**
     * Ensure the target user belongs to the current company.
     * Super-admin puede editar cualquier usuario excepto otros super-admins.
     */
    protected function authorizeSameCompany(User $user): void
    {
        if ($this->isSuperAdmin()) {
            abort_if($user->hasRole('super-admin') && $user->id !== Auth::id(), 403);
            return;
        }

        abort_unless($user->company_id === $this->companyId() && $this->companyId() !== null, 403);
        abort_if($user->hasRole('super-admin'), 403);
    }

    #[Middleware('permission:users.view')]
    public function index()
    {
        $query = User::with('roles', 'company')->latest();

        if (! $this->isSuperAdmin()) {
            $query->where('company_id', $this->companyId());
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    #[Middleware('permission:users.create')]
    public function create()
    {
        $roles     = StoreUserRequest::assignableRoles();
        $companies = $this->isSuperAdmin() ? Company::orderBy('name')->get(['id', 'name']) : collect();

        return view('admin.users.create', compact('roles', 'companies'));
    }

    #[Middleware('permission:users.create')]
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $companyId = $this->isSuperAdmin()
            ? ($data['company_id'] ?? null)
            : $this->companyId();

        $user = User::create([
            'company_id'        => $companyId,
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    #[Middleware('permission:users.edit')]
    public function edit(User $user)
    {
        $this->authorizeSameCompany($user);

        $roles      = StoreUserRequest::assignableRoles();
        $currentRole = $user->roles->pluck('name')->first();
        $companies  = $this->isSuperAdmin() ? Company::orderBy('name')->get(['id', 'name']) : collect();

        return view('admin.users.edit', compact('user', 'roles', 'currentRole', 'companies'));
    }

    #[Middleware('permission:users.edit')]
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorizeSameCompany($user);

        $data = $request->validated();

        $user->name  = $data['name'];
        $user->email = $data['email'];

        if ($this->isSuperAdmin() && array_key_exists('company_id', $data)) {
            $user->company_id = $data['company_id'] ?: null;
        }

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    #[Middleware('permission:users.delete')]
    public function destroy(User $user)
    {
        $this->authorizeSameCompany($user);

        abort_if($user->id === Auth::id(), 403, 'No podés eliminar tu propio usuario.');

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
