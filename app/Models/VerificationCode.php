<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'code',
        'type',
        'expires_at',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at > now();
    }

    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    public static function generate($identifier, $type, $expiresInMinutes = 10): self
    {
        // Invalidate existing codes
        self::where('identifier', $identifier)
            ->where('type', $type)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Generate new code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'identifier' => $identifier,
            'code' => $code,
            'type' => $type,
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }

    public static function verify($identifier, $code, $type): bool
    {
        $verificationCode = self::where('identifier', $identifier)
            ->where('code', $code)
            ->where('type', $type)
            ->valid()
            ->first();

        if ($verificationCode) {
            $verificationCode->markAsUsed();
            return true;
        }

        return false;
    }
}
