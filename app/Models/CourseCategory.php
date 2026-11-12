<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'course_category_id');
    }

    /** Font Awesome icon picked from the category name; admins never set one. */
    public function getIconAttribute(): string
    {
        $icons = [
            'marketing' => 'fa-bullhorn', 'brand' => 'fa-id-badge', 'content' => 'fa-photo-video',
            'social' => 'fa-hashtag', 'business' => 'fa-briefcase', 'bisnis' => 'fa-briefcase',
            'leader' => 'fa-users', 'tech' => 'fa-microchip', 'program' => 'fa-code',
            'komunikasi' => 'fa-comments', 'soft' => 'fa-comments', 'design' => 'fa-pen-nib',
            'kosmetik' => 'fa-spa', 'kesehatan' => 'fa-heartbeat', 'keuangan' => 'fa-coins',
        ];
        $name = Str::lower($this->name);

        foreach ($icons as $word => $icon) {
            if (str_contains($name, $word)) {
                return $icon;
            }
        }

        return 'fa-layer-group';
    }
}
