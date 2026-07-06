<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Template model representing invitation design templates.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $preview_image
 * @property array $configuration
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Template active()
 */
class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_image',
        'configuration',
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
            'configuration' => 'array',
            'active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Get the events that use this template.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /* ------------------------------------------------------------------ */
    /*  Helper Methods                                                     */
    /* ------------------------------------------------------------------ */

    /**
     * Get the default configuration structure for a template.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultConfiguration(): array
    {
        return [
            'colors' => [
                'primary' => '#C9A96E',
                'secondary' => '#1a1a2e',
                'accent' => '#D4AF37',
                'background' => '#0f0f23',
                'text' => '#f5f5f5',
            ],
            'fonts' => [
                'heading' => 'Playfair Display',
                'body' => 'Cormorant Garamond',
            ],
            'sections' => [
                'hero',
                'message',
                'details',
                'countdown',
                'rsvp',
                'location',
                'footer',
            ],
            'animations' => [
                'entrance' => 'fadeInUp',
                'scroll' => true,
            ],
            'style' => 'elegant',
        ];
    }
}
