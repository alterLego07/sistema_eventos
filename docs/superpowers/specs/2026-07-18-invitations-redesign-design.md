# Rediseño de Vista de Invitaciones — Especificación Técnica

**Fecha:** 2026-07-18  
**Versión:** 1.0  
**Estado:** Aprobado

---

## Resumen Ejecutivo

Mejora integral de la vista `events/{event}/invitations` agregando:
1. Sistema de tres estados para invitaciones (Pendiente, Confirmado, Declinado)
2. Confirmación manual de invitaciones por admin desde modal
3. Filtros por estado, mesa e invitado
4. Mejoras visuales: botones agrupados, cabeceras centradas
5. Corrección de bug donde "No asistirá" permitía re-responder

---

## 1. Cambios en la Base de Datos

### Migración Nueva: `add_status_to_invitations_table`

```
Nueva columna: status (string/enum)
- Valores: pending, confirmed, declined
- Default: pending
- Nullable: false
```

**Mapeo de datos existentes durante migración:**
```php
// confirmed = true  →  status = 'confirmed'
// confirmed = false →  status = 'pending'
```

**Deprecación:**
- Mantener columna `confirmed` para compatibilidad backwards
- Futura versión: remover `confirmed` (no en este sprint)

---

## 2. Cambios en el Modelo

### Modelo `Invitation.php`

**Agregar a `$fillable`:**
```php
'status',
```

**Actualizar `statusLabel()` accessor:**
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

**Agregar scopes:**
```php
public function scopePending(Builder $query): Builder
{
    return $query->where('status', 'pending');
}

public function scopeConfirmed(Builder $query): Builder
{
    return $query->where('status', 'confirmed');
}

public function scopeDeclined(Builder $query): Builder
{
    return $query->where('status', 'declined');
}
```

**Agregar accessores (helpers):**
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

---

## 3. Cambios en Controladores

### PublicInvitationController.php — Método `rsvp()`

**Validación actualizada:**
```php
// Si status !== 'pending', bloquear
if ($invitation->status !== 'pending') {
    return redirect()->route('invitation.show', $token)
        ->with('rsvp_already_responded', true);
}
```

**Guardar respuesta:**
```php
$invitation->update([
    'status'                => $attending ? 'confirmed' : 'declined',
    'confirmed_guests'      => $attending ? (int) $validated['confirmed_guests'] : 0,
    'confirmed_at'          => now(),
    'dietary_restrictions'  => $validated['dietary_restrictions'] ?? null,
    'message'               => $validated['message'] ?? null,
    'song_suggestion'       => $validated['song_suggestion'] ?? null,
]);
```

**Mensaje de respuesta:**
```php
return redirect()->route('invitation.show', $token)
    ->with($attending ? 'rsvp_confirmed' : 'rsvp_declined', true);
```

---

### InvitationController.php — Nuevo Método `updateStatus()`

**Ruta nueva:** `PUT /admin/invitations/{invitation}/status`

**Controlador:**
```php
#[Middleware('permission:invitations.edit')]
public function updateStatus(Request $request, Invitation $invitation)
{
    $validated = $request->validate([
        'status' => ['required', 'in:pending,confirmed,declined'],
    ]);

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

---

## 4. Vista de Invitaciones del Admin

### Archivo: `resources/views/admin/invitations/index.blade.php`

**Cambios principales:**

#### 4.1. Tabla — Cabeceras Centradas
```html
<th class="text-center">Estado</th>
<th class="text-center">Confirmados</th>
<th class="text-center">Mesa</th>
<th class="text-center">Permitidos</th>
```

#### 4.2. Badge de Estado (actualizado)
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

#### 4.3. Acciones — Agrupar en Dropdown
**Reemplazar botones individuales con:**
```html
<td class="text-right">
    <div class="dropdown">
        <button class="btn btn-ghost btn-sm" data-bs-toggle="dropdown">
            ⋮ Opciones
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ $inv->invitation_url }}" target="_blank">
                👁️ Ver invitación
            </a></li>
            <li><a class="dropdown-item" href="{{ route('admin.invitations.edit', $inv) }}">
                ✏️ Editar
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><button class="dropdown-item" @click="openStatusModal({{ $inv->id }})">
                ✓ Cambiar estado
            </button></li>
            <li><button class="dropdown-item text-danger" @click="openDeleteModal({{ $inv->id }})">
                🗑️ Eliminar
            </button></li>
            <li><hr class="dropdown-divider"></li>
            <li><button class="dropdown-item text-success" @click="openWa({{ json_encode($inv->guest_name) }})">
                💬 WhatsApp
            </button></li>
        </ul>
    </div>
