<?php

namespace App\Support;

use App\Models\CdeProject;
use App\Models\Company;

class CurrencyHelper
{
    /**
     * Resolve the current project's currency config.
     * Returns ['symbol' => '...', 'position' => 'before'|'after']
     */
    protected static function resolveConfig(): array
    {
        $project = static::project();

        if ($project) {
            $symbol = $project->getCurrencySymbol();
            $position = $project->getCurrencyPosition();
            return ['symbol' => $symbol, 'position' => $position];
        }

        $company = static::company();
        return [
            'symbol' => $company?->currency_symbol ?? $company?->currency ?? '$',
            'position' => $company?->currency_position ?? 'before',
        ];
    }

    /**
     * Get the current project from route context.
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
                $record = $route->parameter('record');
                if ($record instanceof CdeProject) {
                    $cached = $record;
                    return $cached;
                }
                if (is_numeric($record)) {
                    $cached = CdeProject::find($record);
                    return $cached;
                }
            }
        } catch (\Throwable) {
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
     * Get the currency symbol.
     */
    public static function symbol(): string
    {
        return static::resolveConfig()['symbol'];
    }

    /**
     * Get the currency position ('before' or 'after').
     */
    public static function position(): string
    {
        return static::resolveConfig()['position'];
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
        $config = static::resolveConfig();
        if ($config['position'] === 'before') {
            return $config['symbol'];
        }
        return null;
    }

    /**
     * Get the suffix string for form inputs.
     * Returns the symbol if position is 'after', otherwise null.
     */
    public static function suffix(): ?string
    {
        $config = static::resolveConfig();
        if ($config['position'] === 'after') {
            return $config['symbol'];
        }
        return null;
    }

    /**
     * Format an amount with correct position.
     * Examples: $1,500.00 or 1,500 UGX
     */
    public static function format(float|int|null $amount, int $decimals = 2): string
    {
        if (is_null($amount)) {
            return '—';
        }

        $config = static::resolveConfig();
        $formatted = number_format($amount, $decimals);

        return $config['position'] === 'after'
            ? $formatted . ' ' . $config['symbol']
            : $config['symbol'] . $formatted;
    }

    /**
     * Format state for table columns (use as formatStateUsing callback).
     */
    public static function formatter(int $decimals = 2): \Closure
    {
        return fn($state) => static::format($state, $decimals);
    }
}
