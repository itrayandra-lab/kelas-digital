<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per learner and lesson. The server is the source of truth for how far a video was watched:
 * the browser only reports positions, and the server caps how fast "watched" may grow.
 */
class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = ['user_id', 'lesson_id', 'position', 'watched_seconds', 'video_duration', 'started_at', 'last_ping_at', 'completed_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'last_ping_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /** Playback may run at up to 2x speed, plus a little slack for ping timing. */
    private const MAX_SPEED = 2;

    private const SLACK_SECONDS = 5;

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Records a position report. "Watched" can only advance as fast as real time allows since the
     * previous report, so jumping or forging a far position does not count as watching.
     */
    public function recordPing(int $position, int $duration): void
    {
        $now = now();
        $elapsed = $this->last_ping_at ? max(0, $this->last_ping_at->diffInSeconds($now, true)) : 0;
        $reachable = $this->watched_seconds + (int) ceil($elapsed * self::MAX_SPEED) + self::SLACK_SECONDS;

        $this->watched_seconds = max($this->watched_seconds, min($position, $reachable));
        // Resume where they were, but never beyond what was accepted as watched.
        $this->position = min($position, $this->watched_seconds);
        // The longest duration ever reported wins, so a learner can't shrink the video to finish early.
        $this->video_duration = max((int) $this->video_duration, $duration);
        $this->last_ping_at = $now;
    }

    public function videoWatchedEnough(Lesson $lesson): bool
    {
        // If the admin filled in the duration, it sets a floor (minus 20% for rounding) on the reported length.
        $floor = $lesson->duration ? (int) ($lesson->duration * 60 * 0.8) : 0;
        $duration = max((int) $this->video_duration, $floor);

        return $duration > 0 && $this->watched_seconds >= $duration * 0.9;
    }

    /**
     * Text lessons need the page open for a fraction of the estimated reading time (200 words/min).
     */
    public function readLongEnough(Lesson $lesson): bool
    {
        $words = str_word_count(strip_tags((string) $lesson->content));
        $required = min(120, max(10, (int) ($words / 200 * 60 * 0.3)));

        return $this->started_at && $this->started_at->diffInSeconds(now(), true) >= $required;
    }
}
