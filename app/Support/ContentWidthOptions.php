<?php

namespace App\Support;

use Filament\Support\Enums\Width;

class ContentWidthOptions
{
    /**
     * User-facing content width options with labels.
     * We expose the most practical subset of Filament's Width enum.
     */
    public static function options(): array
    {
        return [
            'xl' => 'Extra Large',
            '2xl' => '2 Extra Large',
            '3xl' => '3 Extra Large',
            '4xl' => '4 Extra Large',
            '5xl' => '5 Extra Large',
            '6xl' => '6 Extra Large',
            '7xl' => '7 Extra Large (Default)',
            'full' => 'Full Width',
        ];
    }

    /**
     * Map internal key to the Filament Width enum case.
     */
    public static function widthEnum(string $key): Width
    {
        return match ($key) {
            'xl' => Width::ExtraLarge,
            '2xl' => Width::TwoExtraLarge,
            '3xl' => Width::ThreeExtraLarge,
            '4xl' => Width::FourExtraLarge,
            '5xl' => Width::FiveExtraLarge,
            '6xl' => Width::SixExtraLarge,
            '7xl' => Width::SevenExtraLarge,
            'full' => Width::Full,
            default => Width::SevenExtraLarge,
        };
    }

    /**
     * Descriptions for each width option for the UI cards.
     */
    public static function descriptions(): array
    {
        return [
            'xl' => 'Narrow — focused reading experience',
            '2xl' => 'Compact — great for forms',
            '3xl' => 'Snug — balanced for most tasks',
            '4xl' => 'Standard — comfortable data view',
            '5xl' => 'Wide — spacious layout',
            '6xl' => 'Extra wide — room for large tables',
            '7xl' => 'Maximum boxed — Filament default',
            'full' => 'Edge-to-edge — uses all screen space',
        ];
    }

    /**
     * Approximate CSS max-width values for the preview UI.
     */
    public static function previewPercentages(): array
    {
        return [
            'xl' => '25%',
            '2xl' => '32%',
            '3xl' => '40%',
            '4xl' => '50%',
            '5xl' => '60%',
            '6xl' => '75%',
            '7xl' => '90%',
            'full' => '100%',
        ];
    }
}
