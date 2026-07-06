<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Adds single-database multi-tenancy to a model:
 *  - registers the {@see CompanyScope} global scope, and
 *  - auto-assigns company_id from the authenticated user on creation.
 *
 * The owning table must have a nullable company_id foreign key.
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function (Model $model): void {
            if (! empty($model->company_id)) {
                return;
            }

            if (Auth::check() && ! empty(Auth::user()->company_id)) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    /**
     * Get the company (tenant) that owns the model.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
