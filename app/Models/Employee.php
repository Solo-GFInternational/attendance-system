<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'name',
        'department',
        'designation',
        'status',
        'site_id',
        'face_descriptor',
        'face_registered_at',
    ];

    protected $casts = [
        'face_descriptor' => 'array',
        'face_registered_at' => 'datetime',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}