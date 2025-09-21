<?php
// app/Models/ChefProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class ChefProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'slug',
        'bio',
        'specialties',
        'years_of_experience',
        'cuisines',
        'kitchen_address',
        'kitchen_latitude',
        'kitchen_longitude',
        'delivery_radius_km',
        'minimum_order_amount',
        'delivery_fee',
        'free_delivery_over_amount',
        'free_delivery_threshold',
        'operating_hours',
        'accepts_orders',
        'verification_status',
        'verified_at',
        'verification_notes',
        'rating',
        'total_reviews',
        'total_orders',
        'total_earnings',
        'certifications',
        'gallery_images',
        'bank_name',
        'account_number',
        'account_name',
        'bvn',
        'is_featured',
        'featured_until',
    ];

    protected $casts = [
        'cuisines' => 'array',
        'operating_hours' => 'array',
        'certifications' => 'array',
        'gallery_images' => 'array',
        'kitchen_latitude' => 'decimal:8',
        'kitchen_longitude' => 'decimal:8',
        'minimum_order_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'free_delivery_threshold' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'accepts_orders' => 'boolean',
        'free_delivery_over_amount' => 'boolean',
        'is_featured' => 'boolean',
        'verified_at' => 'datetime',
        'featured_until' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'chef_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'chef_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'chef_id', 'user_id');
    }

    public function payouts()
    {
        return $this->hasMany(ChefPayout::class, 'chef_id', 'user_id');
    }

    // Accessors & Mutators
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ?: Str::slug($this->business_name),
        );
    }

    protected function specialtiesArray(): Attribute
    {
        return Attribute::make(
            get: fn() => is_string($this->specialties)
                ? explode(',', $this->specialties)
                : ($this->specialties ?? []),
        );
    }

    protected function profileImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->gallery_images && count($this->gallery_images) > 0
                ? asset('storage/' . $this->gallery_images[0])
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->business_name) . '&background=f97316&color=fff',
        );
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeAcceptingOrders($query)
    {
        return $query->where('accepts_orders', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')
                    ->orWhere('featured_until', '>', now());
            });
    }

    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm = 50)
    {
        return $query->whereRaw(
            "(6371 * acos(cos(radians(?)) * cos(radians(kitchen_latitude)) * cos(radians(kitchen_longitude) - radians(?)) + sin(radians(?)) * sin(radians(kitchen_latitude)))) <= ?",
            [$latitude, $longitude, $latitude, $radiusKm]
        );
    }

    public function scopeHighRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Helper Methods
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isAcceptingOrders(): bool
    {
        return $this->accepts_orders && $this->isVerified();
    }

    public function isFeatured(): bool
    {
        return $this->is_featured &&
            (!$this->featured_until || $this->featured_until->isFuture());
    }

    public function isOpenNow(): bool
    {
        if (!$this->operating_hours) {
            return false;
        }

        $now = now();
        $dayOfWeek = strtolower($now->format('l'));

        if (!isset($this->operating_hours[$dayOfWeek])) {
            return false;
        }

        $todayHours = $this->operating_hours[$dayOfWeek];

        if (!$todayHours['is_open']) {
            return false;
        }

        $currentTime = $now->format('H:i');
        return $currentTime >= $todayHours['open_time'] &&
            $currentTime <= $todayHours['close_time'];
    }

    public function canDeliverTo($latitude, $longitude): bool
    {
        if (!$this->kitchen_latitude || !$this->kitchen_longitude) {
            return false;
        }

        $distance = $this->calculateDistanceTo($latitude, $longitude);
        return $distance <= $this->delivery_radius_km;
    }

    public function calculateDistanceTo($latitude, $longitude): float
    {
        if (!$this->kitchen_latitude || !$this->kitchen_longitude) {
            return PHP_FLOAT_MAX;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->kitchen_latitude);
        $lonFrom = deg2rad($this->kitchen_longitude);
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

    public function updateRating(): void
    {
        $reviews = $this->reviews();
        $this->update([
            'rating' => $reviews->avg('rating') ?? 0,
            'total_reviews' => $reviews->count(),
        ]);
    }

    public function getDeliveryFeeFor($orderAmount): float
    {
        if (
            $this->free_delivery_over_amount &&
            $this->free_delivery_threshold &&
            $orderAmount >= $this->free_delivery_threshold
        ) {
            return 0;
        }

        return $this->delivery_fee;
    }
}
