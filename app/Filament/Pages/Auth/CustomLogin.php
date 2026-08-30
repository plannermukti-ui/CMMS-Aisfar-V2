<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    protected static string $layout = 'filament.layouts.auth';

    protected string $view = 'filament.pages.auth.custom-login';
}
