<?php

namespace App\Filament\Resources\EmployeeDetails\Pages;

use App\Filament\Resources\EmployeeDetails\EmployeeDetailResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployeeDetail extends ViewRecord
{
    protected static string $resource = EmployeeDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
