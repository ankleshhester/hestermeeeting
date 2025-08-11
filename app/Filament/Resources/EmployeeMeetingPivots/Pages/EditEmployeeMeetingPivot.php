<?php

namespace App\Filament\Resources\EmployeeMeetingPivots\Pages;

use App\Filament\Resources\EmployeeMeetingPivots\EmployeeMeetingPivotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeMeetingPivot extends EditRecord
{
    protected static string $resource = EmployeeMeetingPivotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
