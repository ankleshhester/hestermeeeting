<?php

namespace App\Filament\Resources\ConferenceRooms;

use App\Filament\Resources\ConferenceRooms\Pages\CreateConferenceRoom;
use App\Filament\Resources\ConferenceRooms\Pages\EditConferenceRoom;
use App\Filament\Resources\ConferenceRooms\Pages\ListConferenceRooms;
use App\Filament\Resources\ConferenceRooms\Pages\ViewConferenceRoom;
use App\Filament\Resources\ConferenceRooms\Schemas\ConferenceRoomForm;
use App\Filament\Resources\ConferenceRooms\Schemas\ConferenceRoomInfolist;
use App\Filament\Resources\ConferenceRooms\Tables\ConferenceRoomsTable;
use App\Models\ConferenceRoom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConferenceRoomResource extends Resource
{
    protected static ?string $model = ConferenceRoom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $modelLabel = 'Conference Room';
    protected static ?string $pluralModelLabel = 'Conference Rooms';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ConferenceRoomForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConferenceRoomInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConferenceRoomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConferenceRooms::route('/'),
            'create' => CreateConferenceRoom::route('/create'),
            'view' => ViewConferenceRoom::route('/{record}'),
            'edit' => EditConferenceRoom::route('/{record}/edit'),
        ];
    }
}
