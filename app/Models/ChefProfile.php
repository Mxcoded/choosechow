<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChefProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'slug',
        'bio',
        'kitchen_address',
        'cover_image',
        'profile_image',
        'years_of_experience',
        'minimum_order',
        'delivery_fee',
        'operating_hours',
        'is_online',
        'bank_name',
        'account_number',
        'account_name',
        'cuisines',
        'is_verified', // <--- ADDED: This allows the Admin Verification to save
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'cuisines' => 'array',
        'is_online' => 'boolean',
        'is_verified' => 'boolean', // <--- ADDED: Ensures true/false behavior
        'minimum_order' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the chef is currently accepting orders.
     */
    public function isAcceptingOrders()
    {
        if (!$this->is_online) {
            return false;
        }
        if (is_null($this->operating_hours)) {
            return true;
        }
        return true;
    }

    protected static function boot()
    {
        parent::boot();

        // Automatically generate slug whenever a profile is Created or Updated
        static::saving(function ($profile) {
            if (empty($profile->slug)) {
                $profile->slug = \Illuminate\Support\Str::slug($profile->business_name . '-' . $profile->user_id);
            }
        });
    }
}