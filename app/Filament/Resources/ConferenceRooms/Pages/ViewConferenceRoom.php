<?php

namespace App\Filament\Resources\ConferenceRooms\Pages;

use App\Filament\Resources\ConferenceRooms\ConferenceRoomResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConferenceRoom extends ViewRecord
{
    protected static string $resource = ConferenceRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
