<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmployeeMeetingPivot extends Model
{
    protected $fillable = [
        'employee_detail_id',
        'meeting_id',
        'is_organizer',
        'is_attending',
        'end_time',
        'notes',
        'created_at',
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
}
