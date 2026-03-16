<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitlistSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'waitlist_signup_id',
        'favorite_meals',
        'dietary_preferences',
        'reason_for_choosing',
        'preferred_price_range',
        'meals_per_week',
        'preferred_cuisines',
    ];

    protected $casts = [
        'favorite_meals' => 'array',
        'dietary_preferences' => 'array',
        'preferred_cuisines' => 'array',
    ];

    // Relationships
    public function signup()
    {
        return $this->belongsTo(WaitlistSignup::class, 'waitlist_signup_id');
    }

    // Accessors
    public function getPriceRangeDisplayAttribute()
    {
        $ranges = [
            'budget' => '₦500 - ₦1,500',
            'mid-range' => '₦1,500 - ₦3,500',
            'premium' => '₦3,500+',
        ];
        
        return $ranges[$this->preferred_price_range] ?? $this->preferred_price_range;
    }

    public function getFavoriteMealsListAttribute()
    {
        return is_array($this->favorite_meals) 
            ? implode(', ', $this->favorite_meals) 
            : $this->favorite_meals;
    }

    public function getDietaryPreferencesListAttribute()
    {
        return is_array($this->dietary_preferences) 
            ? implode(', ', $this->dietary_preferences) 
            : $this->dietary_preferences;
    }
}
