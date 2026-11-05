<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site;

class Visitor extends Model
{
    protected $fillable = [
        'visit_date',
        'name',
        'company',
        'purpose',
        'sign_in',
        'sign_out',
        'site_id',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'sign_in' => 'datetime',
        'sign_out' => 'datetime',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}