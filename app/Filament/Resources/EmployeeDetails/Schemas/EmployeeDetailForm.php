<?php

namespace App\Filament\Resources\EmployeeDetails\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('mobile'),
                TextInput::make('extension'),
                TextInput::make('monthly_cost')
                    ->numeric(),
                TextInput::make('hourly_cost')
                    ->required()
                    ->numeric(),
            ]);
    }
}
