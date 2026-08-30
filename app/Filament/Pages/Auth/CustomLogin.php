<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;

class CustomLogin extends BaseLogin
{
    protected static string $layout = 'filament.layouts.auth';

    protected string $view = 'filament.pages.auth.custom-login';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                $this->getEmailFormComponent()
                    ->columnSpan(1),
                $this->getPasswordFormComponent()
                    ->columnSpan(1),
                $this->getRememberFormComponent()
                    ->columnSpanFull(),
            ]);
    }
}
