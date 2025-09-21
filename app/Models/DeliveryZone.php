<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'boundaries',
        'delivery_fee',
        'minimum_order_amount',
        'estimated_delivery_time',
        'operating_hours',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'boundaries' => 'array',
        'delivery_fee' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'operating_hours' => 'array',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    // Helper Methods
    public function containsPoint($latitude, $longitude): bool
    {
        if (!$this->boundaries) return false;

        $polygon = $this->boundaries;
        $x = $longitude;
        $y = $latitude;
        $inside = false;

        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            if ((($polygon[$i][1] > $y) != ($polygon[$j][1] > $y)) &&
                ($x < ($polygon[$j][0] - $polygon[$i][0]) * ($y - $polygon[$i][1]) / ($polygon[$j][1] - $polygon[$i][1]) + $polygon[$i][0])
            ) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    public function isOperatingNow(): bool
    {
        if (!$this->operating_hours) return true;

        $now = now();
        $dayOfWeek = strtolower($now->format('l'));
        $todayHours = $this->operating_hours[$dayOfWeek] ?? null;

        if (!$todayHours || !$todayHours['is_open']) return false;

        $currentTime = $now->format('H:i');
        return $currentTime >= $todayHours['open_time'] &&
            $currentTime <= $todayHours['close_time'];
    }

    public static function findZoneForLocation($latitude, $longitude)
    {
        return self::active()
            ->byPriority()
            ->get()
            ->first(function ($zone) use ($latitude, $longitude) {
                return $zone->containsPoint($latitude, $longitude);
            });
    }
}
