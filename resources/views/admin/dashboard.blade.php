<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Eventos</p>
                <div class="w-9 h-9 rounded-xl bg-brand-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-surface-900">{{ $totalEvents }}</p>
            <p class="text-xs text-surface-400 mt-1">{{ $publishedEvents }} publicados</p>
        </div>

        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Invitaciones</p>
                <div class="w-9 h-9 rounded-xl bg-accent-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-surface-900">{{ $totalInvitations }}</p>
            <p class="text-xs text-surface-400 mt-1">{{ $confirmedInvitations }} confirmadas</p>
        </div>

        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Confirmados</p>
                <div class="w-9 h-9 rounded-xl bg-success-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-surface-900">{{ $confirmedInvitations }}</p>
            <p class="text-xs text-surface-400 mt-1">{{ $pendingInvitations }} pendientes</p>
        </div>

        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Plantillas</p>
                <div class="w-9 h-9 rounded-xl bg-brand-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-surface-900">{{ $totalTemplates }}</p>
            <p class="text-xs text-surface-400 mt-1">activas</p>
        </div>
    </div>

    {{-- Recent events --}}
    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-surface-100">
            <h2 class="text-sm font-semibold text-surface-900">Eventos recientes</h2>
            <a href="{{ route('admin.events.index') }}" class="text-xs text-brand-600 font-medium hover:text-brand-700">
                Ver todos →
            </a>
        </div>

        @if($recentEvents->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-surface-400 text-sm">No hay eventos aún.</p>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm mt-4">
                    Crear primer evento
                </a>
            </div>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Invitaciones</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentEvents as $event)
                        <tr>
                            <td>
                                <p class="font-medium text-surface-800 text-sm">{{ $event->name }}</p>
                                <p class="text-xs text-surface-400">{{ $event->template?->name ?? 'Sin plantilla' }}</p>
                            </td>
                            <td class="text-sm text-surface-600">{{ $event->event_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $event->status === 'published' ? 'badge-success' : ($event->status === 'cancelled' ? 'badge-danger' : 'badge-info') }}">
                                    {{ match($event->status) { 'published' => 'Publicado', 'draft' => 'Borrador', 'cancelled' => 'Cancelado', default => $event->status } }}
                                </span>
                            </td>
                            <td>
                                <span class="text-sm text-surface-700">
                                    {{ $event->confirmed_invitations_count }}/{{ $event->invitations_count }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.events.edit', $event) }}"
                                   class="text-brand-600 hover:text-brand-700 text-xs font-medium">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</x-admin-layout>
