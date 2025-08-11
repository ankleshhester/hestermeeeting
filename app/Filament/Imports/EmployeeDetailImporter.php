<?php

namespace App\Filament\Imports;

use App\Models\EmployeeDetail;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class EmployeeDetailImporter extends Importer
{
    protected static ?string $model = EmployeeDetail::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('employee_code')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('mobile')
                ->rules(['max:255']),
            ImportColumn::make('extension')
                ->rules(['max:255']),
            ImportColumn::make('monthly_cost')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('hourly_cost')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): EmployeeDetail
    {
        return EmployeeDetail::firstOrNew([
            'employee_code' => $this->data['employee_code'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your employee detail import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
