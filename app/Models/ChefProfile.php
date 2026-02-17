<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ChefProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'slug',
        'bio',
        'kitchen_address',
        'city',
        'cover_image',
        'profile_image',
        'years_of_experience',
        'minimum_order',
        'delivery_fee',
        'delivery_radius_km',
        'operating_hours',
        'is_online',
        'bank_name',
        'account_number',
        'account_name',
        'verification_status',
        'verification_notes',
        'is_featured',
        'is_verified',
        'rating',
        'total_reviews',
        'total_orders',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'is_online' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'minimum_order' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    // ================== RELATIONSHIPS ==================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Many-to-Many: Cuisines this chef specializes in
     */
    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'chef_cuisine', 'chef_profile_id', 'cuisine_id');
    }

    // ================== HELPER METHODS ==================

    /**
     * Check if the chef is currently open based on operating hours
     */
    public function isOpenNow(): bool
    {
        if (!$this->is_online) {
            return false;
        }

        if (empty($this->operating_hours)) {
            return true; // If no hours set, assume always open when online
        }

        $now = Carbon::now();
        $dayName = strtolower($now->format('l')); // e.g., 'monday'
        
        $todayHours = $this->operating_hours[$dayName] ?? null;

        if (!$todayHours || ($todayHours['closed'] ?? false)) {
            return false;
        }

        $openTime = Carbon::parse($todayHours['open'] ?? '00:00');
        $closeTime = Carbon::parse($todayHours['close'] ?? '23:59');

        return $now->between($openTime, $closeTime);
    }

    /**
     * Alias for isOpenNow() - for backward compatibility
     */
    public function isAcceptingOrders(): bool
    {
        return $this->isOpenNow();
    }

    // ================== BOOT ==================

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($profile) {
            if (empty($profile->slug)) {
                $profile->slug = \Illuminate\Support\Str::slug($profile->business_name . '-' . $profile->user_id);
            }
        });
    }
}
