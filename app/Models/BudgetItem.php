<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single expense line in an event's budget.
 *
 * @property int $id
 * @property int|null $company_id
 * @property int $event_id
 * @property string $category
 * @property string $concept
 * @property string $estimated_amount
 * @property string|null $actual_amount
 * @property bool $paid
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $vendor
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|BudgetItem paid()
 * @method static Builder|BudgetItem pending()
 */
class BudgetItem extends Model
{
    use BelongsToCompany;

    /**
     * Suggested expense categories (free text, these are only hints for the UI).
     *
     * @var array<int, string>
     */
    public const CATEGORIES = [
        'Salón / Locación',
        'Catering',
        'Bebidas',
        'Decoración',
        'Música / DJ',
        'Fotografía / Video',
        'Invitaciones / Papelería',
        'Vestimenta',
        'Transporte',
        'Mobiliario',
        'Personal / Staff',
        'Otros',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'event_id',
        'category',
        'concept',
        'estimated_amount',
        'actual_amount',
        'paid',
        'paid_at',
        'vendor',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_amount' => 'decimal:2',
            'actual_amount' => 'decimal:2',
            'paid' => 'boolean',
            'paid_at' => 'date',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Get the event this budget item belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Boot                                                               */
    /* ------------------------------------------------------------------ */

    /**
     * Inherit the tenant from the parent event when not resolved from auth
     * (e.g. created by the super-admin, who has no company of their own).
     */
    protected static function booted(): void
    {
        static::creating(function (BudgetItem $item): void {
            if (empty($item->company_id) && ! empty($item->event_id)) {
                $item->company_id = Event::withoutGlobalScope(CompanyScope::class)
                    ->whereKey($item->event_id)
                    ->value('company_id');
            }
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Scope a query to only include paid items.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('paid', true);
    }

    /**
     * Scope a query to only include pending (unpaid) items.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('paid', false);
    }
}
