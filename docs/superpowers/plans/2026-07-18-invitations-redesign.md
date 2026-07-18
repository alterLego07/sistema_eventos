# Rediseño de Vista de Invitaciones — Plan de Implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Agregar sistema de tres estados a invitaciones, permitir confirmación manual por admin, y mejorar UX de tabla con filtros y UI centrada.

**Architecture:** Cambios en capas: BD (nueva columna status), modelo (scopes/accessores), controladores (validación y CRUD de estado), vistas (tabla mejorada con dropdown/modal, filtros). Mantener compatibilidad backwards con campo `confirmed`.

**Tech Stack:** Laravel 10+, Blade, Alpine.js (ya en proyecto), Tailwind CSS

## Global Constraints

- Mantener columna `confirmed` para backwards compatibility
- Invitado puede responder UNA sola vez (status !== 'pending' = bloqueado)
- Admin puede cambiar estado en cualquier momento
- Badge de estado: warning (pending), success (confirmed), danger (declined)
- Filtros en orden: Estado > Mesa > Invitado (por prioridad)
- Cabeceras de tabla centradas
- Acciones agrupadas en dropdown

---

## Task 1: Crear Migración — Agregar Columna `status`

**Files:**
- Create: `database/migrations/2026_07_18_000000_add_status_to_invitations_table.php`

**Interfaces:**
- Produces: Columna `status` enum (pending/confirmed/declined) en tabla `invitations`

- [ ] **Step 1: Crear archivo de migración**

```bash
php artisan make:migration add_status_to_invitations_table
```

- [ ] **Step 2: Escribir código de migración**

Reemplazar contenido de `database/migrations/2026_07_18_000000_add_status_to_invitations_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Agregar columna status con valores enum
            $table->enum('status', ['pending', 'confirmed', 'declined'])
                  ->default('pending')
                  ->after('token');
        });

        // Mapear datos existentes: confirmed=true -> status='confirmed'
        DB::table('invitations')
            ->where('confirmed', true)
            ->update(['status' => 'confirmed']);
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
```

- [ ] **Step 3: Ejecutar migración**

```bash
php artisan migrate
```

Expected output: Migration completed successfully

- [ ] **Step 4: Verificar en BD**

```bash
php artisan tinker
>>> DB::table('invitations')->first();
// Debe mostrar columna 'status' con valores
```

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_07_18_000000_add_status_to_invitations_table.php
git commit -m "feat: add status column to invitations table

Adds enum status (pending/confirmed/declined) and migrates existing confirmed data"
```

---

## Task 2: Actualizar Modelo `Invitation` — Agregar Status Support

**Files:**
- Modify: `app/Models/Invitation.php`

**Interfaces:**
- Produces: 
  - `$invitation->status` (pending|confirmed|declined)
  - `$invitation->status_label` (Pendiente|Confirmado|No asistirá)
  - `$invitation->isPending` / `isConfirmed` / `isDeclined` (booleans)
  - Scopes: `->pending()`, `->confirmed()`, `->declined()`

- [ ] **Step 1: Abrir archivo modelo**

```bash
code app/Models/Invitation.php
```

- [ ] **Step 2: Agregar `status` a $fillable**

Buscar array `$fillable` (línea ~49) y agregar:

```php
protected $fillable = [
    'company_id',
    'event_id',
    'token',
    'guest_name',
    'phone',
    'email',
    'table_number',
    'allowed_guests',
    'status',  // ← AGREGAR
    'confirmed',
    'confirmed_guests',
    'confirmed_at',
    'dietary_restrictions',
    'message',
    'song_suggestion',
];
```

- [ ] **Step 3: Agregar casts para `status`**

En método `casts()` (línea ~71), agregar:

```php
protected function casts(): array
{
    return [
        'confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
        'status' => 'string', // ← AGREGAR
    ];
}
```

- [ ] **Step 4: Reemplazar método `statusLabel()` accessor**

Buscar y reemplazar método `statusLabel()` (línea ~160):

```php
protected function statusLabel(): Attribute
{
    return Attribute::make(
        get: fn (): string => match($this->status) {
            'confirmed' => 'Confirmado',
            'declined' => 'No asistirá',
            default => 'Pendiente',
        },
    );
}
```

- [ ] **Step 5: Agregar accessores computed (helpers)**

Después del método `statusLabel()`, agregar:

```php
#[Computed]
public function isPending(): bool
{
    return $this->status === 'pending';
}

