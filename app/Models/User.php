<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'user_type',
        'status',
        'avatar',
        'date_of_birth',
        'gender',
        'referral_code',
        'referred_by',
        'device_token',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'preferences' => 'array',
    ];

    // Relationships
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function chefProfile()
    {
        return $this->hasOne(ChefProfile::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->whereIn('status', ['active', 'trial'])
            ->latest();
    }

    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function chefOrders()
    {
        return $this->hasMany(Order::class, 'chef_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'chef_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'chef_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function payouts()
    {
        return $this->hasMany(ChefPayout::class, 'chef_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by', 'referral_code');
    }

    // Accessors & Mutators
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->first_name . ' ' . $this->last_name,
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcrypt($value),
        );
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->avatar
                ? asset('storage/' . $this->avatar)
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=ef4444&color=fff',
        );
    }

    // Scopes
    public function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }

    public function scopeChefs($query)
    {
        return $query->where('user_type', 'chef');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Helper Methods
    public function isCustomer(): bool
    {
        return $this->user_type === 'customer';
    }

    public function isChef(): bool
    {
        return $this->user_type === 'chef';
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function getDefaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    public function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        } while (self::where('referral_code', $code)->exists());

        $this->update(['referral_code' => $code]);
        return $code;
    }
}
