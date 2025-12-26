<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'chef_profile_id',
        'name',           // e.g. "Lekki Phase 1"
        'min_order_amount',
        'delivery_fee',
        'delivery_time_min', // Estimated minutes
        'delivery_time_max',
    ];

    public function chefProfile()
    {
        return $this->belongsTo(ChefProfile::class);
    }
}