#[Computed]
public function isConfirmed(): bool
{
    return $this->status === 'confirmed';
}

#[Computed]
public function isDeclined(): bool
{
    return $this->status === 'declined';
}
```

- [ ] **Step 6: Actualizar scopes**

Reemplazar los scopes existentes `confirmed()` y `pending()` (línea ~127-141):

```php
/**
 * Scope a query to only include pending (no respuesta) invitations.
 */
public function scopePending(Builder $query): Builder
{
    return $query->where('status', 'pending');
}

/**
 * Scope a query to only include confirmed invitations.
 */
public function scopeConfirmed(Builder $query): Builder
{
    return $query->where('status', 'confirmed');
}

/**
 * Scope a query to only include declined invitations.
 */
public function scopeDeclined(Builder $query): Builder
{
    return $query->where('status', 'declined');
}
```

- [ ] **Step 7: Verificar modelo con Tinker**

```bash
php artisan tinker
>>> $inv = Invitation::first();
>>> $inv->status;
>>> $inv->status_label;
>>> $inv->isPending;
>>> Invitation::pending()->count();
```

Expected: status se ve, labels funcionan, scopes devuelven datos

- [ ] **Step 8: Commit**

```bash
git add app/Models/Invitation.php
git commit -m "feat: add status field support to Invitation model

- Add status to fillable and casts
- Replace statusLabel() to use enum values
- Add computed accessors: isPending, isConfirmed, isDeclined
- Update scopes to query by status"
```

---

## Task 3: Crear Request Validador para Cambio de Estado

**Files:**
- Create: `app/Http/Requests/UpdateInvitationStatusRequest.php`

**Interfaces:**
- Produces: Request class que valida `status` en enum (pending|confirmed|declined)

- [ ] **Step 1: Generar clase Request**

```bash
php artisan make:request UpdateInvitationStatusRequest
```

- [ ] **Step 2: Escribir validaciones**

Abrir `app/Http/Requests/UpdateInvitationStatusRequest.php` y reemplazar:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvitationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('invitations.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,confirmed,declined'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El estado es requerido.',
            'status.in' => 'El estado debe ser: pending, confirmed o declined.',
        ];
    }
}
```

- [ ] **Step 3: Verificar sintaxis**

```bash
php artisan tinker
>>> use App\Http\Requests\UpdateInvitationStatusRequest;
>>> (new UpdateInvitationStatusRequest())->rules();
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Requests/UpdateInvitationStatusRequest.php
git commit -m "feat: add UpdateInvitationStatusRequest validator"
```

---

## Task 4: Actualizar `PublicInvitationController` — Bloquear Re-respuestas

**Files:**
- Modify: `app/Http/Controllers/PublicInvitationController.php`

**Interfaces:**
- Consumes: `Invitation::$status`, validación POST
- Produces: Método `rsvp()` que bloquea si status !== 'pending', guarda status='confirmed' o 'declined'

- [ ] **Step 1: Abrir controlador público**

```bash
code app/Http/Controllers/PublicInvitationController.php
```

- [ ] **Step 2: Actualizar método `rsvp()` — agregar validación de status**

Reemplazar línea 34-37 (check de confirmado previo):

```php
// Bloquear si ya respondió (status !== pending)
if ($invitation->status !== 'pending') {
    return redirect()->route('invitation.show', $token)
        ->with('rsvp_already_responded', true);
}
```

- [ ] **Step 3: Actualizar guardado de invitación**

Reemplazar línea 59-66 (update):

```php
$attending = (bool) $validated['confirmed'];

$invitation->update([
    'status'                => $attending ? 'confirmed' : 'declined',
    'confirmed_guests'      => $attending ? (int) ($validated['confirmed_guests'] ?? 1) : 0,
    'confirmed_at'          => now(),
    'dietary_restrictions'  => $validated['dietary_restrictions'] ?? null,
    'message'               => $validated['message'] ?? null,
    'song_suggestion'       => $validated['song_suggestion'] ?? null,
]);
```

- [ ] **Step 4: Verificar controlador (lectura manual)**

