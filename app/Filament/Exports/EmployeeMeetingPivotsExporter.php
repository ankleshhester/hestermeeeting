<?php

namespace App\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;

class EmployeeMeetingPivotsExporter extends Exporter
{
    protected static ?string $model = \App\Models\EmployeeMeetingPivot::class; // Replace with your actual model

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employeeDetail.name')
                ->label('Employee Name'),
            ExportColumn::make('meeting.title')
                ->label('Meeting Title'),
            ExportColumn::make('created_at')
                ->label('Started At')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d-m-Y H:i') : 'N/A'),
            ExportColumn::make('end_time')
                ->label('Ended At')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d-m-Y H:i') : 'N/A'),
            ExportColumn::make('duration')
                ->label('Duration')
                ->formatStateUsing(function ($record) {
                    if (!$record->created_at || !$record->end_time) {
                        return 'N/A';
                    }
                    return self::calculateDuration($record->created_at, $record->end_time); // Use self:: to call the static method
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee meeting pivots export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    protected static function calculateDuration($start, $end)
    {
        $diffInMinutes = Carbon::parse($start)->diffInMinutes(Carbon::parse($end));
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        return $hours > 0
            ? "{$hours} hrs {$minutes} mins"
            : "{$minutes} mins";
    }
}
