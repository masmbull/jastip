<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Try cache first for performance
        $cacheKey = 'setting_' . $key;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey, $default);
        }

        $setting = self::where('key', $key)->first();
        $value = $setting ? self::castValue($setting->value, $setting->type) : $default;

        // Cache for 1 hour (only for public settings or all)
        Cache::put($cacheKey, $value, 3600);

        return $value;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general', bool $isPublic = false): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group,
                'is_public' => $isPublic,
            ]
        );

        Cache::forget('setting_' . $key);
    }

    /**
     * Cast a value based on its type.
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'array' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        $keys = self::pluck('key')->toArray();
        foreach ($keys as $key) {
            Cache::forget('setting_' . $key);
        }
    }
}