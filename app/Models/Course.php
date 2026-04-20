<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

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
        'course_type',
        'benefits',
        'topics_preview',
        'schedule_start',
        'schedule_end',
        'meeting_platform',
    ];

    protected $casts = [
        'featured_at' => 'datetime',
        'schedule_start' => 'datetime',
        'schedule_end' => 'datetime',
    ];

    /**
     * Check if this is a free class
     */
    public function isFreeClass(): bool
    {
        return $this->course_type === 'free';
    }

    /**
     * Check if this is a paid course
     */
    public function isPaidCourse(): bool
    {
        return $this->course_type === 'paid';
    }

    /**
     * Return the sluggable configuration array for this model.
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
            ],
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
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('payment_status', 'status', 'enrolled_at', 'payment_method', 'payment_proof')
            ->withTimestamps();
    }

    /**
     * Get the enrollments for this course
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
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
    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
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
