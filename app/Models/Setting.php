<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public static function value(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever(static::cacheKey($key), function () use ($key, $default) {
            return static::query()
                ->where('key', $key)
                ->value('value')
                ?? $default;
        });
    }

    /**
     * Get a boolean setting value with type conversion.
     */
    public static function valueBool(string $key, ?bool $default = null): ?bool
    {
        $value = static::value($key);

        if ($value === null) {
            return $default;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        return $parsed ?? $default;
    }

    public static function updateValue(string $key, string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        Cache::forget(static::cacheKey($key));
    }

    /**
     * Update multiple settings atomically within a transaction.
     *
     * @param  array<string, string>  $pairs
     */
    public static function putMany(array $pairs): void
    {
        \DB::transaction(function () use ($pairs) {
            foreach ($pairs as $key => $value) {
                static::updateValue($key, $value);
            }
        });
    }

    /**
     * Flush all setting caches (useful for testing and maintenance).
     */
    public static function flushAll(): void
    {
        $keys = static::query()->pluck('key');

        foreach ($keys as $key) {
            Cache::forget(static::cacheKey($key));
        }
    }

    protected static function cacheKey(string $key): string
    {
        return sprintf('settings.%s', $key);
    }
}
