<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'category',
        'preparation_time',
        'is_available',
        'is_featured',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
    ];

    // ================== RELATIONSHIPS ==================
    
    /**
     * The chef (user) who owns this menu item
     */
    public function chef()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for chef() - backward compatibility
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Many-to-Many: Cuisines this menu item belongs to
     */
    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'cuisine_menu', 'menu_id', 'cuisine_id');
    }

    /**
     * Many-to-Many: Dietary preferences for this menu item
     */
    public function dietaryPreferences()
    {
        return $this->belongsToMany(DietaryPreference::class, 'dietary_preference_menu', 'menu_id', 'dietary_preference_id');
    }

    // ================== ACCESSORS ==================

    /**
     * Get images as array (for backward compatibility with views expecting 'images')
     */
    public function getImagesAttribute()
    {
        if ($this->image) {
            return [$this->image];
        }
        return [];
    }

    // ================== BOOT ==================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($menu) {
            if (empty($menu->slug)) {
                $menu->slug = Str::slug($menu->name . '-' . uniqid());
            }
        });
    }
}
