<?php

namespace App\Support;

use App\Models\Company;

class CurrencyHelper
{
    /**
     * Get the current company (from authenticated user).
     */
    protected static function company(): ?Company
    {
        $user = auth()->user();
        return $user?->company;
    }

    /**
     * Get the currency symbol for the current company.
     */
    public static function symbol(): string
    {
        $company = static::company();
        return $company?->currency_symbol ?? $company?->currency ?? '$';
    }

    /**
     * Get the currency code (e.g. USD, UGX) for the current company.
     */
    public static function code(): string
    {
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
            $symbol = $company?->currency_symbol ?? $company?->currency ?? '$';
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
            $symbol = $company?->currency_symbol ?? $company?->currency ?? '$';
            $space = ($company?->currency_space ?? false) ? ' ' : '';
            return $space . $symbol;
        }

        return null;
    }

    /**
     * Format an amount using the current company's currency settings.
     */
    public static function format(float|int|null $amount, int $decimals = 2): string
    {
        if (is_null($amount)) {
            return '—';
        }

        $company = static::company();

        if ($company) {
            return $company->formatCurrency($amount, $decimals);
        }

        return '$' . number_format($amount, $decimals);
    }

    /**
     * Format state for table columns (use as formatStateUsing callback).
     */
    public static function formatter(int $decimals = 2): \Closure
    {
        return fn($state) => static::format($state, $decimals);
    }
}
