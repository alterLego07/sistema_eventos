<x-admin-layout>
    <x-slot name="title">Eventos</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo evento
        </a>
    </x-slot>

    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">

        @if($events->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-surface-700 font-medium mb-1">No hay eventos</p>
                <p class="text-surface-400 text-sm mb-5">Empezá creando tu primer evento.</p>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">Crear evento</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Fecha</th>
                            <th>Plantilla</th>
                            <th>Estado</th>
                            <th>Invitaciones</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td>
                                    <p class="font-semibold text-surface-800 text-sm">{{ $event->name }}</p>
                                    <p class="text-xs text-surface-400">{{ $event->location ?? '—' }}</p>
                                </td>
                                <td class="text-sm text-surface-600 whitespace-nowrap">
                                    {{ $event->event_date->format('d/m/Y') }}
                                    <span class="text-surface-400 text-xs block">{{ $event->formatted_time }}</span>
                                </td>
                                <td class="text-sm text-surface-500">{{ $event->template?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ match($event->status) {
                                        'published' => 'badge-success',
                                        'draft'     => 'badge-info',
                                        'cancelled' => 'badge-danger',
                                        default     => 'badge-warning',
                                    } }}">
                                        {{ match($event->status) {
                                            'published' => 'Publicado',
                                            'draft'     => 'Borrador',
                                            'cancelled' => 'Cancelado',
                                            'completed' => 'Finalizado',
                                            default     => $event->status,
                                        } }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.events.invitations.index', $event) }}"
                                       class="inline-flex items-center gap-1.5 text-sm text-surface-700 hover:text-brand-600 transition-colors">
                                        <span class="font-semibold">{{ $event->confirmed_count }}</span>
                                        <span class="text-surface-400">/</span>
                                        <span>{{ $event->invitations_count }}</span>
                                    </a>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.events.stats', $event) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Estadísticas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.events.budget.index', $event) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Presupuesto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.events.invitations.index', $event) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Ver invitaciones">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                              x-data
                                              @submit.prevent="if(confirm('¿Eliminar este evento y todas sus invitaciones?')) $el.submit()">
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

            @if($events->hasPages())
                <div class="px-6 py-4 border-t border-surface-100">
                    {{ $events->links() }}
                </div>
            @endif
        @endif
    </div>

</x-admin-layout>
