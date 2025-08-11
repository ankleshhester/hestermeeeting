<?php

namespace App\Filament\Resources\EmployeeMeetingPivots\Pages;

use App\Filament\Resources\EmployeeMeetingPivots\EmployeeMeetingPivotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeMeetingPivots extends ListRecords
{
    protected static string $resource = EmployeeMeetingPivotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
