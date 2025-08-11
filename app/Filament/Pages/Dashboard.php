<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Meeting;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public $start_date;
    public $end_date;

    public function mount()
    {
        $this->start_date = now()->subMonth()->toDateString();
        $this->end_date = Carbon::now();
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Filter Meetings')
                ->description('Filter meetings by date range and status.')
                ->schema([
                    // Use a Grid component for a clean 3-column layout.
                    Grid::make()
                        ->columns(3)
                        ->schema([
                            // Date pickers for the date range
                            DatePicker::make('start_date')
                                ->label('Start Date')
                                ->native(false)
                                ->placeholder('Select start date')
                                ->live(debounce: 200)
                                ->default(Carbon::now()->subMonth()),

                            DatePicker::make('end_date')
                                ->label('End Date')
                                ->native(false)
                                ->placeholder('Select end date')
                                ->live(debounce: 200)
                                ->default(Carbon::now()),

                            // // Select field for the meeting status
                            // Select::make('status')
                            //     ->label('Status')
                            //     ->options(Meeting::getStatuses() ?? ['scheduled' => 'Scheduled', 'completed' => 'Completed'])
                            //     ->placeholder('All Statuses')
                            //     ->live(debounce: 200),
                        ]),
                ])
                ->columnSpanFull(),
            ]);
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && $user->hasRole('super_admin'); // Adjust role name as needed
    }
}
