<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('user_portal.layout_type', 'header');
        $this->migrator->add('user_portal.theme_mode', 'light');
        $this->migrator->add('user_portal.container_width', 'fluid');
        $this->migrator->add('user_portal.header_fixed', true);
        $this->migrator->add('user_portal.sidebar_fixed', true);
        $this->migrator->add('user_portal.toolbar_display', true);
        $this->migrator->add('user_portal.toolbar_fixed', true);
        $this->migrator->add('user_portal.footer_display', true);
        $this->migrator->add('user_portal.footer_fixed', false);
        $this->migrator->add('user_portal.primary_color', '#009EF7');
        $this->migrator->add('user_portal.font_family', 'Inter');
        $this->migrator->add('user_portal.form_style', 'default');
        $this->migrator->add('user_portal.button_style', 'default');
        $this->migrator->add('user_portal.menu_icon_style', 'duotune');
    }
};
