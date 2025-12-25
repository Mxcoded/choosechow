<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DietaryPreference extends Model
{
    use HasFactory;

    protected $table = 'dietary_preferences'; // Explicit table name
    protected $fillable = ['name', 'slug'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'dietary_preference_menu');
    }
}
