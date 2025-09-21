<?php
// app/Models/Menu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str; 

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chef_id',
        'name',
        'slug',
        'description',
        'price',
        'discounted_price',
        'category',
        'cuisine_types',
        'dietary_info',
        'preparation_time_minutes',
        'serves_count',
        'ingredients',
        'allergens',
        'spice_level',
        'images',
        'is_available',
        'stock_quantity',
        'availability_schedule',
        'nutritional_info',
        'cooking_instructions',
        'storage_instructions',
        'featured_until',
        'is_featured',
        'view_count',
        'order_count',
        'average_rating',
        'total_reviews',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'cuisine_types' => 'array',
        'dietary_info' => 'array',
        'ingredients' => 'array',
        'allergens' => 'array',
        'images' => 'array',
        'availability_schedule' => 'array',
        'nutritional_info' => 'array',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
        'average_rating' => 'decimal:2',
    ];

    // Relationships
    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function chefProfile()
    {
        return $this->belongsTo(ChefProfile::class, 'chef_id', 'user_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    // Accessors & Mutators
    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->discounted_price ?? $this->price,
        );
    }

    protected function hasDiscount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->discounted_price && $this->discounted_price < $this->price,
        );
    }

    protected function discountPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->hasDiscount) return 0;
                return round((($this->price - $this->discounted_price) / $this->price) * 100);
            },
        );
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::slug($value ?: $this->name),
        );
    }

    protected function primaryImage(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->images[0] ?? null,
        );
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->whereHas('chef', function ($q) {
                $q->where('accepts_orders', true)
                    ->whereNull('suspended_at');
            });
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('stock_quantity')
                ->orWhere('stock_quantity', '>', 0);
        });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByCuisine($query, $cuisine)
    {
        return $query->whereJsonContains('cuisine_types', $cuisine);
    }

    public function scopeByDietaryInfo($query, $dietary)
    {
        return $query->whereJsonContains('dietary_info', $dietary);
    }

    public function scopeBySpiceLevel($query, $level)
    {
        return $query->where('spice_level', $level);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('featured_until', '>', now());
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->orderByDesc('order_count')
            ->orderByDesc('average_rating')
            ->limit($limit);
    }

    public function scopeHighRated($query, $minRating = 4.0)
    {
        return $query->where('average_rating', '>=', $minRating);
    }

    public function scopeByPriceRange($query, $min = null, $max = null)
    {
        return $query->when($min, function ($q) use ($min) {
            return $q->where('price', '>=', $min);
        })->when($max, function ($q) use ($max) {
            return $q->where('price', '<=', $max);
        });
    }

    public function scopeQuickPrep($query, $maxMinutes = 30)
    {
        return $query->where('preparation_time_minutes', '<=', $maxMinutes);
    }

    // Helper Methods
    public function isAvailable(): bool
    {
        return $this->is_available &&
            $this->isInStock() &&
            $this->isAvailableNow() &&
            $this->chef->chefProfile?->isOpenNow();
    }

    public function isInStock(): bool
    {
        return !$this->stock_quantity || $this->stock_quantity > 0;
    }

    public function isAvailableNow(): bool
    {
        if (!$this->availability_schedule) return true;

        $now = now();
        $dayOfWeek = strtolower($now->format('l'));
        $todaySchedule = $this->availability_schedule[$dayOfWeek] ?? null;

        if (!$todaySchedule || !($todaySchedule['available'] ?? true)) return false;

        if (isset($todaySchedule['start_time']) && isset($todaySchedule['end_time'])) {
            $currentTime = $now->format('H:i');
            return $currentTime >= $todaySchedule['start_time'] &&
                $currentTime <= $todaySchedule['end_time'];
        }

        return true;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured &&
            $this->featured_until &&
            $this->featured_until->isFuture();
    }

    public function hasDietaryInfo($info): bool
    {
        return in_array($info, $this->dietary_info ?? []);
    }

    public function hasAllergen($allergen): bool
    {
        return in_array($allergen, $this->allergens ?? []);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function decrementStock($quantity = 1): bool
    {
        if (!$this->stock_quantity) return true; // Unlimited stock

        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }

        return false; // Insufficient stock
    }

    public function restoreStock($quantity = 1): void
    {
        if ($this->stock_quantity !== null) {
            $this->increment('stock_quantity', $quantity);
        }
    }

    public function updateRating(): void
    {
        $avgRating = $this->reviews()->avg('rating') ?: 0;
        $totalReviews = $this->reviews()->count();

        $this->update([
            'average_rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
        ]);
    }

    public function setFeatured($until = null): void
    {
        $this->update([
            'is_featured' => true,
            'featured_until' => $until ?: now()->addDays(7),
        ]);
    }

    public function removeFeatured(): void
    {
        $this->update([
            'is_featured' => false,
            'featured_until' => null,
        ]);
    }

    public function getEstimatedDeliveryTime(): int
    {
        $prepTime = $this->preparation_time_minutes;
        $chefProfile = $this->chefProfile;

        // Add chef's average delivery time (default 30 minutes)
        $deliveryTime = 30;

        return $prepTime + $deliveryTime;
    }

    public function canBeOrderedBy($userId, $quantity = 1): array
    {
        $errors = [];

        if (!$this->isAvailable()) {
            $errors[] = 'This item is currently unavailable';
        }

        if (!$this->isInStock()) {
            $errors[] = 'This item is out of stock';
        }

        if ($this->stock_quantity && $quantity > $this->stock_quantity) {
            $errors[] = "Only {$this->stock_quantity} items available";
        }

        $chefProfile = $this->chefProfile;
        if ($chefProfile && !$chefProfile->isOpenNow()) {
            $errors[] = 'Chef is currently closed';
        }

        return [
            'can_order' => empty($errors),
            'errors' => $errors,
        ];
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($menu) {
            if (!$menu->slug) {
                $menu->slug = Str::slug($menu->name);
            }
        });

        static::updating(function ($menu) {
            if ($menu->isDirty('name') && !$menu->isDirty('slug')) {
                $menu->slug = Str::slug($menu->name);
            }
        });

        static::created(function ($menu) {
            // Update chef's menu count or other stats if needed
        });

        static::deleted(function ($menu) {
            // Clean up related data if needed
        });
    }
}