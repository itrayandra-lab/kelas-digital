<?php

namespace App\Models;

use App\Support\YouTube;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'type',
        'youtube_video_id',
        'content',
        'module',
        'order',
        'duration',
        'is_preview',
    ];

    public function setYoutubeVideoIdAttribute($value): void
    {
        $this->attributes['youtube_video_id'] = YouTube::id($value);
    }

    protected $casts = ['is_preview' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (Lesson $lesson) {
            if ($lesson->section_id && ! $lesson->course_id) {
                $lesson->course_id = CourseSection::whereKey($lesson->section_id)->value('course_id');
            }

            // Older callers (seeders, legacy forms) still pass a free-text "module"; map it to a real section.
            if (! $lesson->section_id && $lesson->course_id) {
                $title = trim((string) $lesson->module) ?: 'Materi';
                $lesson->section_id = CourseSection::firstOrCreate(
                    ['course_id' => $lesson->course_id, 'title' => $title],
                    ['order' => CourseSection::where('course_id', $lesson->course_id)->max('order') + 1],
                )->id;
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function isVideo(): bool
    {
        return $this->type !== 'text';
    }
}
