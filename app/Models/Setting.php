<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    /**
     * Boot the model and register event listeners.
     */
    protected static function booted(): void
    {
        static::updated(function () {
            Cache::forget('site_settings');
        });

        static::deleted(function () {
            Cache::forget('site_settings');
        });
    }

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->value;
    }

    /**
     * Get the value attribute with type casting.
     */
    public function getValueAttribute($value): mixed
    {
        return match ($this->type) {
            'integer' => (int) $value,
            'boolean' => (bool) $value,
            'array' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Set the value attribute with type serialization.
     */
    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = match ($this->type) {
            'array' => json_encode($value),
            default => $value,
        };
    }
}
