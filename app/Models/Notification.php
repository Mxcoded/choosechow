<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'title',
        'message',
        'data',
        'channels',
        'read_at',
        'sent_at',
        'delivery_status',
        'priority',
    ];

    protected $casts = [
        'data' => 'array',
        'channels' => 'array',
        'delivery_status' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    // Helper Methods
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function wasDeliveredVia($channel): bool
    {
        return isset($this->delivery_status[$channel]) &&
            $this->delivery_status[$channel] === 'delivered';
    }

    public function getData($key = null): mixed
    {
        if ($key) {
            return $this->data[$key] ?? null;
        }
        return $this->data;
    }
}
