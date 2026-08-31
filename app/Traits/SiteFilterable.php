<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait SiteFilterable
{
    /**
     * Apply the current user's site filter to an Eloquent query.
     *
     * If the authenticated user has a site_id set, the query is scoped to that site.
     * If the user has no site_id (All Sites), no filter is applied.
     */
    public function scopeForCurrentUserSite(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user && filled($user->site_id)) {
            $query->where('site_id', $user->site_id);
        }

        return $query;
    }

    /**
     * Get the current user's site filter query scope for a model.
     */
    public static function forCurrentSite(): Builder
    {
        $query = static::query();
        $user = Auth::user();

        if ($user && filled($user->site_id)) {
            $query->where('site_id', $user->site_id);
        }

        return $query;
    }

    /**
     * Get the current user's site name for display in headers.
     */
    public static function getCurrentSiteName(): string
    {
        $user = Auth::user();

        if ($user && filled($user->site_id) && $user->site) {
            return $user->site->site_name;
        }

        return 'All Sites';
    }

    /**
     * Check if the current user is restricted to a specific site.
     */
    public static function isCurrentUserSiteRestricted(): bool
    {
        $user = Auth::user();

        return $user && filled($user->site_id);
    }

    /**
     * Get the current user's site_id value (nullable).
     */
    public static function getCurrentSiteId(): ?string
    {
        $user = Auth::user();

        return ($user && filled($user->site_id)) ? $user->site_id : null;
    }

    /**
     * Apply site filter to a query via a related model's site_id.
     * Use when the model doesn't have site_id directly but joins through a relation.
     *
     * @param  string  $relation  The relationship name on the model
     * @param  string  $foreignColumn  The foreign key on the related table (default: 'site_id')
     */
    public static function applySiteFilterViaRelation(Builder $query, string $relation, string $foreignColumn = 'site_id'): Builder
    {
        $siteId = static::getCurrentSiteId();

        if ($siteId) {
            $query->whereHas($relation, function ($q) use ($siteId, $foreignColumn) {
                $q->where($foreignColumn, $siteId);
            });
        }

        return $query;
    }
}
