<?php

namespace App\Filament\Exports;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Maatwebsite\Excel\Excel;
use pxlrbt\FilamentExcel\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\EmployeeDetail;

class MeetingEmployeesExport extends ExcelExport
{
    public static function make(string $name = 'export'): static
    {
        return parent::make($name)
            ->fromTable() // Use export based on table's query
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->select('employee_details.id', 'employee_details.name', 'employee_details.hourly_cost')
                ->selectRaw('
                    SUM(TIMESTAMPDIFF(SECOND, emp.created_at, COALESCE(emp.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) AS total_duration_seconds,
                    AVG(TIMESTAMPDIFF(SECOND, emp.created_at, COALESCE(emp.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) AS avg_duration_seconds,
                    (SUM(TIMESTAMPDIFF(SECOND, emp.created_at, COALESCE(emp.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) / 3600) * employee_details.hourly_cost AS total_cost
                ')

                ->groupBy('employee_details.id', 'employee_details.name', 'employee_details.hourly_cost')
            )
            ->withFilename(fn () => 'meeting_employees_' . now()->format('Y-m-d_H-i-s'))
            ->withWriterType(Excel::XLSX)
            ->withColumns([
                Column::make('name')->heading('Employee Name'),
                Column::make('total_duration_seconds')
                    ->heading('Total Duration')
                    ->formatStateUsing(fn ($state) => self::formatDuration($state)),
                Column::make('avg_duration_seconds')
                    ->heading('Avg Duration')
                    ->formatStateUsing(fn ($state) => self::formatDuration($state)),
                Column::make('total_cost')
                    ->heading('Total Cost (INR)')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2)),
            ]);
    }

    protected static function formatDuration($state): string
    {
        $state = (int) floor($state ?? 0);
        if ($state <= 0) {
            return '0 mins';
        }

        $hours = intdiv($state, 3600);
        $minutes = intdiv($state % 3600, 60);

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . ' ' . ($hours === 1 ? 'hr' : 'hrs');
        }
        if ($minutes > 0) {
            $parts[] = $minutes . ' ' . ($minutes === 1 ? 'min' : 'mins');
        }

        return implode(' ', $parts);
    }
}
