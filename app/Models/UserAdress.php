<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'label',
        'street_address',
        'apartment',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'delivery_instructions',
        'is_default',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_default' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->apartment,
            $this->city,
            $this->state,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeDelivery($query)
    {
        return $query->where('type', 'delivery');
    }

    // Helper Methods
    public function setAsDefault(): void
    {
        // Remove default from other addresses
        $this->user->addresses()->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }

    public function calculateDistanceFrom($latitude, $longitude): float
    {
        if (!$this->latitude || !$this->longitude) {
            return 0;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
