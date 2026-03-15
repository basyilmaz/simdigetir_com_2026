<?php

namespace Modules\Settings\Models;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use PDO;
use PDOException;
use Throwable;

class Setting extends Model
{
    private const CACHE_PREFIX = 'settings:';
    private static ?bool $databaseReachable = null;

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
        if (! static::isDatabaseReachable()) {
            return $default;
        }

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

    private static function isDatabaseReachable(): bool
    {
        if (static::$databaseReachable !== null) {
            return static::$databaseReachable;
        }

        $defaultConnection = (string) config('database.default');
        $connection = (array) config("database.connections.$defaultConnection", []);
        $driver = (string) ($connection['driver'] ?? '');

        if ($driver === 'sqlite') {
            static::$databaseReachable = true;

            return true;
        }

        $timeoutSeconds = max((float) env('DB_RUNTIME_PROBE_TIMEOUT', 0.3), 0.1);
        $host = $connection['host'] ?? null;
        if (is_array($host)) {
            $host = $host[0] ?? null;
        }

        if (is_string($host) && $host !== '') {
            $port = (int) ($connection['port'] ?? ($driver === 'pgsql' ? 5432 : ($driver === 'sqlsrv' ? 1433 : 3306)));
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeoutSeconds);
            if ($socket === false) {
                static::$databaseReachable = false;

                return false;
            }
            fclose($socket);
        }

        if (! in_array($driver, ['mysql', 'pgsql', 'sqlsrv'], true)) {
            static::$databaseReachable = true;

            return true;
        }

        $host = (string) ($host ?: '127.0.0.1');
        $port = (int) ($connection['port'] ?? ($driver === 'pgsql' ? 5432 : ($driver === 'sqlsrv' ? 1433 : 3306)));
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        if ($driver === 'mysql') {
            $database = $database !== '' ? $database : 'information_schema';
            $charset = (string) ($connection['charset'] ?? 'utf8mb4');
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
        } elseif ($driver === 'pgsql') {
            $database = $database !== '' ? $database : 'postgres';
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
        } else {
            $database = $database !== '' ? $database : 'master';
            $dsn = "sqlsrv:Server={$host},{$port};Database={$database}";
        }

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => max((int) ceil($timeoutSeconds), 1),
            ]);
            $pdo = null;
            static::$databaseReachable = true;
        } catch (Throwable) {
            static::$databaseReachable = false;
        }

        return static::$databaseReachable;
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
