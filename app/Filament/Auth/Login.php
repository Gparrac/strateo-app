<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Support\Facades\Log;

class Login extends BaseAuth
{

    protected bool $stateForm = FALSE; 

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getToggleEmailOrDocument(),
                $this->getFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getToggleEmailOrDocument(): Component
    {
        return Toggle::make('Correo')
                    ->inline()
                    ->default(true)
                    ->reactive()
                    ->afterStateUpdated(function (?bool $state, ?bool $old) {
                        $this->state->set('showLoginForm', !$state);
                    });
    }

    protected function getFormComponent(): Component
    {
        $formComponent = $this->state->get('showLoginForm', true)
            ? $this->getLoginFormComponent()
            : $this->getEmailFormComponentOptional();

        return $formComponent;
    }

    protected function getEmailFormComponentOptional(): Component
    {
        return TextInput::make('email')
            ->label('Correo')
            ->autocomplete()
            ->autofocus();
    }
 
    protected function getLoginFormComponent(): Component 
    {
        return TextInput::make('login')
            ->label('Login')
            ->required()
            ->autocomplete()
            ->autofocus();
    }
}