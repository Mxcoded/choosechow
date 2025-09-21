<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeChefs($query)
    {
        return $query->where('favoritable_type', User::class);
    }

    public function scopeMenus($query)
    {
        return $query->where('favoritable_type', Menu::class);
    }

    // Helper Methods
    public static function toggle($userId, $favoritable)
    {
        $favorite = self::where([
            'user_id' => $userId,
            'favoritable_type' => get_class($favoritable),
            'favoritable_id' => $favoritable->id,
        ])->first();

        if ($favorite) {
            $favorite->delete();
            return false; // Unfavorited
        } else {
            self::create([
                'user_id' => $userId,
                'favoritable_type' => get_class($favoritable),
                'favoritable_id' => $favoritable->id,
            ]);
            return true; // Favorited
        }
    }

    public static function isFavorited($userId, $favoritable): bool
    {
        return self::where([
            'user_id' => $userId,
            'favoritable_type' => get_class($favoritable),
            'favoritable_id' => $favoritable->id,
        ])->exists();
    }
}