<?php

namespace App\Filament\Resources\Meetings\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\AttachAction;
use Filament\Tables\Actions\Action;


class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public static function configureForm(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('employee_detail_id')
                ->label('Employee')
                ->relationship('employeeDetail', 'name')
                ->searchable()
                ->required(),

            Toggle::make('is_organizer')
                ->label('Is Organizer?')
                ->default(false),

            Toggle::make('is_attending')
                ->label('Is Attending?')
                ->default(true),

            DateTimePicker::make('start_time')
                ->label('Start Time')
                ->default(now())
                ->required(),

            DateTimePicker::make('end_time')
                ->label('End Time')
                ->required()
                ->rules(['after_or_equal:start_time']),

            Textarea::make('notes')
                ->label('Notes')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Employee')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Start Time')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('pivot.end_time')
                    ->label('End Time')
                    ->dateTime(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordTitleAttribute('name')
                    ->label('Add Attendee')
                    ->recordSelect(function ($select) {
                        return $select
                            ->searchable(['employee_code', 'name', 'email'])
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return "{$record->name} ({$record->employee_code}) - {$record->email}";
                            });
                    }),
            ])
            ->filters([
                        // Define any table filters here
            ]);
    }
}
