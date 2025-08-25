<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'conference_room_id',
        'start_time',
        'end_time',
        'title',
        'notes',
        'cost',
        'status',
        'created_by',
        'is_active',
    ];

    public static function getStatuses()
    {
        return [
            'scheduled' => 'Scheduled',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Get the conference room associated with the meeting.
     */
    public function conferenceRoom()
    {
        return $this->belongsTo(ConferenceRoom::class);
    }

    protected static function booted()
    {
        static::creating(function ($meeting) {
            $meeting->start_time = now(); // same as created_at
        });
    }

    /**
     * Get the employees associated with the meeting.
     */
    public function employees()
    {
        return $this->belongsToMany(EmployeeDetail::class, 'employee_meeting_pivots')
                    ->withPivot('is_organizer', 'is_attending', 'end_time', 'notes')
                    ->withTimestamps();
    }

    public function employeeMeetingPivots()
    {
        return $this->hasMany(EmployeeMeetingPivot::class, 'meeting_id');
    }

    public function calculateAndSaveCost()
    {
        $this->load('employeeMeetingPivots.employee');
        $totalCost = $this->employeeMeetingPivots->sum(function ($pivot) {
            if (!$pivot->end_time || !$pivot->employee || !$pivot->employee->hourly_cost) {

                return 0;
            }

            $start = \Carbon\Carbon::parse($pivot->created_at);
            $end = \Carbon\Carbon::parse($pivot->end_time);

            if ($end->lessThan($start)) {

                return 0;
            }

            $durationInHours = $start->diffInMinutes($end) / 60;
            $cost = $pivot->employee->hourly_cost * $durationInHours;

            return $cost;
        });


        $this->update(['cost' => $totalCost]);

        return $totalCost;
    }

}
