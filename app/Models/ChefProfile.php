<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChefProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'slug',
        'bio',
        'years_of_experience',
        'kitchen_address',
        'is_online',        // Now matches DB
        'minimum_order',    // Now matches DB
        'delivery_radius_km',
        'operating_hours',
        'bank_name',
        'account_number',
        'account_name',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'is_online' => 'boolean',
        'minimum_order' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'chef_cuisine');
    }

    public function deliveryZones()
    {
        return $this->hasMany(DeliveryZone::class);
    }

    // Auto-generate Slug
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($profile) {
            if ($profile->isDirty('business_name') && empty($profile->slug)) {
                $profile->slug = Str::slug($profile->business_name);
            }
        });
    }

    // Helper
    public function isOpenNow()
    {
        if (!$this->is_online) return false;

        $today = strtolower(now()->format('l'));
        $schedule = $this->operating_hours[$today] ?? null;

        if (!$schedule || ($schedule['closed'] ?? true)) {
            return false;
        }

        $now = now()->format('H:i');
        return $now >= $schedule['open'] && $now <= $schedule['close'];
    }
    /**
     * Check if the chef profile is verified.
     * * @return bool
     */
    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Helper to check if accepting orders (maps to is_online column)
     */
    public function isAcceptingOrders()
    {
        return (bool) $this->is_online;
    }
}
