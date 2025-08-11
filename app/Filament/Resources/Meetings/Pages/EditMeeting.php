<?php

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\MeetingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Meetings\Schemas\MeetingForm;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class EditMeeting extends EditRecord
{
    protected static string $resource = MeetingResource::class;

    protected function getFormSchema(): array
    {
        return MeetingForm::configure($this->getForm())->getComponents();
    }

    public function getTitle(): string
    {
        $conferenceRoomName = Auth::user()
            ?->conferenceRooms()
            ->select('conference_rooms.name')
            ->value('conference_rooms.name');

        return $this->record->conferenceRoom?->name
            ? 'Meeting in ' . $conferenceRoomName
            : 'Edit Meeting';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set end_time to now()
        $data['end_time'] = now();
        $data['status'] = 'completed'; // Update status to completed
        $data['is_active'] = false; // Mark the meeting as inactive

        return $data;
    }

    protected function afterSave(): void
    {
        // Update the pivot table to set is_attending to true for all employees
        $this->record->employees()->sync(
            $this->record->employees->pluck('id')->mapWithKeys(function ($employeeId) {
                return [$employeeId => [
                    'end_time' => now(),
                ]];
            })->toArray()
        );

        // Calculate and save the meeting cost
        $this->record->calculateAndSaveCost();

        // Calculate duration
        $start = Carbon::parse($this->record->start_time);
        $end = Carbon::parse($this->record->end_time);
        $diffInMinutes = $start->diffInMinutes($end);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        $hourLabel = $hours === 1 ? 'hr' : 'hrs';
        $minuteLabel = $minutes === 1 ? 'min' : 'mins';

        $durationText = "{$hours} {$hourLabel} & {$minutes} {$minuteLabel}";

        // Show notification
        Notification::make()
            ->title($this->record->title . ' - Meeting Ended')
            ->body("Total Duration: {$durationText} \n </br> Total Meeting Cost: ₹" . number_format($this->record->cost, 2))
            ->success()
            ->persistent()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();

        foreach ($actions as $key => $action) {
            if ($action->getName() === 'save') {
                // Only show the "End Meeting" button if meeting is active
                if ($this->record->status === 'ongoing') {
                    $action->label('End Meeting');
                } else {
                    // Remove the action if not active
                    unset($actions[$key]);
                }
            }
        }

        return $actions;
    }

}
