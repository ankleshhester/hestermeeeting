<?php

namespace App\Filament\Resources\Meetings\Pages;


use App\Filament\Resources\Meetings\Actions\Action;
use App\Filament\Resources\Meetings\MeetingResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action as FilamentAction;
use Illuminate\Support\Facades\Auth;

class CreateMeeting extends CreateRecord
{
    protected static string $resource = MeetingResource::class;

    public function getTitle(): string
    {
        $conferenceRoom = Auth::user()?->conferenceRooms()?->first()?->name;

        return $conferenceRoom
            ? 'Meeting in ' . $conferenceRoom
            : 'Start Meeting';
    }

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();

        // Filter out the 'createAnother' action
        // Filter out the 'createAnother' action
        $actions = array_filter($actions, fn(FilamentAction $action) => $action->getName() !== 'createAnother');

        // Optionally, you can still change the label of the main 'create' action
        foreach ($actions as $action) {
            if ($action->getName() === 'create') {
                $action->label('Start');
            }
        }

        return $actions;
    }

}
