<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reference', 'type', 'amount', 'description', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}