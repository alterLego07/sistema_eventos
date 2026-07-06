<x-admin-layout>
    <x-slot name="title">Plantillas</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva plantilla
        </a>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($templates as $template)
            <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden flex flex-col">
                {{-- Color preview bar --}}
                <div class="h-2 w-full" style="background: linear-gradient(90deg,
                    {{ $template->configuration['colors']['primary'] ?? '#4c6ef5' }},
                    {{ $template->configuration['colors']['accent'] ?? '#e64980' }})">
                </div>

                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-surface-900">{{ $template->name }}</h3>
                            <p class="text-xs text-surface-400 mt-0.5">{{ $template->description }}</p>
                        </div>
                        <span class="badge {{ $template->active ? 'badge-success' : 'badge-danger' }} ml-2 flex-shrink-0">
                            {{ $template->active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>

                    {{-- Color swatches --}}
                    <div class="flex gap-1.5 mb-4">
                        @foreach(['primary', 'secondary', 'accent', 'background', 'text'] as $colorKey)
                            @if(isset($template->configuration['colors'][$colorKey]))
                                <div class="w-5 h-5 rounded-full border border-surface-200 ring-1 ring-white"
                                     style="background: {{ $template->configuration['colors'][$colorKey] }}"
                                     title="{{ $colorKey }}: {{ $template->configuration['colors'][$colorKey] }}">
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="text-xs text-surface-400 mb-4 space-y-1">
                        <p>Fuente: <span class="text-surface-600">{{ $template->configuration['fonts']['heading'] ?? '—' }}</span></p>
                        <p>Secciones: <span class="text-surface-600">{{ count($template->configuration['sections'] ?? []) }}</span></p>
                        <p>Eventos: <span class="text-surface-600">{{ $template->events_count }}</span></p>
                    </div>

                    <div class="flex items-center gap-2 mt-auto pt-4 border-t border-surface-100">
                        <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-secondary btn-sm flex-1 justify-center">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('admin.templates.destroy', $template) }}"
                              x-data @submit.prevent="if(confirm('¿Desactivar esta plantilla?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-icon btn-sm text-danger-500 hover:bg-danger-50" title="Desactivar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 xl:col-span-3 bg-white rounded-2xl border border-surface-100 shadow-sm px-6 py-16 text-center">
                <p class="text-surface-700 font-medium mb-1">No hay plantillas</p>
                <p class="text-surface-400 text-sm mb-5">Creá una plantilla para usarla en tus eventos.</p>
                <a href="{{ route('admin.templates.create') }}" class="btn btn-primary btn-sm">Crear plantilla</a>
            </div>
        @endforelse
    </div>

    @if($templates->hasPages())
        <div class="mt-6">{{ $templates->links() }}</div>
    @endif

</x-admin-layout>
