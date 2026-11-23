<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    /**
     * Codes that cannot be used because /attendance/{code} already means
     * something else (see routes/web.php).
     */
    public const RESERVED_CODES = [
        'EMPLOYEE',
        'EMERGENCY',
        'RECOGNIZE',
        'REGISTER-FACE',
        'SIGN-IN',
        'SIGN-OUT',
        'GOING-OUT',
        'RETURNING',
        'STATUS',
    ];

    protected $fillable = [
        'name',
        'code',
        'active',
    ];

    /** False for older codes that cannot be a tablet link (e.g. with spaces). */
    public function hasValidCode(): bool
    {
        return preg_match('/^[A-Za-z0-9]+(-[A-Za-z0-9]+)*$/', $this->code) === 1
            && ! in_array(strtoupper($this->code), self::RESERVED_CODES, true);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}