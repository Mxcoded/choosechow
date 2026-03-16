<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WaitlistSignup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'neighborhood_id',
        'actor_category_id',
        'referral_token',
        'referred_by_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'discovery_source',
        'ip_address',
        'user_agent',
        'step_completed',
        'status',
        'verified_at',
        'converted_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    // Auto-generate referral token on create
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($signup) {
            if (empty($signup->referral_token)) {
                $signup->referral_token = self::generateUniqueToken();
            }
        });
    }

    // Generate unique 8-character referral token
    public static function generateUniqueToken(): string
    {
        do {
            $token = strtoupper(Str::random(8));
        } while (self::where('referral_token', $token)->exists());
        
        return $token;
    }

    // ================== RELATIONSHIPS ==================

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function actorCategory()
    {
        return $this->belongsTo(ActorCategory::class);
    }

    public function referrer()
    {
        return $this->belongsTo(WaitlistSignup::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(WaitlistSignup::class, 'referred_by_id');
    }

    public function survey()
    {
        return $this->hasOne(WaitlistSurvey::class);
    }

    // ================== SCOPES ==================

    public function scopeFoodLovers($query)
    {
        return $query->where('role', 'food_lover');
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeWithSurvey($query)
    {
        return $query->where('step_completed', 2);
    }

    public function scopeFromSource($query, $source)
    {
        return $query->where('utm_source', $source);
    }

    public function scopeInNeighborhood($query, $neighborhoodId)
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    // ================== ACCESSORS ==================

    public function getRoleDisplayAttribute()
    {
        return $this->role === 'food_lover' ? 'Food Lover' : 'Vendor';
    }

    public function getReferralLinkAttribute()
    {
        return url('/waitlist/join?ref=' . $this->referral_token);
    }

    public function getReferralCountAttribute()
    {
        return $this->referrals()->count();
    }

    public function getHasUtmAttribute()
    {
        return !empty($this->utm_source) || !empty($this->utm_medium) || !empty($this->utm_campaign);
    }

    public function getTrafficSourceAttribute()
    {
        if ($this->has_utm) {
            return $this->utm_source ?? 'Unknown UTM';
        }
        return $this->discovery_source ?? 'Direct';
    }

    // ================== HELPERS ==================

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isFoodLover(): bool
    {
        return $this->role === 'food_lover';
    }

    public function hasSurvey(): bool
    {
        return $this->step_completed >= 2;
    }

    public function markSurveyCompleted(): void
    {
        $this->update(['step_completed' => 2]);
    }

    public function markVerified(): void
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    public function markConverted(): void
    {
        $this->update([
            'status' => 'converted',
            'converted_at' => now(),
        ]);
    }
}
