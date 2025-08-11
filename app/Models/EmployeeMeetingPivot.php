<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeeMeetingPivot extends Model
{
    protected $fillable = [
        'employee_id',
        'meeting_id',
        'is_organizer',
        'is_attending',
        'end_time',
        'notes',
    ];

    /**
     * Get the employee associated with the pivot.
     */
    // public function employee()
    // {
    //     return $this->belongsTo(EmployeeDetail::class);
    // }

    public function employee()
    {
        return $this->belongsTo(EmployeeDetail::class, 'employee_detail_id'); // Use correct foreign key
    }

    public function employeeDetail()
    {
        return $this->belongsTo(EmployeeDetail::class);
    }

    /**
     * Get the meeting associated with the pivot.
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function scopeWithTotalMeetingDuration($query, $startDate = null, $endDate = null)
    {
        return $query
            ->join('employee_details', 'employee_meeting_pivots.employee_detail_id', '=', 'employee_details.id')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('employee_meeting_pivots.created_at', [$startDate, $endDate]);
            })
            ->select([
                'employee_meeting_pivots.employee_detail_id',
                'employee_details.name',
                DB::raw('SUM(TIMESTAMPDIFF(SECOND, employee_meeting_pivots.created_at, employee_meeting_pivots.end_time)) as total_duration_seconds')
            ])
            ->groupBy('employee_meeting_pivots.employee_detail_id', 'employee_details.name')
            ->orderByDesc('total_duration_seconds');
    }
}
