<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'validation_rules',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'validation_rules' => 'array',
    ];

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Accessors
    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    // Mutators
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = match ($this->type) {
            'boolean' => $value ? '1' : '0',
            'array', 'json' => json_encode($value),
            default => (string) $value,
        };
    }

    // Helper Methods
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    public static function getGroup($group)
    {
        return self::byGroup($group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
