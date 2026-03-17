<?php

use App\Models\Setting;

if (! function_exists('currency_symbol')) {
    /**
     * Get the display symbol for a currency code.
     * Defaults to the currency stored in settings if no code is given.
     */
    function currency_symbol(?string $code = null): string
    {
        $code = $code ?? setting('currency', 'PHP');

        return match ($code) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            default => '₱',
        };
    }
}

if (! function_exists('setting')) {
    /**
     * Get a single setting value by key, with an optional default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('settings')) {
    /**
     * Get all settings as a key => value array.
     */
    function settings(): array
    {
        return Setting::allAsArray();
    }
}

if (! function_exists('service_icons')) {
    /**
     * Return the icon set cycled through for service cards.
     */
    function service_icons(): array
    {
        return [
            'fa-solid fa-car',
            'fa-solid fa-spray-can-sparkles',
            'fa-solid fa-star',
            'fa-solid fa-bolt',
            'fa-solid fa-gem',
            'fa-solid fa-shield-halved',
            'fa-solid fa-wand-magic-sparkles',
            'fa-solid fa-droplet',
        ];
    }
}

if (! function_exists('service_icon')) {
    /**
     * Get a service icon by index (cycles through the list).
     */
    function service_icon(int $index): string
    {
        $icons = service_icons();

        return $icons[$index % count($icons)];
    }
}

if (! function_exists('service_col_class')) {
    /**
     * Responsive Bootstrap column class based on service count.
     */
    function service_col_class(int $count): string
    {
        return match (true) {
            $count >= 4 => 'col-lg-3',
            $count === 3 => 'col-lg-4',
            default      => 'col-lg-6',
        };
    }
}

if (! function_exists('format_price')) {
    /**
     * Format a price with the correct currency symbol.
     */
    function format_price(float|int|null $amount, int $decimals = 0): string
    {
        return currency_symbol() . number_format($amount ?? 0, $decimals);
    }
}
