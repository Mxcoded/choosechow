<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'menus';

    protected $fillable = [
        'chef_id',
        'category_id', // Changed from 'category' string to Foreign Key
        'name',
        'slug',
        'description',
        'price',
        'discounted_price',
        'preparation_time_minutes',
        'serves_count',
        'ingredients',      // JSON: List of strings (e.g. ["Salt", "Pepper"])
        'allergens',        // JSON: List of strings
        'spice_level',      // 0-5
        'images',           // JSON: Paths
        'is_available',
        'stock_quantity',
        'availability_schedule', // JSON: {"monday": {"start": "09:00", "end": "17:00"}}
        'nutritional_info',      // JSON: {"Calories": "500", "Protein": "20g"}
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

    // --- RELATIONSHIPS ---

    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Pivot Relationship: Menu <-> Cuisines
    public function cuisines(): BelongsToMany
    {
        return $this->belongsToMany(Cuisine::class, 'cuisine_menu');
    }

    // Pivot Relationship: Menu <-> Dietary Preferences
    public function dietaryPreferences(): BelongsToMany
    {
        return $this->belongsToMany(DietaryPreference::class, 'dietary_preference_menu');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    // --- SCOPES ---

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->whereHas('chef', function ($q) {
                $q->where('status', 'active'); // Ensure chef is active
            });
    }

    // Filter by Category ID
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Filter by Cuisine Slug (via Pivot)
    public function scopeByCuisine($query, $cuisineSlug)
    {
        return $query->whereHas('cuisines', function ($q) use ($cuisineSlug) {
            $q->where('slug', $cuisineSlug);
        });
    }

    // Filter by Dietary Preference (via Pivot)
    public function scopeByDietary($query, $preferenceSlug)
    {
        return $query->whereHas('dietaryPreferences', function ($q) use ($preferenceSlug) {
            $q->where('slug', $preferenceSlug);
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('featured_until', '>', now());
    }

    // --- ACCESSORS & MUTATORS ---

    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->discounted_price ?? $this->price,
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

    // --- MODEL EVENTS ---

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($menu) {
            if (empty($menu->slug)) {
                $menu->slug = Str::slug($menu->name . '-' . Str::random(6));
            }
        });

        static::updating(function ($menu) {
            if ($menu->isDirty('name') && !$menu->isDirty('slug')) {
                $menu->slug = Str::slug($menu->name . '-' . Str::random(6));
            }
        });
    }
}
