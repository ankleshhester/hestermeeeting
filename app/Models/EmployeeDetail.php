<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;

    protected $table = 'employee_details';

    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'mobile',
        'extension',
        'monthly_cost',
        'hourly_cost',
    ];

    /**
     * Get the meetings associated with the employee.
     */
    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'employee_meeting_pivots')
                    ->withPivot('is_organizer', 'is_attending', 'end_time', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the name of the employee.
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    // public function conferenceRooms()
    // {
    //     return $this->hasMany(ConferenceRoom::class);
    // }
    // Define any relationships, accessors, or mutators here
}