El método completo debe quedar así:

```php
public function rsvp(Request $request, string $token)
{
    $invitation = Invitation::where('token', $token)
        ->with('event')
        ->firstOrFail();

    if ($invitation->status !== 'pending') {
        return redirect()->route('invitation.show', $token)
            ->with('rsvp_already_responded', true);
    }

    $validated = $request->validate([
        'confirmed'           => ['required', 'boolean'],
        'confirmed_guests'    => [
            'required_if:confirmed,1',
            'integer',
            'min:1',
            "max:{$invitation->allowed_guests}",
        ],
        'dietary_restrictions' => ['nullable', 'string', 'max:500'],
        'message'              => ['nullable', 'string', 'max:1000'],
        'song_suggestion'      => ['nullable', 'string', 'max:255'],
    ], [
        'confirmed.required'           => 'Indicá si confirmás tu asistencia.',
        'confirmed_guests.required_if' => 'Indicá cuántas personas asistirán.',
        'confirmed_guests.min'         => 'Al menos 1 persona debe asistir.',
        'confirmed_guests.max'         => "Máximo {$invitation->allowed_guests} invitado(s) permitido(s).",
    ]);

    $attending = (bool) $validated['confirmed'];

    $invitation->update([
        'status'                => $attending ? 'confirmed' : 'declined',
        'confirmed_guests'      => $attending ? (int) ($validated['confirmed_guests'] ?? 1) : 0,
        'confirmed_at'          => now(),
        'dietary_restrictions'  => $validated['dietary_restrictions'] ?? null,
        'message'               => $validated['message'] ?? null,
        'song_suggestion'       => $validated['song_suggestion'] ?? null,
    ]);

    return redirect()->route('invitation.show', $token)
        ->with($attending ? 'rsvp_confirmed' : 'rsvp_declined', true);
}
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/PublicInvitationController.php
git commit -m "fix: block re-responses and save declined status

- Check status !== 'pending' to block already-responded invitees
- Save status='confirmed' or 'declined' on rsvp
- Set confirmed_guests=0 when declining"
```

---

## Task 5: Actualizar `InvitationController` Admin — Agregar Método `updateStatus`

**Files:**
- Modify: `app/Http/Controllers/Admin/InvitationController.php`

**Interfaces:**
- Consumes: `UpdateInvitationStatusRequest`, Invitation model
- Produces: Route `PUT /admin/invitations/{invitation}/status`, devuelve JSON

- [ ] **Step 1: Abrir controlador admin**

```bash
code app/Http/Controllers/Admin/InvitationController.php
```

- [ ] **Step 2: Agregar use de Request al inicio**

Después de `use Illuminate\Http\Request;`, agregar:

```php
use App\Http\Requests\UpdateInvitationStatusRequest;
use Illuminate\Http\JsonResponse;
```

- [ ] **Step 3: Agregar método `updateStatus()` al final de la clase**

Antes de cerrar la clase `}`, agregar:

```php
#[Middleware('permission:invitations.edit')]
public function updateStatus(UpdateInvitationStatusRequest $request, Invitation $invitation): JsonResponse
{
    $validated = $request->validated();
    
    $wasNull = $invitation->status === 'pending';
    
    $invitation->update([
        'status'       => $validated['status'],
        'confirmed_at' => $wasNull && $validated['status'] !== 'pending' 
                          ? now() 
                          : $invitation->confirmed_at,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Estado actualizado correctamente.',
        'status'  => $invitation->status,
        'label'   => $invitation->status_label,
    ]);
}
```

- [ ] **Step 4: Registrar ruta en `routes/web.php`**

Abrir `routes/web.php` y buscar la sección de invitaciones (línea con `admin.invitations`).

Agregar después de las rutas CRUD existentes:

```php
Route::put('invitations/{invitation}/status', [InvitationController::class, 'updateStatus'])
    ->name('admin.invitations.updateStatus');
```

- [ ] **Step 5: Verificar ruta**

```bash
php artisan route:list | grep updateStatus
```

Expected: `PUT /admin/invitations/{invitation}/status ... invitations.updateStatus`

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/InvitationController.php routes/web.php
git commit -m "feat: add admin endpoint to change invitation status

