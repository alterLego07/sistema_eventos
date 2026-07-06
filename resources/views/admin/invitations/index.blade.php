<x-admin-layout>
    <x-slot name="title">Invitaciones — {{ $event->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-secondary btn-sm">
            ← Volver al evento
        </a>
        <a href="{{ route('admin.events.invitations.create', $event) }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva invitación
        </a>
    </x-slot>

    {{-- Event summary bar --}}
    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm p-5 mb-5 flex flex-wrap gap-6">
        <div>
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Fecha</p>
            <p class="text-sm font-semibold text-surface-800 mt-0.5">{{ $event->formatted_date }}</p>
        </div>
        <div>
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Total</p>
            <p class="text-sm font-semibold text-surface-800 mt-0.5">{{ $invitations->total() }} invitaciones</p>
        </div>
        <div>
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Confirmadas</p>
            <p class="text-sm font-semibold text-success-600 mt-0.5">{{ $event->confirmed_count }}</p>
        </div>
        <div>
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Pendientes</p>
            <p class="text-sm font-semibold text-warning-600 mt-0.5">{{ $event->pending_count }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">

        @if($invitations->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-accent-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-surface-700 font-medium mb-1">Sin invitaciones</p>
                <p class="text-surface-400 text-sm mb-5">Empezá agregando invitados al evento.</p>
                <a href="{{ route('admin.events.invitations.create', $event) }}" class="btn btn-primary btn-sm">Agregar invitado</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Invitado</th>
                            <th>Contacto</th>
                            <th>Mesa</th>
                            <th>Permitidos</th>
                            <th>Estado</th>
                            <th>Confirmados</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitations as $inv)
                            <tr>
                                <td>
                                    <p class="font-semibold text-surface-800 text-sm">{{ $inv->guest_name }}</p>
                                    <p class="text-[0.7rem] text-surface-400 font-mono">{{ $inv->token }}</p>
                                </td>
                                <td>
                                    <p class="text-xs text-surface-600">{{ $inv->email ?? '—' }}</p>
                                    <p class="text-xs text-surface-400">{{ $inv->phone ?? '' }}</p>
                                </td>
                                <td class="text-sm text-surface-600">{{ $inv->table_number ?? '—' }}</td>
                                <td class="text-sm text-surface-600">{{ $inv->allowed_guests }}</td>
                                <td>
                                    <span class="badge {{ $inv->confirmed ? 'badge-success' : 'badge-warning' }}">
                                        {{ $inv->status_label }}
                                    </span>
                                </td>
                                <td class="text-sm text-surface-600">
                                    {{ $inv->confirmed ? $inv->confirmed_guests : '—' }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ $inv->invitation_url }}" target="_blank"
                                           class="btn btn-ghost btn-icon btn-sm" title="Ver invitación pública">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.invitations.edit', $inv) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.invitations.destroy', $inv) }}"
                                              x-data @submit.prevent="if(confirm('¿Eliminar esta invitación?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-icon btn-sm text-danger-500 hover:bg-danger-50" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($invitations->hasPages())
                <div class="px-6 py-4 border-t border-surface-100">
                    {{ $invitations->links() }}
                </div>
            @endif
        @endif
    </div>

</x-admin-layout>
