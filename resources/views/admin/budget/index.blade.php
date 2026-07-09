<x-admin-layout>
    <x-slot name="title">Presupuesto — {{ $event->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-secondary btn-sm">← Volver al evento</a>
        <a href="{{ route('admin.events.budget.create', $event) }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva partida
        </a>
    </x-slot>

    @php $cur = $event->currency ?? 'MXN'; @endphp

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Estimado</p>
            <p class="text-xl font-bold text-surface-800 mt-1">{{ number_format($event->budget_estimated_total, 2) }} <span class="text-sm text-surface-400">{{ $cur }}</span></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Gasto real</p>
            <p class="text-xl font-bold text-surface-800 mt-1">{{ number_format($event->budget_actual_total, 2) }} <span class="text-sm text-surface-400">{{ $cur }}</span></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Pagado</p>
            <p class="text-xl font-bold text-success-600 mt-1">{{ number_format($event->budget_paid_total, 2) }} <span class="text-sm text-surface-400">{{ $cur }}</span></p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-surface-400 font-medium uppercase tracking-wide">Pendiente de pago</p>
            <p class="text-xl font-bold text-warning-600 mt-1">{{ number_format($event->budget_pending_total, 2) }} <span class="text-sm text-surface-400">{{ $cur }}</span></p>
        </div>
    </div>

    {{-- Variance banner --}}
    @php $variance = $event->budget_variance; @endphp
    @if($items->isNotEmpty())
        <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl border
                    {{ $variance >= 0 ? 'bg-success-50 text-success-600 border-success-400/20' : 'bg-danger-50 text-danger-600 border-danger-400/20' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="{{ $variance >= 0 ? 'M5 13l4 4L19 7' : 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
            </svg>
            <p class="text-sm font-medium">
                @if($variance >= 0)
                    Dentro del presupuesto: {{ number_format($variance, 2) }} {{ $cur }} por debajo de lo estimado.
                @else
                    Excedido: {{ number_format(abs($variance), 2) }} {{ $cur }} por encima de lo estimado.
                @endif
            </p>
        </div>
    @endif

    {{-- Items table --}}
    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
        @if($items->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                </div>
                <p class="text-surface-700 font-medium mb-1">Sin partidas de presupuesto</p>
                <p class="text-surface-400 text-sm mb-5">Empezá agregando los gastos estimados del evento.</p>
                <a href="{{ route('admin.events.budget.create', $event) }}" class="btn btn-primary btn-sm">Agregar partida</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Categoría / Concepto</th>
                            <th>Proveedor</th>
                            <th class="text-right">Estimado</th>
                            <th class="text-right">Real</th>
                            <th>Pago</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>
                                    <p class="font-semibold text-surface-800 text-sm">{{ $item->concept }}</p>
                                    <p class="text-xs text-surface-400">{{ $item->category }}</p>
                                </td>
                                <td class="text-sm text-surface-600">{{ $item->vendor ?? '—' }}</td>
                                <td class="text-sm text-surface-700 text-right whitespace-nowrap">{{ number_format($item->estimated_amount, 2) }}</td>
                                <td class="text-sm text-right whitespace-nowrap {{ $item->actual_amount !== null && $item->actual_amount > $item->estimated_amount ? 'text-danger-600 font-semibold' : 'text-surface-700' }}">
                                    {{ $item->actual_amount !== null ? number_format($item->actual_amount, 2) : '—' }}
                                </td>
                                <td>
                                    @if($item->paid)
                                        <span class="badge badge-success">Pagado</span>
                                        @if($item->paid_at)<span class="text-xs text-surface-400 block mt-0.5">{{ $item->paid_at->format('d/m/Y') }}</span>@endif
                                    @else
                                        <span class="badge badge-warning">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.budget.edit', $item) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.budget.destroy', $item) }}"
                                              x-data @submit.prevent="if(confirm('¿Eliminar esta partida?')) $el.submit()">
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
                    <tfoot>
                        <tr class="border-t-2 border-surface-100 font-semibold">
                            <td colspan="2" class="text-sm text-surface-800">Totales</td>
                            <td class="text-sm text-surface-800 text-right">{{ number_format($event->budget_estimated_total, 2) }}</td>
                            <td class="text-sm text-surface-800 text-right">{{ number_format($event->budget_actual_total, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Breakdown by category --}}
            <div class="px-6 py-5 border-t border-surface-100">
                <p class="text-xs font-bold uppercase tracking-wide text-surface-400 mb-3">Por categoría</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($byCategory as $category => $data)
                        <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-surface-50 border border-surface-100">
                            <div>
                                <p class="text-sm font-semibold text-surface-800">{{ $category }}</p>
                                <p class="text-xs text-surface-400">{{ $data['count'] }} partida(s)</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-surface-800">{{ number_format($data['actual'], 2) }}</p>
                                <p class="text-xs text-surface-400">est. {{ number_format($data['estimated'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-admin-layout>
