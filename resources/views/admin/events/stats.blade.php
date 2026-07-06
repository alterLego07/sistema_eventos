<x-admin-layout>
    <x-slot name="title">Estadísticas · {{ $event->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.events.invitations.index', $event) }}" class="btn btn-ghost btn-sm">
            Ver invitaciones
        </a>
        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary btn-sm">Editar evento</a>
    </x-slot>

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-surface-400 mb-6">
        <a href="{{ route('admin.events.index') }}" class="hover:text-surface-700 transition-colors">Eventos</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-surface-800 font-semibold truncate">{{ $event->name }}</span>
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-surface-600 shrink-0">Estadísticas</span>
    </nav>

    {{-- Info del evento --}}
    <div class="flex flex-wrap items-center gap-3 mb-6 px-5 py-4 bg-white rounded-2xl border border-surface-100 shadow-sm">
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-surface-800">{{ $event->name }}</p>
            <p class="text-sm text-surface-400 mt-0.5">
                {{ $event->formatted_date }} · {{ $event->formatted_time }}
                @if($event->location) · {{ $event->location }} @endif
            </p>
        </div>
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
    </div>

    {{-- Estado vacío --}}
    @if($totals->total === 0)
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm px-6 py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-surface-700 font-medium mb-1">Sin invitaciones todavía</p>
            <p class="text-surface-400 text-sm mb-5">Las estadísticas aparecerán cuando cargues las primeras invitaciones.</p>
            <a href="{{ route('admin.events.invitations.create', $event) }}" class="btn btn-primary btn-sm">
                Agregar invitación
            </a>
        </div>
    @else

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

            <div class="stat-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Total</p>
                    <div class="w-9 h-9 rounded-xl bg-brand-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-surface-900">{{ $totals->total }}</p>
                <p class="text-xs text-surface-400 mt-1">invitaciones enviadas</p>
            </div>

            <div class="stat-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Confirmadas</p>
                    <div class="w-9 h-9 rounded-xl bg-success-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-surface-900">{{ $totals->confirmed }}</p>
                <p class="text-xs text-surface-400 mt-1">{{ $totals->total_expected }} asistentes esperados</p>
            </div>

            <div class="stat-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Pendientes</p>
                    <div class="w-9 h-9 rounded-xl bg-warning-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-surface-900">{{ $totals->pending }}</p>
                <p class="text-xs text-surface-400 mt-1">sin responder</p>
            </div>

            <div class="stat-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Confirmación</p>
                    <div class="w-9 h-9 rounded-xl bg-brand-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-surface-900">{{ $confirmationRate }}%</p>
                <p class="text-xs text-surface-400 mt-1">tasa de respuesta</p>
            </div>

        </div>

        {{-- Barras de progreso --}}
        <div class="grid lg:grid-cols-2 gap-4 mb-6">

            <div class="bg-white rounded-2xl border border-surface-100 shadow-sm p-5">
                <p class="text-sm font-semibold text-surface-700 mb-4">Tasa de respuesta</p>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-1 progress-bar">
                        <div class="progress-bar-fill" style="width: {{ $confirmationRate }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-surface-700 w-12 text-right shrink-0">{{ $confirmationRate }}%</span>
                </div>
                <div class="flex gap-4 text-xs text-surface-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-success-500 inline-block shrink-0"></span>
                        {{ $totals->confirmed }} confirmadas
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-surface-200 inline-block shrink-0"></span>
                        {{ $totals->pending }} pendientes
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-surface-100 shadow-sm p-5">
                <p class="text-sm font-semibold text-surface-700 mb-4">Capacidad de invitados</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-500">Cupos habilitados</span>
                        <span class="text-sm font-bold text-surface-800">{{ $totals->total_allowed }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-500">Asistentes confirmados</span>
                        <span class="text-sm font-bold text-surface-800">{{ $totals->total_expected }}</span>
                    </div>
                    @if($totals->total_allowed > 0)
                        @php $occupancy = min(round(($totals->total_expected / $totals->total_allowed) * 100), 100); @endphp
                        <div class="pt-1 border-t border-surface-100">
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ $occupancy }}%"></div>
                            </div>
                            <p class="text-xs text-surface-400 mt-1.5">{{ $occupancy }}% de ocupación esperada</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Confirmaciones recientes --}}
        @if($recentConfirmations->isNotEmpty())
            <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-surface-100">
                    <p class="text-sm font-semibold text-surface-800">Confirmaciones recientes</p>
                    <p class="text-xs text-surface-400 mt-0.5">Últimas {{ $recentConfirmations->count() }} respuestas</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Invitado</th>
                                <th>Fecha</th>
                                <th>Asistentes</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentConfirmations as $inv)
                                <tr>
                                    <td class="font-medium text-surface-800 text-sm">{{ $inv->guest_name }}</td>
                                    <td class="text-sm text-surface-500 whitespace-nowrap">
                                        {{ $inv->confirmed_at?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            {{ $inv->confirmed_guests }} {{ $inv->confirmed_guests === 1 ? 'persona' : 'personas' }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-surface-500 max-w-xs">
                                        @if($inv->dietary_restrictions)
                                            <span class="badge badge-warning">{{ $inv->dietary_restrictions }}</span>
                                        @endif
                                        @if($inv->message)
                                            <span class="italic text-surface-400 text-xs">{{ Str::limit($inv->message, 50) }}</span>
                                        @endif
                                        @if(!$inv->dietary_restrictions && !$inv->message)
                                            <span class="text-surface-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-2 gap-4 mb-6">

            {{-- Distribución por mesa --}}
            @if($tableDistribution->isNotEmpty())
                <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-surface-100">
                        <p class="text-sm font-semibold text-surface-800">Distribución por mesa</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Mesa</th>
                                    <th>Invitaciones</th>
                                    <th>Confirmadas</th>
                                    <th>Asistentes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tableDistribution as $row)
                                    <tr>
                                        <td class="font-semibold text-surface-800 text-sm">Mesa {{ $row->table_number }}</td>
                                        <td class="text-sm text-surface-600">{{ $row->total }}</td>
                                        <td>
                                            <span class="badge {{ $row->confirmed_count > 0 ? 'badge-success' : 'badge-info' }}">
                                                {{ $row->confirmed_count }}/{{ $row->total }}
                                            </span>
                                        </td>
                                        <td class="text-sm font-medium text-surface-800">{{ $row->expected_guests }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Restricciones dietarias --}}
            @if($dietaryRestrictions->isNotEmpty())
                <div class="bg-white rounded-2xl border border-surface-100 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <p class="text-sm font-semibold text-surface-800">Restricciones alimentarias</p>
                        <span class="badge badge-warning">{{ $dietaryRestrictions->count() }}</span>
                    </div>
                    <ul class="space-y-2">
                        @foreach($dietaryRestrictions as $inv)
                            <li class="flex items-start gap-2 text-sm">
                                <svg class="w-4 h-4 text-warning-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>
                                    <span class="font-medium text-surface-700">{{ $inv->guest_name }}:</span>
                                    <span class="text-surface-500"> {{ $inv->dietary_restrictions }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        {{-- Sugerencias de canciones --}}
        @if($songSuggestions->isNotEmpty())
            <div class="bg-white rounded-2xl border border-surface-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <p class="text-sm font-semibold text-surface-800">Sugerencias de canciones</p>
                    <span class="badge badge-info">{{ $songSuggestions->count() }}</span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($songSuggestions as $inv)
                        <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-surface-50 border border-surface-100">
                            <span class="text-brand-400 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-surface-800 truncate">{{ $inv->song_suggestion }}</p>
                                <p class="text-xs text-surface-400 truncate">{{ $inv->guest_name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif

</x-admin-layout>
