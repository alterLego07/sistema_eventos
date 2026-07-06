<x-admin-layout>
    <x-slot name="title">Empresas</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva empresa
        </a>
    </x-slot>

    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
        @if($companies->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-6 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/>
                    </svg>
                </div>
                <p class="text-surface-700 font-medium mb-1">No hay empresas</p>
                <p class="text-surface-400 text-sm mb-5">Creá la primera empresa y su administrador.</p>
                <a href="{{ route('admin.companies.create') }}" class="btn btn-primary btn-sm">Crear empresa</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Contacto</th>
                            <th>Usuarios</th>
                            <th>Eventos</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if($company->logo)
                                            <img src="{{ Storage::url($company->logo) }}" alt="" class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-9 h-9 rounded-lg gradient-brand flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($company->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-surface-800 text-sm">{{ $company->name }}</p>
                                            <p class="text-xs text-surface-400">{{ $company->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm text-surface-600">
                                    {{ $company->email ?? '—' }}
                                    <span class="text-surface-400 text-xs block">{{ $company->phone ?? '' }}</span>
                                </td>
                                <td class="text-sm text-surface-700 font-semibold">{{ $company->users_count }}</td>
                                <td class="text-sm text-surface-700 font-semibold">{{ $company->events_count }}</td>
                                <td>
                                    <span class="badge {{ $company->active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $company->active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.companies.edit', $company) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.companies.destroy', $company) }}"
                                              x-data
                                              @submit.prevent="if(confirm('¿Eliminar esta empresa? Se eliminarán también sus eventos e invitaciones.')) $el.submit()">
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

            @if($companies->hasPages())
                <div class="px-6 py-4 border-t border-surface-100">
                    {{ $companies->links() }}
                </div>
            @endif
        @endif
    </div>

</x-admin-layout>
