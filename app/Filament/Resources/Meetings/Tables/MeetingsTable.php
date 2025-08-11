<?php

namespace App\Filament\Resources\Meetings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MeetingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('conferenceRoom.name')
                    ->label('Conference Room')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function ($record) {
                        $start = Carbon::parse($record->start_time);
                        $end = Carbon::parse($record->end_time);

                        $diffInMinutes = $start->diffInMinutes($end);
                        $hours = floor($diffInMinutes / 60);
                        $minutes = $diffInMinutes % 60;

                        $hourLabel = $hours < 2 ? 'hrs' : 'hrs';
                        $minuteLabel = $minutes < 2 ? 'min' : 'mins';

                        return "{$hours} {$hourLabel} & {$minutes} {$minuteLabel}";

                    }),

                TextColumn::make('cost')
                    ->label('Total Cost')
                    ->money('INR'),

                Tables\Columns\TextColumn::make('employees.name')
                    ->label('Attendees')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('conference_room_id')
                    ->relationship('conferenceRoom', 'name')
                    ->label('Conference Room'),
            ])
            ->recordActions([
                EditAction::make()
                        ->visible(function ($record) {
                    $user = Auth::user();

                    // Superadmin always sees Edit
                    if ($user->hasRole('super_admin')) {
                        return true;
                    }

                    // Regular user: only see Edit if meeting is ongoing
                    if ($user->hasRole('User')) {
                        return $record->status === 'ongoing';
                    }

                    // Otherwise, hidden
                    return false;
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
