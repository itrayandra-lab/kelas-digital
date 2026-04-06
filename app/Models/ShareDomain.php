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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            default => '<span class="badge bg-warning">Unknown</span>',
        };
    }
}