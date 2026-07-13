<x-admin-layout>
    <x-slot name="title">Editar evento</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.events.invitations.index', $event) }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
            Ver invitaciones ({{ $event->invitations_count ?? $event->invitations()->count() }})
        </a>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100">
                <h2 class="font-semibold text-surface-900">{{ $event->name }}</h2>
                <p class="text-sm text-surface-400 mt-0.5">Modificá los datos del evento.</p>
            </div>

            <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">Nombre del evento <span class="text-danger-500">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $event->name) }}"
                               class="form-input @error('name') border-danger-500 @enderror">
                        @error('name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="event_date">Fecha <span class="text-danger-500">*</span></label>
                        <input id="event_date" type="date" name="event_date"
                               value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}"
                               class="form-input @error('event_date') border-danger-500 @enderror">
                        @error('event_date') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="event_time">Hora <span class="text-danger-500">*</span></label>
                        <input id="event_time" type="time" name="event_time"
                               value="{{ old('event_time', substr($event->event_time, 0, 5)) }}"
                               class="form-input @error('event_time') border-danger-500 @enderror">
                        @error('event_time') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="description">Descripción</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-input @error('description') border-danger-500 @enderror">{{ old('description', $event->description) }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="location">Lugar</label>
                        <input id="location" type="text" name="location" value="{{ old('location', $event->location) }}"
                               class="form-input @error('location') border-danger-500 @enderror">
                        @error('location') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="location_url">URL de Google Maps</label>
                        <input id="location_url" type="url" name="location_url" value="{{ old('location_url', $event->location_url) }}"
                               class="form-input @error('location_url') border-danger-500 @enderror">
                        @error('location_url') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="template_id">Plantilla</label>
                        <select id="template_id" name="template_id" class="form-input">
                            <option value="">Sin plantilla</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}"
                                    {{ old('template_id', $event->template_id) == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="status">Estado</label>
                        <select id="status" name="status" class="form-input">
                            @foreach(['draft' => 'Borrador', 'published' => 'Publicado', 'cancelled' => 'Cancelado', 'completed' => 'Finalizado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $event->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="currency">Moneda (presupuesto)</label>
                        <select id="currency" name="currency" class="form-input">
                            @php
                                $currencies = [
                                    'PYG' => 'PYG — Guaraní (Gs.)',
                                    'MXN' => 'MXN — Peso Mexicano',
                                    'USD' => 'USD — Dólar',
                                    'EUR' => 'EUR — Euro',
                                    'ARS' => 'ARS — Peso Argentino',
                                    'COP' => 'COP — Peso Colombiano',
                                    'CLP' => 'CLP — Peso Chileno',
                                    'PEN' => 'PEN — Sol Peruano',
                                    'BRL' => 'BRL — Real Brasileño',
                                    'GBP' => 'GBP — Libra Esterlina',
                                ];
                            @endphp
                            @foreach($currencies as $code => $label)
                                <option value="{{ $code }}" {{ old('currency', $event->currency ?? 'PYG') === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Imagen de portada</label>
                        @if($event->cover_image)
                            <div class="mb-3">
                                <img src="{{ Storage::url($event->cover_image) }}" alt="Portada actual"
                                     class="h-24 w-40 object-cover rounded-xl border border-surface-200">
                                <p class="text-xs text-surface-400 mt-1">Imagen actual</p>
                            </div>
                        @endif
                        <input id="cover_image" type="file" name="cover_image" accept="image/*"
                               class="block w-full text-sm text-surface-600 file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0 file:text-sm file:font-medium
                                      file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                        <p class="mt-1 text-xs text-surface-400">Dejá en blanco para mantener la imagen actual.</p>
                        @error('cover_image') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-100">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