- New method updateStatus() validates and updates status
- Endpoint: PUT /admin/invitations/{invitation}/status
- Returns JSON with updated status and label
- Records confirmed_at on status change from pending"
```

---

## Task 6: Actualizar Vista Admin — Tabla Mejorada (Parte 1: Dropdown de Acciones)

**Files:**
- Modify: `resources/views/admin/invitations/index.blade.php`

**Interfaces:**
- Consumes: Invitation model con status y scopes
- Produces: Tabla con dropdown de acciones

- [ ] **Step 1: Abrir vista de invitaciones**

```bash
code resources/views/admin/invitations/index.blade.php
```

- [ ] **Step 2: Actualizar cabeceras de tabla — Centrar**

Buscar `<thead>` (línea 74) y reemplazar todo el bloque de `<tr>`:

```html
<thead>
    <tr>
        <th class="text-left">Invitado</th>
        <th class="text-center">Contacto</th>
        <th class="text-center">Mesa</th>
        <th class="text-center">Permitidos</th>
        <th class="text-center">Estado</th>
        <th class="text-center">Confirmados</th>
        <th class="text-center">Acciones</th>
    </tr>
</thead>
```

- [ ] **Step 3: Actualizar badges de estado**

Buscar línea 101-104 (badge span) y reemplazar:

```html
<td class="text-center">
    <span class="badge {{ match($inv->status) {
        'confirmed' => 'badge-success',
        'declined' => 'badge-danger',
        default => 'badge-warning'
    } }}">
        {{ $inv->status_label }}
    </span>
</td>
```

- [ ] **Step 4: Reemplazar sección de acciones (botones dispersos)**

Buscar línea 109-154 (div con botones individuales) y reemplazar COMPLETAMENTE:

```html
<td class="text-center">
    <div class="dropdown">
        <button class="btn btn-ghost btn-icon btn-sm dropdown-toggle" 
                type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="5" r="2"/>
                <circle cx="12" cy="12" r="2"/>
                <circle cx="12" cy="19" r="2"/>
            </svg>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 200px;">
            {{-- Ver Invitación --}}
            <li>
                <a class="dropdown-item text-sm" href="{{ $inv->invitation_url }}" target="_blank">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Ver invitación
                </a>
            </li>
            
            {{-- Editar --}}
            <li>
                <a class="dropdown-item text-sm" href="{{ route('admin.invitations.edit', $inv) }}">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            {{-- Cambiar Estado --}}
            <li>
                <button class="dropdown-item text-sm" type="button"
                        @click="openStatusModal({{ $inv->id }}, '{{ $inv->guest_name }}', '{{ $inv->status }}')">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Cambiar estado
                </button>
            </li>

            {{-- WhatsApp --}}
            <li>
                <button class="dropdown-item text-sm text-success" type="button"
                        @click="openWa('{{ json_encode($inv->guest_name) }}', '{{ json_encode($waMsg) }}')">
                    <svg class="w-4 h-4 inline mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </button>
            </li>

            {{-- Eliminar --}}
            <li>
                <form method="POST" action="{{ route('admin.invitations.destroy', $inv) }}"
                      x-data @submit.prevent="if(confirm('¿Eliminar esta invitación?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit" class="dropdown-item text-sm text-danger">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar
                    </button>
                </form>
            </li>
        </ul>
    </div>
</td>
```

- [ ] **Step 5: Commit parcial (agregar Bootstrap JS si falta)**

Verificar que el layout tenga Bootstrap incluido. Si no, abrir `resources/views/layouts/admin.blade.php` y verificar que tiene:

```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

```bash
git add resources/views/admin/invitations/index.blade.php
git commit -m "feat: refactor invitation actions to dropdown menu

- Group WhatsApp, View, Edit, Change Status, Delete in dropdown
- Center all table headers
- Update status badges with correct colors (warning/success/danger)"
```

---

## Task 7: Actualizar Vista Admin — Modal de Cambio de Estado

**Files:**
- Modify: `resources/views/admin/invitations/index.blade.php`

**Interfaces:**
- Consumes: updateStatus() endpoint
- Produces: Modal Alpine.js para cambiar estado

- [ ] **Step 1: Agregar variables x-data al inicio de la tabla**

Buscar `x-data="{` (línea ~37) y reemplazar/extender:

