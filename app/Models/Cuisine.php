<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cuisine extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($cuisine) {
            if (empty($cuisine->slug)) {
                $cuisine->slug = Str::slug($cuisine->name);
            }
        });
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'cuisine_menu');
    }

    public function chefs()
    {
        return $this->belongsToMany(ChefProfile::class, 'chef_cuisine');
    }
}
