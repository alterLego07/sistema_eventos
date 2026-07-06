<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Global scope that restricts queries to the authenticated user's company.
 *
 * Rules:
 *  - No authenticated user (public routes, CLI): no filtering. Public
 *    invitation lookups by token must work without a tenant context.
 *  - super-admin (platform owner, no company): sees every company's data.
 *  - Any other user: results are limited to their own company_id.
 */
class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return;
        }

        if (empty($user->company_id)) {
            return;
        }

        $builder->where($model->getTable() . '.company_id', $user->company_id);
    }
}
