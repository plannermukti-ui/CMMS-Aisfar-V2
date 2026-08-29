<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // General
        $this->migrator->add('general.site_name', 'CMMS Application');
        $this->migrator->add('general.site_logo', null);
        $this->migrator->add('general.company_address', null);

        // Mail
        $this->migrator->add('mail.mail_mailer', 'smtp');
        $this->migrator->add('mail.mail_host', 'smtp.mailtrap.io');
        $this->migrator->add('mail.mail_port', 2525);
        $this->migrator->add('mail.mail_username', null);
        $this->migrator->add('mail.mail_password', null);
        $this->migrator->add('mail.mail_encryption', 'tls');
        $this->migrator->add('mail.mail_from_address', 'hello@example.com');
        $this->migrator->add('mail.mail_from_name', 'CMMS');

        // Theme
        $this->migrator->add('theme.primary_color', '#f59e0b');
        $this->migrator->add('theme.secondary_color', '#3b82f6');
        $this->migrator->add('theme.sidebar_color', '#1e293b');
        $this->migrator->add('theme.header_color', '#ffffff');
        $this->migrator->add('theme.body_background_color', '#f3f4f6');
        $this->migrator->add('theme.font_family', 'Inter');
    }
};
