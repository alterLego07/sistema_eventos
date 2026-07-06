# Sistema de Invitaciones

Plataforma web para crear y gestionar invitaciones digitales con confirmación de asistencia (RSVP). Construida sobre Laravel 12 con un frontend en Tailwind CSS v4 y Alpine.js.

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade, Tailwind CSS v4, Alpine.js 3 |
| Bundler | Vite 7 + `@tailwindcss/vite` |
| Base de datos | SQLite (local) / MySQL (producción) |
| Autenticación | Laravel Breeze |

---

## Requisitos previos

- PHP >= 8.2
- Composer
- Node.js >= 20
- npm >= 10

---

## Instalación local

```bash
# 1. Clonar el repositorio
git clone <repo-url> sistema-invitaciones
cd sistema-invitaciones

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Base de datos (SQLite por defecto)
touch database/database.sqlite
php artisan migrate --seed

# 6. Compilar assets
npm run build

# 7. Iniciar servidor
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

---

## Scripts npm

| Comando | Descripción |
|---------|-------------|
| `npm run dev` | Servidor de desarrollo Vite con HMR |
| `npm run build` | Build de producción en `public/build/` |

---

## Estructura del proyecto

```
sistema-invitaciones/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/            # Controllers de autenticación (Breeze)
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   │       ├── StoreEventRequest.php
│   │       ├── UpdateEventRequest.php
│   │       ├── StoreInvitationRequest.php
│   │       └── ConfirmInvitationRequest.php
│   ├── Models/
│   │   ├── Event.php
│   │   ├── Invitation.php
│   │   ├── Template.php
│   │   └── User.php
│   └── View/Components/
├── database/
│   ├── migrations/
│   └── seeders/
│       └── TemplateSeeder.php
├── resources/
│   ├── css/app.css              # Design system (Tailwind v4 @theme)
│   ├── js/app.js                # Alpine.js bootstrap
│   └── views/
│       ├── auth/                # Vistas de autenticación
│       ├── components/          # Blade components reutilizables
│       ├── layouts/             # Layouts: app, admin, guest
│       └── profile/
├── routes/
│   ├── web.php
│   └── auth.php
├── vite.config.js
└── postcss.config.js
```

---

## Dominio / Modelos

### User
Usuario administrador del sistema. Propietario de los eventos.

### Event
Representa un evento (boda, cumpleaños, etc.) con su información de fecha, lugar y diseño.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `user_id` | FK | Propietario del evento |
| `template_id` | FK nullable | Plantilla visual asignada |
| `name` | string | Nombre del evento |
| `slug` | string unique | URL amigable |
| `event_date` | date | Fecha del evento |
| `event_time` | time | Hora del evento |
| `location` | string | Lugar del evento |
| `location_url` | string | Enlace a Google Maps |
| `cover_image` | string | Imagen de portada |
| `settings` | json | Configuración RSVP, mensajes personalizados |
| `template_config` | json | Overrides de colores/fuentes sobre la plantilla base |
| `status` | string | `draft` / `published` / `cancelled` / `completed` |

**Accessors:** `formatted_date`, `formatted_time`, `confirmed_count`, `total_expected`, `pending_count`, `confirmation_rate`, `merged_template_config`

**Scopes:** `published()`, `upcoming()`, `draft()`

### Invitation
Invitación individual para un invitado. Accesible públicamente via token único.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `event_id` | FK | Evento al que pertenece |
| `token` | string(16) unique | Token auto-generado para la URL pública |
| `guest_name` | string | Nombre del invitado |
| `phone` / `email` | string | Contacto del invitado |
| `table_number` | int | Mesa asignada |
| `allowed_guests` | int | Máximo de acompañantes permitidos |
| `confirmed` | boolean | Estado de confirmación |
| `confirmed_guests` | int | Número de personas que asistirán |
| `confirmed_at` | timestamp | Fecha/hora de confirmación |
| `dietary_restrictions` | text | Restricciones alimentarias |
| `message` | text | Mensaje personal del invitado |
| `song_suggestion` | string | Canción sugerida |

**URL pública:** `{APP_URL}/i/{token}`

**Scopes:** `confirmed()`, `pending()`

### Template
Plantilla de diseño visual reutilizable entre eventos.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `name` / `slug` | string | Identificación de la plantilla |
| `configuration` | json | Colores, fuentes, secciones habilitadas, animaciones |
| `active` | boolean | Disponible para nuevos eventos |

La configuración base de una plantilla puede ser sobreescrita por `Event.template_config` usando `array_replace_recursive`.

**Configuración por defecto:**
```json
{
  "colors": { "primary": "#C9A96E", "secondary": "#1a1a2e" },
  "fonts": { "heading": "Playfair Display", "body": "Cormorant Garamond" },
  "sections": ["hero", "message", "details", "countdown", "rsvp", "location", "footer"],
  "animations": { "entrance": "fadeInUp", "scroll": true },
  "style": "elegant"
}
```

---

## Relaciones entre modelos

```
User ──< Event >── Template
              │
              └──< Invitation
