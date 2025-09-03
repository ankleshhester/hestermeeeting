<?php

namespace App\Filament\Resources\EmployeeMeetingPivots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Exports\EmployeeMeetingPivotsExporter;
use Filament\Actions\ExportAction;

class EmployeeMeetingPivotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employeeDetail.name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('meeting.title')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Started At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function ($record) {
                        if (!$record->created_at || !$record->end_time) {
                            return '-';
                        }

                        $start = Carbon::parse($record->created_at);
                        $end = Carbon::parse($record->end_time);

                        $diffInMinutes = $start->diffInMinutes($end);
                        $hours = floor($diffInMinutes / 60);
                        $minutes = $diffInMinutes % 60;

                        $hourLabel = $hours === 1 ? 'hr' : 'hrs';
                        $minuteLabel = $minutes === 1 ? 'min' : 'mins';

                        return "{$hours} {$hourLabel} & {$minutes} {$minuteLabel}";
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label('Export Excel')
                    ->exporter  (EmployeeMeetingPivotsExporter::class)
            ]);

    if (! function_exists('calculateDuration')) {
        function calculateDuration($start, $end)
        {
            $diffInMinutes = Carbon::parse($start)->diffInMinutes(Carbon::parse($end));
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;

            return $hours > 0
                ? "{$hours} hrs {$minutes} mins"
                : "{$minutes} mins";
        }
    }
    }

}
