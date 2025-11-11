<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Cviebrock\EloquentSluggable\Sluggable;

class Course extends Model
{
    use HasFactory;
    use HasSEO;
    use Sluggable;

    protected $fillable = [
        'title',
        'slug',
        'instructor',
        'description',
        'price',
        'thumbnail',
        'trailer_video_id',
        'course_category_id',
        'level',
        'is_featured',
        'featured_at',
    ];

    protected $casts = [
        // Removed full_video_ids cast as it's no longer needed
        'featured_at' => 'datetime',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => false, // Slug tidak berubah saat update
                'unique' => true,
                'separator' => '-',
                'maxLength' => 100,
                'maxLengthKeepWords' => true,
            ]
        ];
    }

    /**
     * Scope a query to only include featured courses
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->orderBy('featured_at', 'desc');
    }

    /**
     * Get the users enrolled in this course
     */
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'enrollments')
                    ->withPivot('payment_status', 'status', 'enrolled_at', 'payment_method', 'payment_proof')
                    ->withTimestamps();
    }

    /**
     * Get the enrollments for this course
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    /**
     * Get the lessons for this course
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    /**
     * Get dynamic SEO data for this course
     */
    public function getDynamicSEOData(): \RalphJSmit\Laravel\SEO\Support\SEOData
    {
        return new \RalphJSmit\Laravel\SEO\Support\SEOData(
            title: $this->title,
            description: $this->description ?: 'Kursus online berkualitas tinggi di Kelas Digital. Pelajari dengan instruktur berpengalaman dan dapatkan sertifikat setelah menyelesaikan kursus.',
            author: $this->instructor ?: 'Kelas Digital Team',
            image: $this->thumbnail ?: '/logo.webp',
            url: route('course.show', $this->slug),
            published_time: $this->created_at,
            modified_time: $this->updated_at,
            section: $this->category?->name,
        );
    }
}