```

- Un `User` puede tener muchos `Event`s.
- Un `Event` pertenece opcionalmente a un `Template`.
- Un `Event` tiene muchas `Invitation`s.
- Una `Invitation` pertenece a un `Event`.

---

## Diseño del frontend

El sistema de diseño está definido en [resources/css/app.css](resources/css/app.css) usando la API `@theme` de Tailwind CSS v4.

**Variables de diseño:**
- Paleta de colores: `brand`, `accent`, `success`, `warning`, `danger`, `surface`
- Tipografías: Inter (sans), Playfair Display (serif), Outfit (display)
- Animaciones: `fade-in`, `fade-in-up`, `slide-in-*`, `scale-in`, `float`, `shimmer`
- Clases utilitarias: `glass`, `glass-dark`, `gradient-brand`, `text-gradient`

**Clases de componentes CSS:** `.btn`, `.btn-primary/secondary/danger/ghost`, `.badge`, `.stat-card`, `.event-card`, `.admin-table`, `.form-input`, `.progress-bar`

**Layouts Blade:**
- `layouts/guest` — páginas públicas (auth, invitaciones)
- `layouts/app` — área autenticada
- `layouts/admin` — panel de administración

---

## Autenticación

Implementada con **Laravel Breeze** (stack Blade). Incluye:
- Registro / Login / Logout
- Verificación de email
- Recuperación y reseteo de contraseña
- Confirmación de contraseña para acciones sensibles
- Edición de perfil y eliminación de cuenta

---

## Variables de entorno relevantes

| Variable | Descripción |
|----------|-------------|
| `APP_URL` | URL base (usada para generar `invitation_url`) |
| `DB_CONNECTION` | `sqlite` (dev) o `mysql` (prod) |
| `MAIL_MAILER` | Driver de correo (usar `smtp` en producción) |
| `MAIL_FROM_ADDRESS` | Dirección remitente de las invitaciones |
| `SESSION_DRIVER` | `database` por defecto |

---

## Configuración de Vite / Tailwind

El proyecto usa **Tailwind CSS v4** integrado como plugin de Vite, no via PostCSS:

- [vite.config.js](vite.config.js) registra `tailwindcss()` de `@tailwindcss/vite`.
- [postcss.config.js](postcss.config.js) queda vacío (`autoprefixer` no es necesario con v4).
- Toda la configuración de diseño vive en [resources/css/app.css](resources/css/app.css) via `@theme`.
- Los paths de escaneo de clases se declaran en el CSS con `@source`.

---

## Base de datos

Por defecto usa **SQLite** en desarrollo. Para cambiar a MySQL, editar `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_invitaciones
DB_USERNAME=root
DB_PASSWORD=secret
```

El seeder `TemplateSeeder` carga las plantillas iniciales:

```bash
php artisan db:seed --class=TemplateSeeder
```
