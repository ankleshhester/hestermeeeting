<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;


class ConferenceRoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'conferenceRooms';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                        TextColumn::make('name')
                            ->label('Conference Room')
                            ->searchable(),

                        BooleanColumn::make('pivot.is_default')
                            ->label('Default'),
                    ])
                    ->headerActions([
                        AttachAction::make()
                            ->label('Attach Conference Room')
                            ->preloadRecordSelect() // ⬅️ Ensures options load
                            ->recordTitleAttribute('name')
                            ->recordSelect(function ($select) {
                                return $select
                                    ->searchable(['name'])
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        return "{$record->name}";
                                    });
                            }) // ⬅️ Shows name as option label
                            ->form([
                                Toggle::make('is_default')
                                    ->label('Is Default')
                                    ->default(false),
                            ]),
                            ]);
    }
}
