<?php

namespace App\Support;

use App\Models\CdeProject;
use App\Models\Company;

class CurrencyHelper
{
    /**
     * Resolve the currency symbol using the priority chain:
     * 1. Current project (from route context)
     * 2. Current company (from auth user)
     * 3. Fallback to '$'
     */
    protected static function resolveSymbol(): string
    {
        // 1. Try project-level currency
        $project = static::project();
        if ($project) {
            if ($project->currency_symbol)
                return $project->currency_symbol;
            if ($project->currency && isset(CdeProject::$currencies[$project->currency])) {
                return CdeProject::$currencies[$project->currency]['symbol'];
            }
        }

        // 2. Try company-level currency
        $company = static::company();
        return $company?->currency_symbol ?? $company?->currency ?? '$';
    }

    /**
     * Get the current project from route context.
     * Works within CdeProjectResource pages where the record is a CdeProject.
     */
    protected static function project(): ?CdeProject
    {
        static $cached = null;
        static $resolved = false;

        if ($resolved)
            return $cached;
        $resolved = true;

        try {
            $route = request()->route();
            if ($route) {
                // Try the 'record' parameter (standard Filament resource pages)
                $record = $route->parameter('record');
                if ($record instanceof CdeProject) {
                    $cached = $record;
                    return $cached;
                }
                // If record is an ID, try to resolve it
                if (is_numeric($record)) {
                    $cached = CdeProject::find($record);
                    return $cached;
                }
            }
        } catch (\Throwable) {
            // Silently fail — we're outside a project context
        }

        return null;
    }

    /**
     * Get the current company (from authenticated user).
     */
    protected static function company(): ?Company
    {
        $user = auth()->user();
        return $user?->company;
    }

    /**
     * Get the currency symbol (project → company → $).
     */
    public static function symbol(): string
    {
        return static::resolveSymbol();
    }

    /**
     * Get the currency code (e.g. USD, UGX).
     */
    public static function code(): string
    {
        $project = static::project();
        if ($project?->currency)
            return $project->currency;
        return static::company()?->currency ?? 'USD';
    }

    /**
     * Get the prefix string for form inputs.
     * Returns the symbol if position is 'before', otherwise null.
     */
    public static function prefix(): ?string
    {
        $company = static::company();
        $position = $company?->currency_position ?? 'before';

        if ($position === 'before') {
            $symbol = static::resolveSymbol();
            $space = ($company?->currency_space ?? false) ? ' ' : '';
            return $symbol . $space;
        }

        return null;
    }

    /**
     * Get the suffix string for form inputs.
     * Returns the symbol if position is 'after', otherwise null.
     */
    public static function suffix(): ?string
    {
        $company = static::company();
        $position = $company?->currency_position ?? 'before';

        if ($position === 'after') {
            $symbol = static::resolveSymbol();
            $space = ($company?->currency_space ?? false) ? ' ' : '';
            return $space . $symbol;
        }

        return null;
    }

    /**
     * Format an amount using the resolved currency.
     */
    public static function format(float|int|null $amount, int $decimals = 2): string
    {
        if (is_null($amount)) {
            return '—';
        }

        $symbol = static::resolveSymbol();
        return $symbol . number_format($amount, $decimals);
    }

    /**
     * Format state for table columns (use as formatStateUsing callback).
     */
    public static function formatter(int $decimals = 2): \Closure
    {
        return fn($state) => static::format($state, $decimals);
    }
}
