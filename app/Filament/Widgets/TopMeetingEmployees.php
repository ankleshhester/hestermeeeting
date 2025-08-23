<?php

namespace App\Filament\Widgets;

use App\Models\EmployeeDetail;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Exports\MeetingEmployeesExport;

class TopMeetingEmployees extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Meeting Employees';

    // Make the widget span the full width of the dashboard
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        $startDate = $this->pageFilters['start_date'] ?? null;
        $endDate   = $this->pageFilters['end_date'] ?? null;

        return EmployeeDetail::query()
            ->select('employee_details.id', 'employee_details.name', 'employee_details.hourly_cost')
        ->selectRaw('
            SUM(TIMESTAMPDIFF(SECOND, emp.created_at, COALESCE(emp.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) AS total_duration_seconds,
            AVG(TIMESTAMPDIFF(SECOND, emp.created_at, COALESCE(emp.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) AS avg_duration_seconds,
            (SUM(TIMESTAMPDIFF(SECOND, emp.created_at, COALESCE(emp.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) / 3600) * employee_details.hourly_cost AS total_cost
        ')
        ->join('employee_meeting_pivots as emp', 'employee_details.id', '=', 'emp.employee_detail_id')
        ->when($startDate, fn (Builder $q) => $q->whereDate('emp.created_at', '>=', $startDate))
        ->when($endDate, fn (Builder $q) => $q->whereDate('emp.created_at', '<=', $endDate))
        ->groupBy('employee_details.id', 'employee_details.name', 'employee_details.hourly_cost');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Employee Name')
                ->searchable(),

            Tables\Columns\TextColumn::make('total_duration_seconds')
                ->label('Total Duration')
                ->formatStateUsing(fn ($state) => $this->formatDuration($state))
                ->sortable(query: fn (Builder $query, string $direction) =>
                    $query->orderByRaw('total_duration_seconds ' . $direction)
                ),

            Tables\Columns\TextColumn::make('avg_duration_seconds')
                ->label('Avg Duration')
                ->formatStateUsing(fn ($state) => $this->formatDuration($state))
                ->sortable(query: fn (Builder $query, string $direction) =>
                    $query->orderByRaw('avg_duration_seconds ' . $direction)
                ),

            Tables\Columns\TextColumn::make('total_cost')
                ->label('Total Cost')
                ->money('INR', true)
                ->sortable(query: fn (Builder $query, string $direction) =>
                    $query->orderByRaw('total_cost ' . $direction)
                ),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exports([
                    MeetingEmployeesExport::make('Export Rooms'),
                ]),
        ];
    }

    protected function formatDuration($state): string
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