```html
<div
    class="bg-white rounded-2xl border border-surface-100 shadow-sm overflow-visible"
    x-data="{
        waOpen: false,
        waGuest: '',
        waMessage: '',
        waCopied: false,
        statusModalOpen: false,
        statusModalId: null,
        statusModalGuest: '',
        statusModalCurrent: '',
        
        openWa(guest, message) {
            this.waGuest = guest;
            this.waMessage = message;
            this.waCopied = false;
            this.waOpen = true;
        },
        
        openStatusModal(id, guest, status) {
            this.statusModalId = id;
            this.statusModalGuest = guest;
            this.statusModalCurrent = status;
            this.statusModalOpen = true;
        },
        
        async updateInvitationStatus(id, newStatus) {
            try {
                const response = await fetch(`/admin/invitations/${id}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    // Recargar tabla (o actualizar fila específica)
                    window.location.reload();
                } else {
                    alert('Error al actualizar estado');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        },
        
        async copyMessage() {
            try {
                await navigator.clipboard.writeText(this.waMessage);
                this.waCopied = true;
                setTimeout(() => { this.waCopied = false; }, 2500);
            } catch(e) {}
        }
    }"
    @keydown.escape.window="waOpen = false; statusModalOpen = false"
>
```

- [ ] **Step 2: Agregar Modal HTML antes del cierre de div principal**

Buscar `</div>` que cierra el modal de WhatsApp (línea ~240) y después agregar el modal de estado:

```html
        {{-- ── Modal Cambiar Estado ── --}}
        <div
            x-show="statusModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);"
            @click.self="statusModalOpen = false"
        >
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-sm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-surface-100 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-surface-900 text-sm">Cambiar estado</p>
                        <p class="text-xs text-surface-400 mt-0.5" x-text="statusModalGuest"></p>
                    </div>
                    <button type="button" @click="statusModalOpen = false"
                            class="text-surface-400 hover:text-surface-600 p-1 rounded-lg hover:bg-surface-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 space-y-3">
                    <button
                        type="button"
                        @click="updateInvitationStatus(statusModalId, 'confirmed')"
                        class="w-full btn btn-success"
                    >
                        ✓ Confirmar asistencia
                    </button>

                    <button
                        type="button"
                        @click="updateInvitationStatus(statusModalId, 'declined')"
                        class="w-full btn btn-danger"
                    >
                        ✗ Declinar asistencia
                    </button>

                    <button
                        type="button"
                        @click="updateInvitationStatus(statusModalId, 'pending')"
                        class="w-full btn btn-warning"
                    >
                        ⊘ Volver a Pendiente
                    </button>
                </div>

                {{-- Footer --}}
                <div class="px-6 pb-4 flex gap-3">
                    <button type="button" @click="statusModalOpen = false" class="btn btn-secondary flex-1">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/invitations/index.blade.php
git commit -m "feat: add status change modal for admin

