<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Event model representing an invitation event.
 *
 * @property int $id
 * @property int|null $company_id
 * @property int $user_id
 * @property int|null $template_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $event_date
 * @property string $event_time
 * @property string|null $location
 * @property string|null $location_url
 * @property string|null $cover_image
 * @property array|null $settings
 * @property array|null $template_config
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read string $formatted_date
 * @property-read string $formatted_time
 * @property-read int $confirmed_count
 * @property-read int $total_expected
 * @property-read int $pending_count
 * @property-read float $confirmation_rate
 * @property-read array $merged_template_config
 *
 * @method static Builder|Event published()
 * @method static Builder|Event upcoming()
 * @method static Builder|Event draft()
 */
class Event extends Model
{
    use BelongsToCompany;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'template_id',
        'name',
        'slug',
        'description',
        'event_date',
        'event_time',
        'location',
        'location_url',
        'cover_image',
        'settings',
        'template_config',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'settings' => 'array',
            'template_config' => 'array',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Get the user that owns the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template associated with the event.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the invitations for the event.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Scope a query to only include published events.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include upcoming events (event_date >= today).
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    /**
     * Scope a query to only include draft events.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    /**
     * Get the formatted date in Spanish (e.g. "Sábado 15 de Marzo, 2025").
     */
    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $days = [
                    'Sunday' => 'Domingo',
                    'Monday' => 'Lunes',
                    'Tuesday' => 'Martes',
                    'Wednesday' => 'Miércoles',
                    'Thursday' => 'Jueves',
                    'Friday' => 'Viernes',
                    'Saturday' => 'Sábado',
                ];

                $months = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                    4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                    10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                ];

                /** @var Carbon $date */
                $date = $this->event_date;

                $dayName = $days[$date->format('l')] ?? $date->format('l');
                $day = $date->format('j');
                $month = $months[(int) $date->format('n')] ?? $date->format('F');
                $year = $date->format('Y');

                return "{$dayName} {$day} de {$month}, {$year}";
            },
        );
    }

    /**
     * Get the formatted time (e.g. "18:00 hrs").
     */
    protected function formattedTime(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Carbon::parse($this->event_time)->format('H:i') . ' hrs',
        );
    }

    /**
     * Get the count of confirmed invitations.
     */
    protected function confirmedCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->invitations()->where('confirmed', true)->count(),
        );
    }

    /**
     * Get the total expected guests (sum of confirmed_guests from confirmed invitations).
     */
    protected function totalExpected(): Attribute
    {
        return Attribute::make(
            get: fn (): int => (int) $this->invitations()->where('confirmed', true)->sum('confirmed_guests'),
        );
    }

    /**
     * Get the count of pending (non-confirmed) invitations.
     */
    protected function pendingCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => $this->invitations()->where('confirmed', false)->count(),
        );
    }

    /**
     * Get the confirmation rate as a percentage.
     */
    protected function confirmationRate(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $total = $this->invitations()->count();

                if ($total === 0) {
                    return 0.0;
                }

                $confirmed = $this->invitations()->where('confirmed', true)->count();

                return round(($confirmed / $total) * 100, 2);
            },
        );
    }

    /**
     * Get the merged template configuration (base template config + event overrides).
     *
     * @return array<string, mixed>
     */
    protected function mergedTemplateConfig(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $baseConfig = $this->template?->configuration ?? [];
                $eventOverrides = $this->template_config ?? [];

                return array_replace_recursive($baseConfig, $eventOverrides);
            },
        );
    }
}
