<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Registration extends Register
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Data Diri')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->minLength(3)
                                ->maxLength(255),
                            TextInput::make('phone_number')
                                ->label('Nomor Telepon')
                                ->required()
                                ->tel()
                                ->maxLength(15),
                            TextInput::make('address')
                                ->label('Alamat')
                                ->required()
                                ->maxLength(255),
                        ]),
                    Wizard\Step::make('Autentikasi')
                        ->schema([
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
                ])
                    ->submitAction(new HtmlString(Blade::render(
                        '<x-filament::button type="submit" size="sm" wire:submit="register">Daftar</x-filament::button>'
                    )))
            ]);
    }

    protected function getFormActions(): array
    {
        return [

        ];
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'customer',
            ]);

            $user->customer()->create([
                'name' => $data['name'],
                'phone_number' => $data['phone_number'],
                'address' => $data['address'],
            ]);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }
}
