<?php

namespace App\Filament\Widgets;

use App\Models\Meeting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Log;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder; // Import Builder for type hinting in when()
use Illuminate\Support\Number;

class MeetingStatsWidget extends BaseWidget
{
    // Use the InteractsWithPageFilters trait to access filters defined on the page.
    use InteractsWithPageFilters;

    // short the widget before participation table display
    protected int | string | array $columnSpan = 'full';


    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        // Access filters from the page using $this->pageFilters.
        $startDate = $this->pageFilters['start_date'] ?? null;
        $endDate = $this->pageFilters['end_date'] ?? null;
        $status = $this->pageFilters['status'] ?? null;

        // Start with a base query for the Meeting model.
        $query = Meeting::query();

        try {
            // Apply all filters to the single query instance.
            $query->when($startDate, fn (Builder $query) => $query->whereDate('start_time', '>=', $startDate))
                  ->when($endDate, fn (Builder $query) => $query->whereDate('start_time', '<=', $endDate))
                  ->when($status, fn (Builder $query) => $query->where('status', $status));

            // Perform a single query to get all necessary statistics.
            $stats = $query->whereNotNull('start_time')
                           ->whereNotNull('end_time')
                           ->selectRaw('COUNT(*) as total_meetings')
                           ->selectRaw('SUM(cost) as total_cost')
                           ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration_minutes')
                           ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as total_duration_minutes')
                           ->first();

            // Extract values from the single result.
            $totalMeetings = $stats->total_meetings ?? 0;
            $totalCost = $stats->total_cost ?? 0;
            $avgDurationMinutes = $stats->avg_duration_minutes ?? 0;
            $totalDurationMinutes = $stats->total_duration_minutes ?? 0;

            // Helper function to format duration in minutes to a human-readable string.
            $formatDuration = function ($minutes) {
                if ($minutes <= 0) {
                    return '0 minutes';
                }
                $hours = floor($minutes / 60);
                $minutes = $minutes % 60;
                $parts = [];
                if ($hours > 0) {
                    $parts[] = Number::format($hours) . 'hrs';
                }
                if ($minutes > 0) {
                    $parts[] = Number::format($minutes) . 'mins';
                }
                return implode(' & ', $parts);
            };

            return [
                Stat::make('Total Meetings', Number::format($totalMeetings))
                    ->description('All meetings in the system')
                    ->color('primary')
                    ->extraAttributes(['class' => 'text-xs']),
                Stat::make('Total Cost', '₹' . Number::format($totalCost, 2))
                    ->description('Sum of all meeting costs')
                    ->color('success')
                    ->extraAttributes(['class' => 'text-xs']),
                Stat::make('Average Duration', $formatDuration($avgDurationMinutes))
                    ->description('Average meeting duration')
                    ->color('info')
                    ->extraAttributes(['class' => 'text-xs']),
                Stat::make('Total Duration', $formatDuration($totalDurationMinutes))
                    ->description('Total time spent in all meetings')
                    ->color('warning')
                    ->extraAttributes(['class' => 'text-xs']),
            ];

        } catch (\Exception $e) {
            Log::error('Error in MeetingStatsWidget: ' . $e->getMessage());
            return [
                Stat::make('Error', 'Unable to load stats')
                    ->description('Check logs for details')
                    ->color('danger'),
            ];
        }
    }
}
