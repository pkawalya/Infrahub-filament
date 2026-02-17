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
     * Get CSS gradient strings for swatch cards.
     */
    public static function gradients(): array
    {
        return [
            'amber' => 'linear-gradient(135deg, #f59e0b, #d97706, #b45309)',
            'indigo' => 'linear-gradient(135deg, #818cf8, #6366f1, #4338ca)',
            'violet' => 'linear-gradient(135deg, #a78bfa, #8b5cf6, #7c3aed)',
            'rose' => 'linear-gradient(135deg, #fb7185, #f43f5e, #e11d48)',
            'emerald' => 'linear-gradient(135deg, #34d399, #10b981, #059669)',
            'cyan' => 'linear-gradient(135deg, #22d3ee, #06b6d4, #0891b2)',
            'pink' => 'linear-gradient(135deg, #f472b6, #ec4899, #db2777)',
            'orange' => 'linear-gradient(135deg, #fb923c, #f97316, #ea580c)',
            'blue' => 'linear-gradient(135deg, #60a5fa, #3b82f6, #2563eb)',
            'green' => 'linear-gradient(135deg, #4ade80, #22c55e, #16a34a)',
            'teal' => 'linear-gradient(135deg, #2dd4bf, #14b8a6, #0d9488)',
            'sky' => 'linear-gradient(135deg, #38bdf8, #0ea5e9, #0284c7)',
            'purple' => 'linear-gradient(135deg, #c084fc, #a855f7, #9333ea)',
            'red' => 'linear-gradient(135deg, #f87171, #ef4444, #dc2626)',
            'yellow' => 'linear-gradient(135deg, #facc15, #eab308, #ca8a04)',
            'lime' => 'linear-gradient(135deg, #a3e635, #84cc16, #65a30d)',
            'slate' => 'linear-gradient(135deg, #94a3b8, #64748b, #475569)',
            'gray' => 'linear-gradient(135deg, #9ca3af, #6b7280, #4b5563)',
            'zinc' => 'linear-gradient(135deg, #a1a1aa, #71717a, #52525b)',
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