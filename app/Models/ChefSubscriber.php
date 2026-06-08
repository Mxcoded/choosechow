<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChefSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chef_id',
        'notify_new_menu',
        'notify_promotions',
        'notify_availability',
    ];

    protected $casts = [
        'notify_new_menu' => 'boolean',
        'notify_promotions' => 'boolean',
        'notify_availability' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }
}