- Alpine.js modal to change invitation status
- Three buttons: Confirm / Decline / Revert to Pending
- AJAX call to updateStatus endpoint
- Page reloads on success"
```

---

## Task 8: Actualizar Vista Admin — Agregar Filtros

**Files:**
- Modify: `resources/views/admin/invitations/index.blade.php`, `app/Http/Controllers/Admin/InvitationController.php`

**Interfaces:**
- Consumes: Query parameters (status, table_number, guest_name)
- Produces: Filtros en dropdown/input, tabla filtrada

- [ ] **Step 1: Actualizar método `index()` en InvitationController**

Abrir `app/Http/Controllers/Admin/InvitationController.php` y reemplazar método `index()`:

```php
#[Middleware('permission:invitations.view')]
public function index(Event $event, Request $request)
{
    $query = $event->invitations();

    // Filtro 1: Estado (prioridad)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filtro 2: Mesa
    if ($request->filled('table')) {
        $table = $request->table;
        if ($table === 'null') {
            $query->whereNull('table_number');
        } else {
            $query->where('table_number', $table);
        }
    }

    // Filtro 3: Invitado (búsqueda)
    if ($request->filled('guest')) {
        $query->where('guest_name', 'like', '%' . $request->guest . '%');
    }

    $invitations = $query->latest()->paginate(20);
    
    // Obtener lista única de mesas para dropdown
    $tables = $event->invitations()
        ->whereNotNull('table_number')
        ->distinct()
        ->orderBy('table_number')
        ->pluck('table_number')
        ->toArray();

    return view('admin.invitations.index', compact('event', 'invitations', 'tables'));
}
```

- [ ] **Step 2: Agregar filtros HTML en vista**

Abrir `resources/views/admin/invitations/index.blade.php` y buscar después del "Event summary bar" (línea 33).

Agregar antes de la tabla:

```html
    {{-- Filtros --}}
    <div class="bg-white rounded-2xl border border-surface-100 shadow-sm p-5 mb-5">
        <form method="GET" action="{{ route('admin.events.invitations.index', $event) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            {{-- Filtro 1: Estado (Prioridad 1) --}}
            <div>
                <label class="form-label">Estado</label>
                <select name="status" class="form-input">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                    <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>No asistirá</option>
                </select>
            </div>

            {{-- Filtro 2: Mesa (Prioridad 2) --}}
            <div>
                <label class="form-label">Mesa</label>
                <select name="table" class="form-input">
                    <option value="">Todas las mesas</option>
                    <option value="null" {{ request('table') === 'null' ? 'selected' : '' }}>Sin asignar</option>
                    @foreach($tables as $table)
                        <option value="{{ $table }}" {{ request('table') == $table ? 'selected' : '' }}>
                            Mesa {{ $table }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro 3: Invitado (Prioridad 3) --}}
            <div>
                <label class="form-label">Buscar invitado</label>
                <input type="text" name="guest" class="form-input" placeholder="Nombre del invitado..."
                       value="{{ request('guest') }}">
            </div>

            {{-- Botones --}}
            <div class="flex items-end gap-2 col-span-full">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.events.invitations.index', $event) }}" class="btn btn-secondary">
                    Limpiar filtros
                </a>
            </div>
        </form>
    </div>
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/InvitationController.php resources/views/admin/invitations/index.blade.php
git commit -m "feat: add invitation filters (status, table, guest name)

