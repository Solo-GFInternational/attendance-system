<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'sign_in_photo',
        'remarks',
    ];


    protected $casts = [
        'attendance_date' => 'date',
    ];


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE
    |--------------------------------------------------------------------------
    */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }


    /*
    |--------------------------------------------------------------------------
    | OUTINGS
    |--------------------------------------------------------------------------
    */

    public function outings(): HasMany
    {
        return $this->hasMany(AttendanceOuting::class);
    }
}