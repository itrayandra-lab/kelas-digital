<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_name',
        'webhook_url',
        'api_key',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if domain is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if domain is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Activate the domain
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Deactivate the domain
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Scope query to only active domains
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query to only inactive domains
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Get formatted status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            default => '<span class="badge bg-warning">Unknown</span>',
        };
    }

    /**
     * Generate a new API key
     */
    public function generateApiKey(): string
    {
        $apiKey = bin2hex(random_bytes(32)); 
        $this->update(['api_key' => $apiKey]);
        return $apiKey;
    }
}