- Filter priority: Status > Table > Guest
- GET params: ?status=pending&table=1&guest=John
- Display unique table numbers in dropdown
- Clear filters button"
```

---

## Task 9: Actualizar Vista Pública — Mostrar Estado y Bloquear Respuesta

**Files:**
- Modify: `resources/views/invitation-templates/default.blade.php`

**Interfaces:**
- Consumes: `$invitation->status`, `$invitation->isPending/isConfirmed/isDeclined`
- Produces: Mostrar estado si ya respondió, bloquear formulario RSVP, mensaje de no-puede-cambiar

- [ ] **Step 1: Abrir plantilla de invitación**

```bash
code resources/views/invitation-templates/default.blade.php
```

- [ ] **Step 2: Buscar sección RSVP (línea ~711)**

Reemplazar todo el bloque `@if($invitation->confirmed ?? false)` al `@endif` (aprox línea 711-781):

```blade
                    @if($invitation->isConfirmed)
                        <div class="rsvp-status">
                            <div class="rsvp-status__icon">🥂</div>
                            <h3>Ya confirmaste tu asistencia</h3>
                            <p>Con {{ $invitation->confirmed_guests }} 
                               {{ $invitation->confirmed_guests === 1 ? 'persona' : 'personas' }}</p>
                            @if($invitation->confirmed_at)
                                <p class="text-xs text-surface-400 mt-2">{{ $invitation->confirmed_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    @elseif($invitation->isDeclined)
                        <div class="rsvp-status">
                            <div class="rsvp-status__icon">💐</div>
                            <h3>Gracias por avisar</h3>
                            <p>Lamentamos que no puedas asistir.</p>
                            <p class="text-xs text-surface-400 mt-3">Si cambias de opinión, contactá al organizador.</p>
                        </div>
                    @elseif(session('rsvp_confirmed'))
                        <div class="rsvp-status">
                            <div class="rsvp-status__icon">🎊</div>
                            <h3>¡Confirmado!</h3>
                            <p>Tu respuesta fue registrada correctamente.</p>
                        </div>
                    @elseif(session('rsvp_declined'))
                        <div class="rsvp-status">
                            <div class="rsvp-status__icon">💐</div>
                            <h3>Respuesta registrada</h3>
                            <p>Gracias por avisar que no podrás asistir.</p>
                        </div>
                    @elseif(session('rsvp_already_responded'))
                        <div class="form-error" style="border: 1px solid rgba(185, 28, 28, .18);">
                            <p class="text-sm">Ya respondiste a esta invitación y no puedes cambiar tu respuesta.</p>
                            <p class="text-xs text-surface-400 mt-2">Si necesitas cambiar, contactá al organizador.</p>
                        </div>
                    @else
                        {{-- Formulario RSVP --}}
                        <form method="POST" action="{{ route('invitation.rsvp', $invitation->token) }}" id="rsvpForm">
                            @csrf

                            <div class="rsvp-buttons">
                                <button type="button" class="rsvp-btn" data-rsvp-value="1">✦ Asistiré</button>
                                <button type="button" class="rsvp-btn" data-rsvp-value="0">No podré asistir</button>
                            </div>
                            <input type="hidden" name="confirmed" id="confirmedInput" value="{{ old('confirmed') }}">

                            <div id="attendingFields" class="is-hidden">
                                <div class="form-group">
                                    <label class="form-label">¿Cuántos asistirán? Máx. {{ $invitation->allowed_guests ?? 1 }}</label>
                                    <input class="form-input" type="number" name="confirmed_guests" min="1" max="{{ $invitation->allowed_guests ?? 1 }}" value="{{ old('confirmed_guests', 1) }}">
                                </div>

                                @if(in_array('dietary', $sections))
                                    <div class="form-group">
                                        <label class="form-label">Restricciones alimentarias</label>
                                        <input class="form-input" type="text" name="dietary_restrictions" value="{{ old('dietary_restrictions') }}" placeholder="Vegetariano, celíaco, alérgico a...">
                                    </div>
                                @endif

                                @if(in_array('song', $sections))
                                    <div class="form-group">
                                        <label class="form-label">Sugerí una canción</label>
                                        <input class="form-input" type="text" name="song_suggestion" value="{{ old('song_suggestion') }}" placeholder="Artista — Canción">
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="form-label">Mensaje para los anfitriones</label>
                                    <textarea class="form-input" name="message" placeholder="Opcional...">{{ old('message') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn--full" id="submitRsvpBtn" disabled>Enviar confirmación</button>
                        </form>
                    @endif
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/invitation-templates/default.blade.php
git commit -m "feat: show status and block re-response in public invitation

- Show confirmed/declined status with custom messages
- Hide RSVP form if already responded
- Display error if trying to respond again
- Show rsvp_confirmed/rsvp_declined flash messages"
```

---

## Task 10: Testing — Verificar Todos los Flujos

**Files:**
- Manual testing (sin archivos)

**Interfaces:**
- Prueba funcional completa

- [ ] **Step 1: Crear invitaciones de prueba**

```bash
php artisan tinker
>>> $event = Event::first();
>>> for ($i = 0; $i < 5; $i++) {
      $event->invitations()->create([
        'guest_name' => "Test Guest $i",
        'email' => "guest$i@test.com",
        'allowed_guests' => 2,
        'table_number' => $i % 3 + 1,
        'status' => ['pending', 'confirmed', 'declined'][$i % 3],
      ]);
    }
>>> exit
```

- [ ] **Step 2: Acceder a vista admin de invitaciones**

```
http://localhost:8000/admin/events/{event_id}/invitations
```

Expected:
- ✅ Tabla visible con 5 invitaciones
- ✅ Estados mostrados correctamente: Pendiente (amarillo), Confirmado (verde), No asistirá (rojo)
- ✅ Cabeceras centradas
- ✅ Botones agrupados en dropdown (⋮ Opciones)
- ✅ Filtros visibles (Estado, Mesa, Invitado)

- [ ] **Step 3: Probar filtros**

Filtrar por estado "Confirmado":
```
?status=confirmed
```
Expected: Solo 1-2 invitaciones visibles

Filtrar por mesa 1:
```
?status=&table=1
```
Expected: Solo invitaciones en mesa 1

Buscar por nombre "Test Guest 2":
```
?guest=Test%20Guest%202
```
Expected: Solo 1 resultado

- [ ] **Step 4: Probar modal de cambio de estado**

Clic en dropdown → "Cambiar estado" → Modal aparece
- Cambiar a "Confirmar asistencia" → Verificar que status cambió
- Verificar que la tabla se actualiza (recarga)

- [ ] **Step 5: Probar bloqueo de re-respuesta**

Copiar URL de invitación (`http://localhost:8000/i/{token}`)

Primera respuesta:
- Hacer clic en "Asistiré"
- Ingresar # de personas
- Enviar confirmación
- Expected: ✅ Confirmado con 🥂

Acceder de nuevo a la misma URL:
- Expected: Mostrar estado confirmado y NO mostrar formulario
- No debe permitir cambiar respuesta

- [ ] **Step 6: Probar decline flow**

Crear nueva invitación en Tinker con status='pending'
Acceder a su URL
Hacer clic en "No podré asistir"
Enviar
Expected:
- ✅ Estado cambia a "No asistirá" 
- ✅ Mensaje "Gracias por avisar"
- ✅ Acceso posterior muestra "Gracias por avisar" (no permite cambio)

- [ ] **Step 7: Commit (después de verificar todo)**

```bash
git commit --allow-empty -m "test: verify all invitation flows working

Tested:
- Table display with correct status colors
- Dropdown menu grouping
- Filter functionality (state, table, guest)
- Status change modal
- Public RSVP response blocking
- Confirmed/Declined flows"
```

---

## Task 11: Limpieza y Ajustes Finales

**Files:**
- Verificación de detalles menores

- [ ] **Step 1: Verificar mensajes flash**

En `PublicInvitationController`, verificar que los mensajes enviados a la vista sean mostrados correctamente.

Ver en `resources/views/invitation-templates/default.blade.php` que:
- `session('rsvp_confirmed')` → "¡Confirmado!"
- `session('rsvp_declined')` → "Respuesta registrada"
- `session('rsvp_already_responded')` → "Ya respondiste..."

- [ ] **Step 2: Verificar estilos de dropdown Bootstrap**

Asegurarse que el layout principal tenga Bootstrap 5:

```bash
grep -r "bootstrap@5" resources/views/layouts/
```

Si no existe, agregar a `resources/views/layouts/admin.blade.php`:

```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

- [ ] **Step 3: Verificar permisos de ruta**

Confirmar que ruta `updateStatus` tiene permiso `invitations.edit`:

```bash
php artisan route:list | grep updateStatus
```

Expected: middleware "permission:invitations.edit"

- [ ] **Step 4: Verificar que no hay errores SQL**

```bash
php artisan tinker
>>> DB::enableQueryLog();
>>> Invitation::with('event')->pending()->get();
>>> dd(DB::getQueryLog());
```

Expected: Queries limpias, sin N+1 queries

- [ ] **Step 5: Final commit**

```bash
git add -A
git commit -m "cleanup: final adjustments and verification

- Verify Bootstrap integration
- Confirm all permissions are correct
- Check SQL queries for N+1 issues
- Validate all flash messages display correctly"
```

---

## Spec Coverage Check ✓

| Sección Spec | Tarea Implementada |
|---|---|
| 1. BD - Migración | Task 1 ✅ |
| 2. Modelo - Status | Task 2 ✅ |
| 3. Controladores - RSVP | Task 4 ✅ |
| 3. Controladores - updateStatus | Task 5 ✅ |
| 4. Vista Admin - Tabla/Dropdown | Task 6 ✅ |
| 4. Vista Admin - Modal | Task 7 ✅ |
| 4. Vista Admin - Filtros | Task 8 ✅ |
| 5. Vista Pública - Estado/Bloqueo | Task 9 ✅ |
| Testing | Task 10 ✅ |
| Finales | Task 11 ✅ |

✅ **Todas las secciones del spec implementadas**

---

## Notas Importantes

**Rollback:**
```bash
php artisan migrate:rollback
```

**Re-run migración:**
```bash
php artisan migrate
```

**Debugging RSVP:**
```bash
php artisan tinker
>>> $inv = Invitation::first();
>>> $inv->update(['status' => 'pending']); // Reset
```

**Debugging Modal:**
Abrir DevTools → Console → Buscar errores de Alpine.js
Verificar que `x-show` y `@click` funcionan

