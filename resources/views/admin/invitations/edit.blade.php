<x-admin-layout>
    <x-slot name="title">Editar invitación</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.events.invitations.index', $invitation->event_id) }}" class="btn btn-secondary btn-sm">
            ← Volver
        </a>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100">
                <h2 class="font-semibold text-surface-900">{{ $invitation->guest_name }}</h2>
                <p class="text-sm text-surface-400 mt-0.5">
                    Evento: <span class="font-medium text-surface-600">{{ $invitation->event->name }}</span>
                    &nbsp;·&nbsp;
                    Token: <span class="font-mono text-xs text-surface-500">{{ $invitation->token }}</span>
                </p>
            </div>

            {{-- Confirmation status (read-only info) --}}
            @if($invitation->confirmed)
                <div class="mx-6 mt-5 px-4 py-3 rounded-xl bg-success-50 border border-success-400/20 flex items-center gap-3">
                    <svg class="w-4 h-4 text-success-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-success-700">
                        Confirmado el {{ $invitation->confirmed_at->format('d/m/Y H:i') }}
                        · {{ $invitation->confirmed_guests }} {{ $invitation->confirmed_guests === 1 ? 'persona' : 'personas' }}
                        @if($invitation->dietary_restrictions) · {{ $invitation->dietary_restrictions }} @endif
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.invitations.update', $invitation) }}" class="px-6 py-6 space-y-5">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="guest_name">Nombre del invitado <span class="text-danger-500">*</span></label>
                        <input id="guest_name" type="text" name="guest_name" value="{{ old('guest_name', $invitation->guest_name) }}"
                               class="form-input @error('guest_name') border-danger-500 @enderror">
                        @error('guest_name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $invitation->email) }}"
                               class="form-input @error('email') border-danger-500 @enderror">
                        @error('email') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="phone">Teléfono</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone', $invitation->phone) }}"
                               class="form-input @error('phone') border-danger-500 @enderror">
                        @error('phone') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="table_number">Número de mesa</label>
                        <input id="table_number" type="number" name="table_number"
                               value="{{ old('table_number', $invitation->table_number) }}"
                               class="form-input @error('table_number') border-danger-500 @enderror" min="1">
                        @error('table_number') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="allowed_guests">Acompañantes permitidos <span class="text-danger-500">*</span></label>
                        <input id="allowed_guests" type="number" name="allowed_guests"
                               value="{{ old('allowed_guests', $invitation->allowed_guests) }}"
                               class="form-input @error('allowed_guests') border-danger-500 @enderror" min="1" max="20">
                        @error('allowed_guests') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <input id="invited" type="checkbox" name="invited" value="1" {{ old('invited', $invitation->invited) ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-surface-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
                        <label for="invited" class="text-sm text-surface-600 cursor-pointer select-none">Invitación ya enviada</label>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-surface-100">
                    <a href="{{ $invitation->invitation_url }}" target="_blank"
                       class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                        Ver invitación pública →
                    </a>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.events.invitations.index', $invitation->event_id) }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </div>
            </form>

            @role('super-admin|admin')
                <div class="px-6 py-5 border-t border-surface-100">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wide mb-3">
                        Confirmar en nombre del invitado
                    </p>
                    <div class="flex flex-wrap gap-3" x-data="{ guests: {{ $invitation->confirmed_guests ?: $invitation->allowed_guests }} }">
                        <form method="POST" action="{{ route('admin.invitations.confirm', $invitation) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="confirmed" value="1">
                            <input type="number" name="confirmed_guests" x-model="guests" min="1" max="{{ $invitation->allowed_guests }}"
                                   class="form-input w-20 py-1.5 text-sm">
                            <button type="submit" class="btn btn-secondary btn-sm text-success-700">Marcar asistirá</button>
                        </form>
                        <form method="POST" action="{{ route('admin.invitations.confirm', $invitation) }}"
                              x-data @submit.prevent="if(confirm('¿Marcar que este invitado no asistirá?')) $el.submit()">
                            @csrf
                            <input type="hidden" name="confirmed" value="0">
                            <button type="submit" class="btn btn-secondary btn-sm text-danger-700">Marcar no asistirá</button>
                        </form>
                    </div>
                </div>
            @endrole
        </div>
    </div>

</x-admin-layout>
