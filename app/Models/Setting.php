<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'groupe', 'label'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? static::cast($setting->value, $setting->type) : $default;
        });
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(['key' => $key], [
            'value' => is_array($value) ? json_encode($value) : (string) $value,
            'type' => $type,
        ]);
        Cache::forget("setting_{$key}");
    }

    private static function cast(string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }
}
