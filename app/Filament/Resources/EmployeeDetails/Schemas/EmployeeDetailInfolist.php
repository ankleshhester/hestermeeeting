<?php

namespace App\Filament\Resources\EmployeeDetails\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmployeeDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('employee_code'),
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('mobile'),
                TextEntry::make('extension'),
                TextEntry::make('monthly_cost')
                    ->numeric(),
                TextEntry::make('hourly_cost')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
