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

    <div
        class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-visible"
        x-data="{
            waOpen: false,
            waGuest: '',
            waMessage: '',
            waCopied: false,
            openWa(guest, message) {
                this.waGuest = guest;
                this.waMessage = message;
                this.waCopied = false;
                this.waOpen = true;
            },
            async copyMessage() {
                try {
                    await navigator.clipboard.writeText(this.waMessage);
                    this.waCopied = true;
                    setTimeout(() => { this.waCopied = false; }, 2500);
                } catch(e) {}
            }
        }"
        @keydown.escape.window="waOpen = false"
    >

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
                            @php
                                $waMsg = "Hola {$inv->guest_name} 👋\n\nTe compartimos tu invitación personalizada para *{$event->name}*.\n\n📅 {$event->formatted_date}\n🕐 {$event->formatted_time}" . ($event->location ? "\n📍 {$event->location}" : "") . "\n\nIngresá al siguiente enlace para ver todos los detalles y confirmar tu asistencia:\n\n👉 {$inv->invitation_url}\n\n¡Te esperamos!";
                            @endphp
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

                                        {{-- WhatsApp --}}
                                        <button
                                            type="button"
                                            @click="openWa({{ json_encode($inv->guest_name) }}, {{ json_encode($waMsg) }})"
                                            class="btn btn-ghost btn-icon btn-sm text-[#25D366] hover:bg-[#25D366]/10"
                                            title="Generar mensaje WhatsApp"
                                        >
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </button>

                                        {{-- Ver invitación pública --}}
                                        <a href="{{ $inv->invitation_url }}" target="_blank"
                                           class="btn btn-ghost btn-icon btn-sm" title="Ver invitación pública">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('admin.invitations.edit', $inv) }}"
                                           class="btn btn-ghost btn-icon btn-sm" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Eliminar --}}
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

        {{-- ── Modal WhatsApp ── --}}
        <div
            x-show="waOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);"
            @click.self="waOpen = false"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                {{-- Header --}}
                <div class="flex items-center gap-3 px-5 py-4 border-b border-surface-100">
                    <span class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0"
                          style="background: rgba(37,211,102,0.12); color: #25D366;">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-surface-900 text-sm">Mensaje para WhatsApp</p>
                        <p class="text-xs text-surface-400 truncate" x-text="waGuest"></p>
                    </div>
                    <button type="button" @click="waOpen = false"
                            class="text-surface-400 hover:text-surface-600 transition-colors p-1 rounded-lg hover:bg-surface-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Vista previa --}}
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-semibold uppercase tracking-wide mb-2">Vista previa</p>
                    <div class="bg-surface-50 rounded-xl p-4 text-sm text-surface-700 whitespace-pre-wrap leading-relaxed max-h-60 overflow-y-auto border border-surface-100">
                        <span x-text="waMessage"></span>
                    </div>
                    <p class="mt-2 text-xs text-surface-400">Copiá este texto y pegalo en WhatsApp.</p>
                </div>

                {{-- Acciones --}}
                <div class="px-5 pb-5 flex gap-3">
                    <button
                        type="button"
                        @click="copyMessage()"
                        class="btn btn-primary flex-1 gap-2"
                    >
                        <template x-if="!waCopied">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </template>
                        <template x-if="waCopied">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span x-text="waCopied ? '¡Copiado!' : 'Copiar mensaje'"></span>
                    </button>
                    <button type="button" @click="waOpen = false" class="btn btn-secondary">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-admin-layout>
