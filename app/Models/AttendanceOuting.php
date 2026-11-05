<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceOuting extends Model
{
    protected $fillable = [
        'attendance_id',
        'employee_id',
        'destination',
        'out_time',
        'return_time',
        'duration_minutes',
        'reason',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

