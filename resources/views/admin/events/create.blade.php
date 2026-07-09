<x-admin-layout>
    <x-slot name="title">Nuevo evento</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100">
                <h2 class="font-semibold text-surface-900">Información del evento</h2>
                <p class="text-sm text-surface-400 mt-0.5">Completá los datos para crear el evento.</p>
            </div>

            <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">Nombre del evento <span class="text-danger-500">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               class="form-input @error('name') border-danger-500 @enderror"
                               placeholder="Boda de María y Juan">
                        @error('name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="event_date">Fecha <span class="text-danger-500">*</span></label>
                        <input id="event_date" type="date" name="event_date" value="{{ old('event_date') }}"
                               class="form-input @error('event_date') border-danger-500 @enderror"
                               min="{{ date('Y-m-d') }}">
                        @error('event_date') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="event_time">Hora <span class="text-danger-500">*</span></label>
                        <input id="event_time" type="time" name="event_time" value="{{ old('event_time') }}"
                               class="form-input @error('event_time') border-danger-500 @enderror">
                        @error('event_time') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="description">Descripción</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-input @error('description') border-danger-500 @enderror"
                                  placeholder="Una breve descripción del evento...">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="location">Lugar</label>
                        <input id="location" type="text" name="location" value="{{ old('location') }}"
                               class="form-input @error('location') border-danger-500 @enderror"
                               placeholder="Salón de Fiestas La Paloma, Buenos Aires">
                        @error('location') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="location_url">URL de Google Maps</label>
                        <input id="location_url" type="url" name="location_url" value="{{ old('location_url') }}"
                               class="form-input @error('location_url') border-danger-500 @enderror"
                               placeholder="https://maps.google.com/...">
                        @error('location_url') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="template_id">Plantilla</label>
                        <select id="template_id" name="template_id"
                                class="form-input @error('template_id') border-danger-500 @enderror">
                            <option value="">Sin plantilla</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('template_id') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="status">Estado</label>
                        <select id="status" name="status" class="form-input">
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="currency">Moneda (presupuesto)</label>
                        <select id="currency" name="currency" class="form-input">
                            @foreach(['MXN','USD','EUR','COP','ARS','CLP','PEN','BRL','GBP'] as $cur)
                                <option value="{{ $cur }}" {{ old('currency', 'MXN') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="cover_image">Imagen de portada</label>
                        <input id="cover_image" type="file" name="cover_image" accept="image/*"
                               class="block w-full text-sm text-surface-600 file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0 file:text-sm file:font-medium
                                      file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                        <p class="mt-1 text-xs text-surface-400">JPG, PNG o WebP. Máx. 2 MB.</p>
                        @error('cover_image') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-100">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear evento</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
