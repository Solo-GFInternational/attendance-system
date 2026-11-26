<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * System-wide key / value settings. Values are cached because some are read
 * on every page (the festive theme).
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'updated_by'];

    protected $casts = ['updated_by' => 'integer'];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Never throws: if the settings table is missing (not migrated yet) or the
     * database is unreachable, the default is returned so pages still load.
     */
    public static function value(string $key, ?string $default = null): ?string
    {
        try {
            return Cache::rememberForever(self::cacheKey($key), fn () => static::find($key)?->value) ?? $default;
        } catch (Throwable) {
            return $default;
        }
    }

    public static function put(string $key, ?string $value, ?User $by = null): self
    {
        $setting = static::updateOrCreate(['key' => $key], ['value' => $value, 'updated_by' => $by?->id]);

        Cache::forget(self::cacheKey($key));

        return $setting;
    }

    private static function cacheKey(string $key): string
    {
        return "setting:{$key}";
    }
}
