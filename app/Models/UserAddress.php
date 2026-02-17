<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'label',
        'street_address',
        'apartment',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'delivery_instructions',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ================== RELATIONSHIPS ==================

    /**
     * Address belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ================== ACCESSORS ==================

    /**
     * Get full formatted address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->apartment,
            $this->city,
            $this->state,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get short address (street + city)
     */
    public function getShortAddressAttribute(): string
    {
        return $this->street_address . ', ' . $this->city;
    }

    // ================== SCOPES ==================

    /**
     * Scope: Only default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Only delivery addresses
     */
    public function scopeDelivery($query)
    {
        return $query->where('type', 'delivery');
    }
}
