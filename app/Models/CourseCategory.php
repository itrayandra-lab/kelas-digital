<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        $category->slug = \Illuminate\Support\Str::slug($category->name);
    });

    static::updating(function ($category) {
        $category->slug = \Illuminate\Support\Str::slug($category->name);
    });
}

    public function courses()
    {
        return $this->hasMany(Course::class, 'course_category_id');
    }
}

