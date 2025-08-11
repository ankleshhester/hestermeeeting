<?php

namespace App\Filament\Resources\EmployeeMeetingPivots\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmployeeMeetingPivotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_detail_id')
                    ->required()
                    ->numeric(),
                TextInput::make('meeting_id')
                    ->required()
                    ->numeric(),
                Toggle::make('is_organizer')
                    ->required(),
                Toggle::make('is_attending')
                    ->required(),
                DateTimePicker::make('end_time'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
