<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Manage companies (tenants). Restricted to the super-admin: the whole
 * controller is guarded by the role:super-admin middleware at the route level.
 */
class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['users', 'events'])
            ->latest()
            ->paginate(15);

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $data = $request->validated();

        $company = DB::transaction(function () use ($request, $data) {
            $company = Company::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'active' => $request->boolean('active', true),
                'logo' => $request->hasFile('logo')
                    ? $request->file('logo')->store('companies', 'public')
                    : null,
            ]);

            $admin = User::create([
                'company_id' => $company->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'email_verified_at' => now(),
            ]);

            $admin->assignRole('admin');

            return $company;
        });

        return redirect()->route('admin.companies.index')
            ->with('success', "Empresa «{$company->name}» creada con su administrador.");
    }

    public function edit(Company $company)
    {
        $company->loadCount(['users', 'events']);

        return view('admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('companies', 'public');
        }

        $company->update($data);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Company $company)
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Empresa eliminada correctamente.');
    }
}
