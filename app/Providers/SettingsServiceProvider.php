<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole() && ! Schema::hasTable('settings')) {
            return;
        }

        try {
            $mailSettings = app(MailSettings::class);
            if ($mailSettings->mail_host) {
                Config::set('mail.default', $mailSettings->mail_mailer ?: 'smtp');
                Config::set('mail.mailers.smtp.host', $mailSettings->mail_host);
                Config::set('mail.mailers.smtp.port', $mailSettings->mail_port);
                Config::set('mail.mailers.smtp.encryption', $mailSettings->mail_encryption);
                Config::set('mail.mailers.smtp.username', $mailSettings->mail_username);
                Config::set('mail.mailers.smtp.password', $mailSettings->mail_password);
                Config::set('mail.from.address', $mailSettings->mail_from_address);
                Config::set('mail.from.name', $mailSettings->mail_from_name);
            }

            $generalSettings = app(GeneralSettings::class);
            if ($generalSettings->site_name) {
                Config::set('app.name', $generalSettings->site_name);
            }
        } catch (\Throwable $th) {
            // Settings table might not exist yet
        }
    }
}
