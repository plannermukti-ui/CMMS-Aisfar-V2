<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class UserPortalSettings extends Settings
{
    public ?string $layout_type; // 'header' or 'sidebar'

    public ?string $theme_mode; // 'light', 'dark', 'system'

    public ?string $container_width; // 'fluid', 'fixed'

    public ?bool $header_fixed;

    public ?bool $sidebar_fixed;

    public ?bool $toolbar_display;

    public ?bool $toolbar_fixed;

    public ?bool $footer_display;

    public ?bool $footer_fixed;

    public ?string $primary_color;

    public ?string $font_family;

    public ?string $form_style; // 'default' (outline), 'solid', 'transparent'

    public ?string $button_style; // 'default', 'light', 'outline'

    public ?string $menu_icon_style; // 'duotune', 'outline', 'solid'

    public static function group(): string
    {
        return 'user_portal';
    }
}
