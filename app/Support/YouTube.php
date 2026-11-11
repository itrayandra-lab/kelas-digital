<?php

namespace App\Support;

class YouTube
{
    /**
     * Accepts a bare video ID or any common YouTube URL (watch, youtu.be, embed, shorts, live)
     * and returns just the 11-character ID that the embed iframe needs.
     */
    public static function id(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $value;
        }

        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~', $value, $m)) {
            return $m[1];
        }

        return $value;
    }
}
