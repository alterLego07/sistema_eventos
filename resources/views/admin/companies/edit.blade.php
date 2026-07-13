<x-admin-layout>
    <x-slot name="title">Editar empresa</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-surface-900">{{ $company->name }}</h2>
                    <p class="text-sm text-surface-400 mt-0.5">{{ $company->users_count }} usuario(s) · {{ $company->events_count }} evento(s)</p>
                </div>
                @if($company->logo)
                    <img src="{{ Storage::url($company->logo) }}" alt="" class="w-12 h-12 rounded-xl object-cover">
                @endif
            </div>

            <form method="POST" action="{{ route('admin.companies.update', $company) }}" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">Nombre de la empresa <span class="text-danger-500">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $company->name) }}"
                               class="form-input @error('name') border-danger-500 @enderror">
                        @error('name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="email">Email de contacto</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $company->email) }}"
                               class="form-input @error('email') border-danger-500 @enderror">
                        @error('email') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="phone">Teléfono</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                               class="form-input @error('phone') border-danger-500 @enderror">
                        @error('phone') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="logo">Reemplazar logo</label>
                        <input id="logo" type="file" name="logo" accept="image/*"
                               class="block w-full text-sm text-surface-600 file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0 file:text-sm file:font-medium
                                      file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                        <p class="mt-1 text-xs text-surface-400">JPG, PNG o WebP. Máx. 2 MB.</p>
                        @error('logo') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="active" value="1" {{ old('active', $company->active) ? 'checked' : '' }}
                                   class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-surface-700">Empresa activa</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-100">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
