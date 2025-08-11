<?php

namespace App\Filament\Resources\ConferenceRooms\Pages;

use App\Filament\Resources\ConferenceRooms\ConferenceRoomResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditConferenceRoom extends EditRecord
{
    protected static string $resource = ConferenceRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
