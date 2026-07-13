<x-admin-layout>
    <x-slot name="title">Nuevo usuario</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100">
                <h2 class="font-semibold text-surface-900">Datos del usuario</h2>
                <p class="text-sm text-surface-400 mt-0.5">El usuario pertenecerá a tu empresa.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="px-6 py-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @if(auth()->user()->hasRole('super-admin') && $companies->isNotEmpty())
                        <div class="sm:col-span-2">
                            <label class="form-label" for="company_id">Empresa</label>
                            <select id="company_id" name="company_id" class="form-input @error('company_id') border-danger-500 @enderror">
                                <option value="">— Sin empresa (super-admin) —</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">Nombre <span class="text-danger-500">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               class="form-input @error('name') border-danger-500 @enderror"
                               placeholder="Juan Pérez">
                        @error('name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="email">Correo <span class="text-danger-500">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-input @error('email') border-danger-500 @enderror"
                               placeholder="juan@empresa.com">
                        @error('email') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="role">Rol <span class="text-danger-500">*</span></label>
                        <select id="role" name="role" class="form-input @error('role') border-danger-500 @enderror">
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-surface-400">Admin: gestión completa de la empresa. Organizador: solo eventos e invitaciones.</p>
                        @error('role') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="password">Contraseña <span class="text-danger-500">*</span></label>
                        <input id="password" type="password" name="password"
                               class="form-input @error('password') border-danger-500 @enderror">
                        @error('password') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="password_confirmation">Confirmar contraseña <span class="text-danger-500">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-input">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-100">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