</td>
```

#### 4.4. Modal de Cambio de Estado
```html
<!-- Modal Cambiar Estado -->
<div x-show="statusModalOpen" class="modal" @keydown.escape="statusModalOpen = false">
    <div class="modal-content">
        <h3>Cambiar estado de invitación</h3>
        <p>{{ statusModalGuest }}</p>
        
        <div class="modal-buttons">
            <button @click="updateInvitationStatus(statusModalId, 'confirmed')" 
                    class="btn btn-success">
                ✓ Confirmar asistencia
            </button>
            <button @click="updateInvitationStatus(statusModalId, 'declined')" 
                    class="btn btn-danger">
                ✗ Declinar asistencia
            </button>
            <button @click="updateInvitationStatus(statusModalId, 'pending')" 
                    class="btn btn-warning">
                ⊘ Volver a Pendiente
            </button>
        </div>
        
        <button @click="statusModalOpen = false" class="btn btn-secondary">
            Cancelar
        </button>
    </div>
</div>
```

---

### 4.5. Filtros (orden de prioridad)

**Estructura:**
```html
<div class="filters-bar mb-4">
    <!-- 1. Filtro de Estado (Prioridad 1) -->
    <select name="status" class="form-control" @change="applyFilters()">
        <option value="">Todos los estados</option>
        <option value="pending">Pendiente</option>
        <option value="confirmed">Confirmado</option>
        <option value="declined">No asistirá</option>
    </select>
    
    <!-- 2. Filtro de Mesa (Prioridad 2) -->
    <select name="table" class="form-control" @change="applyFilters()">
        <option value="">Todas las mesas</option>
        @foreach($tables as $table)
            <option value="{{ $table }}">Mesa {{ $table }}</option>
        @endforeach
    </select>
    
    <!-- 3. Búsqueda de Invitado (Prioridad 3) -->
    <input type="text" name="guest" class="form-control" 
           placeholder="Buscar por nombre del invitado..." 
           @input.debounce.500="applyFilters()">
</div>
```

**Backend - InvitationController:**
```php
public function index(Event $event, Request $request)
{
    $query = $event->invitations();
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('table')) {
        $query->where('table_number', $request->table);
    }
    
    if ($request->filled('guest')) {
        $query->where('guest_name', 'like', '%'.$request->guest.'%');
    }
    
    $invitations = $query->latest()->paginate(20);
    
    return view('admin.invitations.index', compact('event', 'invitations'));
}
```

---

## 5. Vista de Invitación Pública (Invitado)

### Archivo: `resources/views/invitation-templates/default.blade.php`

**Cambios en sección RSVP:**

#### 5.1. Mostrar estado actual
```php
@if($invitation->isConfirmed)
    <div class="rsvp-status">
        <div class="rsvp-status__icon">🥂</div>
        <h3>Ya confirmaste tu asistencia</h3>
        <p>Con {{ $invitation->confirmed_guests }} 
           {{ $invitation->confirmed_guests === 1 ? 'persona' : 'personas' }}</p>
    </div>
@elseif($invitation->isDeclined)
    <div class="rsvp-status">
        <div class="rsvp-status__icon">💐</div>
        <h3>Gracias por avisar</h3>
        <p>Lamentamos que no puedas asistir. Si cambias de opinión, 
           contacta al organizador.</p>
    </div>
@else
    <!-- Mostrar formulario RSVP -->
    <form method="POST" action="{{ route('invitation.rsvp', $invitation->token) }}">
        @csrf
        <!-- Opciones Asistiré / No podré asistir -->
        <!-- Campos adicionales solo si confirma -->
    </form>
@endif
```

#### 5.2. Mensaje de error mejorado
```php
@if(session('rsvp_already_responded'))
    <div class="alert alert-info">
        Ya respondiste a esta invitación y no puedes cambiar tu respuesta.
        Si necesitas cambiar, contacta al organizador.
    </div>
@endif
```

---

## 6. Data Flow

```
Invitado responde en formulario público
    ↓
POST /i/{token}/rsvp
    ↓
PublicInvitationController::rsvp()
    ├─ Valida: status === 'pending' (si no, rechaza)
    └─ Guarda: status = 'confirmed' | 'declined'
    
Admin cambia estado manualmente
    ↓
PUT /admin/invitations/{id}/status (AJAX)
    ↓
InvitationController::updateStatus()
    ├─ Valida: permiso invitations.edit
    └─ Guarda: status actualizado
    
Tabla se actualiza sin recargar ✓
```

---

## 7. Testing

**Escenarios críticos:**

1. ✅ Invitado confirma → status = confirmed, no puede cambiar
2. ✅ Invitado declina → status = declined, no puede cambiar
3. ✅ Admin cambia pending → confirmed
4. ✅ Admin cambia confirmed → declined
5. ✅ Filtros funcionan: estado, mesa, invitado
6. ✅ Cabeceras centradas en tabla
7. ✅ Dropdown de acciones agrupa botones
8. ✅ Respuesta AJAX en modal sin recargar página

---

## 8. Rollback & Compatibilidad

- Mantener columna `confirmed` para backwards compatibility
- Código nuevo usa `status`, código antiguo sigue funcionando
- Migración es safe: no elimina datos

---

## 9. Scope Out (No incluido)

- Cantidad de permitidos como filtro (menor prioridad)
- Restricciones alimentarias en tabla
- Exportar/descargar datos

