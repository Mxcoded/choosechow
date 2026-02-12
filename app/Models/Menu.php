<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',    // Correct foreign key
        'name',
        'slug',
        'description',
        'price',
        'image',
        'category',
        'is_available',
        'is_featured'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
    ];

    // RELATIONSHIPS
    
    // Links the menu to the Chef (User)
    public function chef()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}