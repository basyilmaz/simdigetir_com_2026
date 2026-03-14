<?php

namespace Modules\Settings\Models;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use PDOException;

class Setting extends Model
{
    private const CACHE_PREFIX = 'settings:';

    protected $fillable = [
        'key',
        'value',
        'group',
        'updated_by',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::getCachedValue($key, $default);
    }

    /**
     * Get a setting value by key with cache.
     */
    public static function getCachedValue(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::rememberForever(static::cacheKey($key), function () use ($key, $default) {
                $setting = static::query()->where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (QueryException|PDOException) {
            return $default;
        }
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, mixed $value, string $group = 'general', ?int $userId = null): static
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'updated_by' => $userId,
            ]
        );

        static::forgetCache($key);

        return $setting;
    }

    public static function forgetCache(string $key): void
    {
        Cache::forget(static::cacheKey($key));
    }

    private static function cacheKey(string $key): string
    {
        return static::CACHE_PREFIX.$key;
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
