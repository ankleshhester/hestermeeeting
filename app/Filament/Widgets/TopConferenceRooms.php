<?php

namespace App\Filament\Widgets;

use App\Models\ConferenceRoom;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Exports\ConferenceRoomsExport;

class TopConferenceRooms extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Conference Rooms (by Duration)';

    protected function getTableQuery(): Builder
    {
        $startDate = $this->pageFilters['start_date'] ?? null;
        $endDate   = $this->pageFilters['end_date'] ?? null;

        return ConferenceRoom::query()
            ->select('conference_rooms.id', 'conference_rooms.name')
            ->selectRaw('
                SUM(TIMESTAMPDIFF(SECOND, m.created_at, COALESCE(m.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) AS total_duration_seconds,
                AVG(TIMESTAMPDIFF(SECOND, m.created_at, COALESCE(m.end_time, CONVERT_TZ(NOW(), "+00:00", "+05:30")))) AS avg_duration_seconds,
                COUNT(m.id) AS total_meetings
            ')
            ->join('meetings as m', 'conference_rooms.id', '=', 'm.conference_room_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('m.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('m.created_at', '<=', $endDate))
            ->groupBy('conference_rooms.id', 'conference_rooms.name');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Conference Room')
                ->searchable(),

            Tables\Columns\TextColumn::make('total_meetings')
                ->label('Total Meetings')
                ->sortable(),

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
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exports([
                    ConferenceRoomsExport::make('Export Rooms'),
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
