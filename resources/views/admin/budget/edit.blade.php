<x-admin-layout>
    <x-slot name="title">Editar partida — {{ $budgetItem->event->name }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-surface-100">
                <h2 class="font-semibold text-surface-900">Editar partida</h2>
                <p class="text-sm text-surface-400 mt-0.5">Moneda del evento: {{ $budgetItem->event->currency ?? 'MXN' }}</p>
            </div>

            <form method="POST" action="{{ route('admin.budget.update', $budgetItem) }}" class="px-6 py-6 space-y-5"
                  x-data="{ paid: {{ old('paid', $budgetItem->paid) ? 'true' : 'false' }} }">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label" for="category">Categoría <span class="text-danger-500">*</span></label>
                        <input list="category-list" id="category" name="category" value="{{ old('category', $budgetItem->category) }}"
                               class="form-input @error('category') border-danger-500 @enderror">
                        <datalist id="category-list">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}"></option>
                            @endforeach
                        </datalist>
                        @error('category') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="concept">Concepto <span class="text-danger-500">*</span></label>
                        <input id="concept" type="text" name="concept" value="{{ old('concept', $budgetItem->concept) }}"
                               class="form-input @error('concept') border-danger-500 @enderror">
                        @error('concept') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="estimated_amount">Monto estimado <span class="text-danger-500">*</span></label>
                        <input id="estimated_amount" type="number" step="0.01" min="0" name="estimated_amount" value="{{ old('estimated_amount', $budgetItem->estimated_amount) }}"
                               class="form-input @error('estimated_amount') border-danger-500 @enderror">
                        @error('estimated_amount') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label" for="actual_amount">Monto real</label>
                        <input id="actual_amount" type="number" step="0.01" min="0" name="actual_amount" value="{{ old('actual_amount', $budgetItem->actual_amount) }}"
                               class="form-input @error('actual_amount') border-danger-500 @enderror"
                               placeholder="Opcional">
                        @error('actual_amount') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="vendor">Proveedor</label>
                        <input id="vendor" type="text" name="vendor" value="{{ old('vendor', $budgetItem->vendor) }}"
                               class="form-input @error('vendor') border-danger-500 @enderror">
                        @error('vendor') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="paid" value="1" x-model="paid" {{ old('paid', $budgetItem->paid) ? 'checked' : '' }}
                                   class="rounded border-surface-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-surface-700">Marcar como pagado</span>
                        </label>
                    </div>

                    <div x-show="paid" x-cloak>
                        <label class="form-label" for="paid_at">Fecha de pago</label>
                        <input id="paid_at" type="date" name="paid_at"
                               value="{{ old('paid_at', optional($budgetItem->paid_at)->format('Y-m-d')) }}"
                               class="form-input @error('paid_at') border-danger-500 @enderror">
                        @error('paid_at') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="notes">Notas</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="form-input @error('notes') border-danger-500 @enderror">{{ old('notes', $budgetItem->notes) }}</textarea>
                        @error('notes') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-surface-100">
                    <a href="{{ route('admin.events.budget.index', $budgetItem->event_id) }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
