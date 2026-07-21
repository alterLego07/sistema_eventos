<x-admin-layout>
    <x-slot name="title">Nueva invitación — {{ $event->name }}</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100">
                <h2 class="font-semibold text-surface-900">Datos del invitado</h2>
                <p class="text-sm text-surface-400 mt-0.5">Evento: <span class="font-medium text-surface-600">{{ $event->name }}</span></p>
            </div>

            <form method="POST" action="{{ route('admin.events.invitations.store', $event) }}" class="px-6 py-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="guest_name">Nombre del invitado <span class="text-danger-500">*</span></label>
                        <input id="guest_name" type="text" name="guest_name" value="{{ old('guest_name') }}"
                               class="form-input @error('guest_name') border-danger-500 @enderror"
                               placeholder="María García" autofocus>
                        @error('guest_name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-input @error('email') border-danger-500 @enderror"
                               placeholder="maria@ejemplo.com">
                        @error('email') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="phone">Teléfono</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                               class="form-input @error('phone') border-danger-500 @enderror"
                               placeholder="+54 9 11 1234-5678">
                        @error('phone') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="table_number">Número de mesa</label>
                        <input id="table_number" type="number" name="table_number" value="{{ old('table_number') }}"
                               class="form-input @error('table_number') border-danger-500 @enderror"
                               min="1" placeholder="1">
                        @error('table_number') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="allowed_guests">Acompañantes permitidos <span class="text-danger-500">*</span></label>
                        <input id="allowed_guests" type="number" name="allowed_guests" value="{{ old('allowed_guests', 1) }}"
                               class="form-input @error('allowed_guests') border-danger-500 @enderror"
                               min="1" max="20">
                        @error('allowed_guests') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <input id="invited" type="checkbox" name="invited" value="1" {{ old('invited') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-surface-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                        <label for="invited" class="text-sm text-surface-600 cursor-pointer select-none">Invitación ya enviada</label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-100">
                    <a href="{{ route('admin.events.invitations.index', $event) }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear invitación</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
