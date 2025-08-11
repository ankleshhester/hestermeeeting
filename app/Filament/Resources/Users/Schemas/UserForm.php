<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),

                 // This is the key part:
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload() // Preloads the options to avoid a separate request
                    ->required(),

                Select::make('conferenceRooms')
                    ->multiple()
                    ->relationship('conferenceRooms', 'name')
                    ->preload()
                    ->label('Conference Rooms'),
            ]);
    }
}
