<?php

namespace App\Filament\Resources\Meetings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;

class MeetingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Meeting Details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->columnSpan(2)
                            ->schema([
                                DateTimePicker::make('start_time')
                                    ->required()
                                    ->default(now())
                                    ->readOnly()
                                    ->label('Start Time'),
                                TextInput::make('title')
                                    ->maxLength(255)
                                    ->placeholder('e.g., Planning Session')
                                    ->label('Title'),

                                TextInput::make('notes')
                                    ->maxLength(500)
                                    ->label('Notes'),

                                Forms\Components\Select::make('conference_room_id')
                                    ->relationship('conferenceRoom', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->visible(fn () => auth()->user()?->hasRole('super_admin')),
                            ]),

                       Select::make('employees')
                            ->multiple()
                            ->label('Attendees')
                            ->placeholder('Select attendees by name, email, or employee code')
                            ->relationship('employees', 'name')
                            ->searchable()
                            ->preload()
                            ->optionsLimit(100)
                            ->visibleOn('create')
                            ->required()
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\EmployeeDetail::query()
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('employee_code', 'like', "%{$search}%")
                                    ->limit(20)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                $employee = \App\Models\EmployeeDetail::find($value);
                                return $employee ? "{$employee->name} ({$employee->employee_code} / {$employee->email})" : null;
                            })
                            ->pivotData([
                                'is_organizer' => false,
                                'is_attending' => true,
                                'notes' => null,
                            ]),

                        // Use a markdown editor for a richer note-taking experience


                        // Automatically set the current user as the organizer
                        Hidden::make('created_by')
                            ->default(fn () => Auth::id())
                            ->dehydrated(true)
                            ->required(),
                    ]),
            ]);
    }
}
