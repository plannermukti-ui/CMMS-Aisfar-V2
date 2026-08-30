<?php

namespace App\Filament\Pages\Auth;

use App\Models\Role;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CustomRegister extends BaseRegister
{
    protected string $view = 'filament.pages.auth.custom-register';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getUsernameFormComponent(),
                $this->getFullNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getFullNameFormComponent(): Component
    {
        return TextInput::make('full_name')
            ->label('Full Name')
            ->required()
            ->maxLength(255);
    }

    protected function handleRegistration(array $data): Model
    {
        $userModel = $this->getUserModel();

        $user = $userModel::create([
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 'pending',
        ]);

        // Assign 'Guest' role if exists
        $guestRole = Role::where('name', 'guest')->first();
        if ($guestRole) {
            $user->roles()->attach($guestRole->id);
        }

        return $user;
    }
}
