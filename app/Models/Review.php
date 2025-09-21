<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'chef_id',
        'menu_id',
        'rating',
        'comment',
        'rating_breakdown',
        'images',
        'is_verified',
        'is_featured',
        'chef_response',
        'chef_responded_at',
    ];

    protected $casts = [
        'rating_breakdown' => 'array',
        'images' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'chef_responded_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeHighRated($query, $minRating = 4)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Helper Methods
    public function hasChefResponse(): bool
    {
        return !empty($this->chef_response);
    }

    public function addChefResponse($response): void
    {
        $this->update([
            'chef_response' => $response,
            'chef_responded_at' => now(),
        ]);
    }

    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    public function getRatingBreakdown($aspect): ?int
    {
        return $this->rating_breakdown[$aspect] ?? null;
    }
}