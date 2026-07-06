<x-admin-layout>
    <x-slot name="title">Nueva plantilla</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100">
                <h2 class="font-semibold text-surface-900">Configuración de plantilla</h2>
                <p class="text-sm text-surface-400 mt-0.5">Define colores, tipografías y secciones.</p>
            </div>

            <form method="POST" action="{{ route('admin.templates.store') }}" class="px-6 py-6 space-y-6">
                @csrf

                {{-- Basic info --}}
                <div class="space-y-4">
                    <div>
                        <label class="form-label" for="name">Nombre <span class="text-danger-500">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               class="form-input @error('name') border-danger-500 @enderror"
                               placeholder="Elegante Dorado">
                        @error('name') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label" for="description">Descripción</label>
                        <textarea id="description" name="description" rows="2"
                                  class="form-input">{{ old('description') }}</textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <input id="active" type="checkbox" name="active" value="1"
                               class="w-4 h-4 rounded border-surface-300 text-brand-600 focus:ring-brand-500"
                               {{ old('active', '1') ? 'checked' : '' }}>
                        <label for="active" class="text-sm font-medium text-surface-700 cursor-pointer">Plantilla activa</label>
                    </div>
                </div>

                <hr class="border-surface-100">

                {{-- Colors --}}
                <div>
                    <h3 class="text-sm font-semibold text-surface-800 mb-4">Colores</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach([
                            'primary'    => ['Principal',   $defaultConfig['colors']['primary']],
                            'secondary'  => ['Secundario',  $defaultConfig['colors']['secondary']],
                            'accent'     => ['Acento',      $defaultConfig['colors']['accent']],
                            'background' => ['Fondo',       $defaultConfig['colors']['background']],
                            'text'       => ['Texto',       $defaultConfig['colors']['text']],
                        ] as $key => [$label, $default])
                            <div>
                                <label class="form-label text-xs" for="color_{{ $key }}">{{ $label }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="color_{{ $key }}" name="configuration[colors][{{ $key }}]"
                                           value="{{ old("configuration.colors.$key", $default) }}"
                                           class="w-10 h-9 rounded-lg border border-surface-200 cursor-pointer p-0.5">
                                    <input type="text" value="{{ old("configuration.colors.$key", $default) }}"
                                           class="form-input flex-1 font-mono text-xs"
                                           x-data
                                           x-ref="text_{{ $key }}"
                                           @input="document.getElementById('color_{{ $key }}').value = $event.target.value"
                                           oninput="this.previousElementSibling.value = this.value"
                                           name="configuration[colors][{{ $key }}]">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-surface-100">

                {{-- Fonts --}}
                <div>
                    <h3 class="text-sm font-semibold text-surface-800 mb-4">Tipografías</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="font_heading">Títulos</label>
                            <input id="font_heading" type="text" name="configuration[fonts][heading]"
                                   value="{{ old('configuration.fonts.heading', $defaultConfig['fonts']['heading']) }}"
                                   class="form-input" placeholder="Playfair Display">
                        </div>
                        <div>
                            <label class="form-label" for="font_body">Cuerpo</label>
                            <input id="font_body" type="text" name="configuration[fonts][body]"
                                   value="{{ old('configuration.fonts.body', $defaultConfig['fonts']['body']) }}"
                                   class="form-input" placeholder="Inter">
                        </div>
                    </div>
                </div>

                <hr class="border-surface-100">

                {{-- Sections --}}
                <div>
                    <h3 class="text-sm font-semibold text-surface-800 mb-4">Secciones</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach(['hero', 'message', 'details', 'countdown', 'rsvp', 'location', 'song', 'dietary', 'footer'] as $section)
                            @php $checked = in_array($section, $defaultConfig['sections']); @endphp
                            <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-surface-200 hover:border-brand-300 transition-colors has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                                <input type="checkbox" name="configuration[sections][]" value="{{ $section }}"
                                       class="w-4 h-4 rounded border-surface-300 text-brand-600 focus:ring-brand-500"
                                       {{ $checked ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-surface-700 capitalize">{{ $section }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-100">
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear plantilla</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
