<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Company (tenant) that owns its own users, events and invitations.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $logo
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Company active()
 */
class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'logo',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Boot                                                               */
    /* ------------------------------------------------------------------ */

    /**
     * Auto-generate a unique slug from the name on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (Company $company): void {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name) . '-' . Str::lower(Str::random(4));
            }
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Get the users that belong to the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the events that belong to the company.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
