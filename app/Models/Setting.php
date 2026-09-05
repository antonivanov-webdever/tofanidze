<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const CACHE_KEY = 'site.settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => self::flush());
        static::deleted(fn () => self::flush());
    }

    /**
     * All settings as a flat key => casted value map.
     */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::query()->get()->mapWithKeys(fn (Setting $setting) => [
                $setting->key => $setting->castValue(),
            ])->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::map()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        if (is_array($value)) {
            $type = 'array';
            $value = json_encode(array_values(array_filter($value, fn ($item) => filled($item))));
        }

        if (is_bool($value)) {
            $type = 'boolean';
            $value = $value ? '1' : '0';
        }

        self::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
            'group' => $group,
        ]);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function castValue(): mixed
    {
        return match ($this->type) {
            'array', 'json' => json_decode((string) $this->value, true) ?: [],
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            default => $this->value,
        };
    }
}
