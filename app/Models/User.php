<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
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

    // --- RELATIONSHIPS ---

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

    // --- ORDER RELATIONSHIPS (CRITICAL FIXES) ---

    // As a Customer (Buying food) - Maps to 'user_id' in orders table
    public function ordersPlaced()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // As a Chef (Selling food) - Maps to 'chef_id' in orders table
    public function ordersReceived()
    {
        return $this->hasMany(Order::class, 'chef_id');
    }

    // --- OTHER RELATIONSHIPS ---

    public function menus()
    {
        return $this->hasMany(Menu::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

     /**
     * Reviews received by this user (as a Chef).
     */
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
        return $this->hasMany(ChefPayout::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by', 'referral_code');
    }

    // --- ACCESSORS & MUTATORS ---

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

    // --- HELPERS ---

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isChef(): bool
    {
        return $this->hasRole('chef');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
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

    // Get the average rating from the reviews table
    public function getAverageRatingAttribute()
    {
        // If no reviews, return 0
        if ($this->receivedReviews()->count() == 0) {
            return 0;
        }
        
        // Calculate average and round to 1 decimal (e.g., 4.5)
        return round($this->receivedReviews()->avg('rating'), 1);
    }

    // Get the total number of reviews
    public function getReviewCountAttribute()
    {
        return $this->receivedReviews()->count();
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->latest();
    }

    /**
     * Relationship: Orders received by this user (as a Chef).
     */
    public function chefOrders()
    {
        return $this->hasMany(Order::class, 'chef_id');
    }
}