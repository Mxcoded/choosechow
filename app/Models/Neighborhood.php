<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Neighborhood extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'city',
        'state',
        'lga',
        'latitude',
        'longitude',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Auto-generate slug on create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($neighborhood) {
            if (empty($neighborhood->slug)) {
                $neighborhood->slug = Str::slug($neighborhood->name . '-' . $neighborhood->city);
            }
        });
    }

    // Relationships
    public function waitlistSignups()
    {
        return $this->hasMany(WaitlistSignup::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return "{$this->name}, {$this->city}";
    }

    // Get signup statistics
    public function getSignupCountAttribute()
    {
        return $this->waitlistSignups()->count();
    }

    public function getDemandCountAttribute()
    {
        return $this->waitlistSignups()->where('role', 'food_lover')->count();
    }

    public function getSupplyCountAttribute()
    {
        return $this->waitlistSignups()->where('role', 'vendor')->count();
    }
}
