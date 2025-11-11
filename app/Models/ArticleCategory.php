<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_featured_section',
        'featured_at',
        'theme_color',
    ];

    protected $casts = [
        'is_featured_section' => 'boolean',
        'featured_at' => 'datetime',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_article_category');
    }

    /**
     * Get the theme color for this category based on slug
     */
    public function getThemeColorAttribute(): string
    {
        // If custom theme_color is set, use it
        if ($this->attributes['theme_color'] ?? null) {
            return $this->attributes['theme_color'];
        }

        // Otherwise, return predefined color based on slug
        return match (strtoupper($this->slug)) {
            'SKINCARE' => 'bg-pink-600',
            'MYTHBUSTER' => 'bg-purple-600',
            'HAIRCARE' => 'bg-blue-600',
            'DECORATIVE' => 'bg-rose-600',
            'BAHANAKTIF' => 'bg-green-600',
            'MENZONE' => 'bg-indigo-600',
            'PERSONALCARE' => 'bg-teal-600',
            'BEAUTYLIFE' => 'bg-amber-600',
            default => 'bg-gray-800',
        };
    }

    /**
     * Scope to get the featured category
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured_section', true);
    }
}

