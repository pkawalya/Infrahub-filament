<?php

namespace App\Support;

use Filament\Support\Colors\Color;

class ColorPalette
{
    /**
     * Standard Filament color options with display labels.
     */
    public static function options(): array
    {
        return [
            // Premium / Special Themes
            'amber' => '🔥 Amber Gold',
            'indigo' => '🌌 Indigo Night',
            'violet' => '💎 Royal Violet',
            'rose' => '🌹 Rose Blush',
            'emerald' => '🍀 Emerald Forest',
            'cyan' => '🧊 Frost Cyan',
            'pink' => '🌸 Cherry Blossom',
            'orange' => '🍊 Sunset Orange',

            // Classic Colors
            'blue' => '🔵 Ocean Blue',
            'green' => '🟢 Natural Green',
            'teal' => '🩵 Teal Breeze',
            'sky' => '🩵 Sky Light',
            'purple' => '🟣 Deep Purple',
            'red' => '🔴 Ruby Red',
            'yellow' => '🟡 Sunshine Yellow',
            'lime' => '🟢 Fresh Lime',

            // Neutral Elegance
            'slate' => '🪨 Slate Modern',
            'gray' => '⚪ Steel Gray',
            'zinc' => '🩶 Zinc Minimal',
        ];
    }

    /**
     * Map color names to Filament Color constants.
     */
    public static function map(): array
    {
        return [
            'amber' => Color::Amber,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'rose' => Color::Rose,
            'emerald' => Color::Emerald,
            'cyan' => Color::Cyan,
            'pink' => Color::Pink,
            'orange' => Color::Orange,
            'blue' => Color::Blue,
            'green' => Color::Green,
            'teal' => Color::Teal,
            'sky' => Color::Sky,
            'purple' => Color::Purple,
            'red' => Color::Red,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'slate' => Color::Slate,
            'gray' => Color::Gray,
            'zinc' => Color::Zinc,
        ];
    }

    /**
     * Get hex color for preview swatches.
     */
    public static function hex(): array
    {
        return [
            'amber' => '#f59e0b',
            'indigo' => '#6366f1',
            'violet' => '#8b5cf6',
            'rose' => '#f43f5e',
            'emerald' => '#10b981',
            'cyan' => '#06b6d4',
            'pink' => '#ec4899',
            'orange' => '#f97316',
            'blue' => '#3b82f6',
            'green' => '#22c55e',
            'teal' => '#14b8a6',
            'sky' => '#0ea5e9',
            'purple' => '#a855f7',
            'red' => '#ef4444',
            'yellow' => '#eab308',
            'lime' => '#84cc16',
            'slate' => '#64748b',
            'gray' => '#6b7280',
            'zinc' => '#71717a',
        ];
    }

    /**
     * Get the Filament Color constant for a given color name.
     */
    public static function constantFor(string $colorName)
    {
        $map = static::map();

        return $map[$colorName] ?? Color::Blue;
    }
}