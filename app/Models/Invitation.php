<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Invitation model representing a guest invitation for an event.
 *
 * @property int $id
 * @property int|null $company_id
 * @property int $event_id
 * @property string $token
 * @property string $guest_name
 * @property string|null $phone
 * @property string|null $email
 * @property int|null $table_number
 * @property int $allowed_guests
 * @property bool $confirmed
 * @property int $confirmed_guests
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property string|null $dietary_restrictions
 * @property string|null $message
 * @property string|null $song_suggestion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read string $invitation_url
 * @property-read string $status_label
 *
 * @method static Builder|Invitation confirmed()
 * @method static Builder|Invitation pending()
 */
class Invitation extends Model
{
    use BelongsToCompany;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'event_id',
        'token',
        'guest_name',
        'phone',
        'email',
        'table_number',
        'allowed_guests',
        'invited',
        'confirmed',
        'confirmed_guests',
        'confirmed_at',
        'dietary_restrictions',
        'message',
        'song_suggestion',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invited' => 'boolean',
            'confirmed' => 'boolean',
            'confirmed_at' => 'datetime',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Boot                                                               */
    /* ------------------------------------------------------------------ */

    /**
     * The "booted" method of the model.
     *
     * Auto-generates a unique 10-character alphanumeric token on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation): void {
            // Inherit tenant from the parent event when not already resolved
            // (e.g. created via Tinker, seeders, o super-admin sin empresa propia).
            // El controller siempre debe pasar company_id explícitamente para
            // evitar este SELECT adicional bajo carga concurrente en MySQL.
            if (empty($invitation->company_id) && ! empty($invitation->event_id)) {
                $invitation->company_id = Event::withoutGlobalScope(CompanyScope::class)
                    ->whereKey($invitation->event_id)
                    ->value('company_id');
            }

            // Generamos el token sin pre-verificar unicidad con SELECT.
            // Si colisiona (62^10 ≈ 839 billones de combinaciones), la restricción
            // UNIQUE lo captura y save() lanza UniqueConstraintViolationException.
            // El controller o el llamador deben manejar ese caso si es crítico.
            if (empty($invitation->token)) {
                $invitation->token = Str::random(10);
            }
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Get the event that this invitation belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Scope a query to only include confirmed invitations.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('confirmed', true);
    }

    /**
     * Scope a query to only include pending (non-confirmed) invitations.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('confirmed_at');
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    /**
     * Get the full invitation URL.
     */
    protected function invitationUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => config('app.url') . '/i/' . $this->token,
        );
    }

    /**
     * Get the human-readable status label.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (): string => match (true) {
                ! $this->confirmed_at => 'Pendiente',
                $this->confirmed => 'Confirmado',
                default => 'No asistirá',
            },
        );
    }
}
