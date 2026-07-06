<x-admin-layout>
    <x-slot name="title">Usuarios</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo usuario
        </a>
    </x-slot>

    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
        @if($users->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-surface-700 font-medium mb-1">No hay usuarios</p>
                <p class="text-surface-400 text-sm mb-5">Agregá usuarios para tu empresa.</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Crear usuario</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full gradient-brand flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <p class="font-semibold text-surface-800 text-sm">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="text-xs text-surface-400 font-normal">(vos)</span>
                                            @endif
                                        </p>
                                    </div>
                                </td>
                                <td class="text-sm text-surface-600">{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge {{ $role->name === 'admin' ? 'badge-info' : 'badge-warning' }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  x-data
                                                  @submit.prevent="if(confirm('¿Eliminar este usuario?')) $el.submit()">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-icon btn-sm text-danger-500 hover:bg-danger-50" title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-surface-100">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>

</x-admin-layout>
