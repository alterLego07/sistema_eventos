<x-admin-layout>
    <x-slot name="title">Nueva empresa</x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.companies.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Datos de la empresa --}}
            <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-surface-100">
                    <h2 class="font-semibold text-surface-900">Datos de la empresa</h2>
                    <p class="text-sm text-surface-400 mt-0.5">Información general del tenant.</p>
                </div>

                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="form-label" for="name">Nombre de la empresa <span class="text-danger-500">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                   class="form-input @error('name') border-danger-500 @enderror"
                                   placeholder="Eventos Premium S.A.">
                            @error('name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="email">Email de contacto</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-input @error('email') border-danger-500 @enderror"
                                   placeholder="contacto@empresa.com">
                            @error('email') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="phone">Teléfono</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-input @error('phone') border-danger-500 @enderror"
                                   placeholder="+52 55 1234 5678">
                            @error('phone') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label" for="logo">Logo</label>
                            <input id="logo" type="file" name="logo" accept="image/*"
                                   class="block w-full text-sm text-surface-600 file:mr-4 file:py-2 file:px-4
                                          file:rounded-lg file:border-0 file:text-sm file:font-medium
                                          file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                            <p class="mt-1 text-xs text-surface-400">JPG, PNG o WebP. Máx. 2 MB.</p>
                            @error('logo') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                                       class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-sm text-surface-700">Empresa activa</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Administrador inicial --}}
            <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-surface-100">
                    <h2 class="font-semibold text-surface-900">Administrador de la empresa</h2>
                    <p class="text-sm text-surface-400 mt-0.5">Este usuario podrá gestionar los eventos y usuarios de la empresa.</p>
                </div>

                <div class="px-6 py-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="form-label" for="admin_name">Nombre <span class="text-danger-500">*</span></label>
                            <input id="admin_name" type="text" name="admin_name" value="{{ old('admin_name') }}"
                                   class="form-input @error('admin_name') border-danger-500 @enderror"
                                   placeholder="Ana Pérez">
                            @error('admin_name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label" for="admin_email">Correo <span class="text-danger-500">*</span></label>
                            <input id="admin_email" type="email" name="admin_email" value="{{ old('admin_email') }}"
                                   class="form-input @error('admin_email') border-danger-500 @enderror"
                                   placeholder="ana@empresa.com">
                            @error('admin_email') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="admin_password">Contraseña <span class="text-danger-500">*</span></label>
                            <input id="admin_password" type="password" name="admin_password"
                                   class="form-input @error('admin_password') border-danger-500 @enderror">
                            @error('admin_password') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="admin_password_confirmation">Confirmar contraseña <span class="text-danger-500">*</span></label>
                            <input id="admin_password_confirmation" type="password" name="admin_password_confirmation"
                                   class="form-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Crear empresa</button>
            </div>
        </form>
    </div>

</x-admin-layout>
