<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public ?string $primary_color;

    public ?string $secondary_color;

    public ?string $sidebar_color;

    public ?string $header_color;

    public ?string $body_background_color;

    public ?string $font_family;

    public static function group(): string
    {
        return 'theme';
    }
}
