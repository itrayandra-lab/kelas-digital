<?php

use Illuminate\Support\Facades\Schedule;

// Schedule the article publishing command to run every minute
Schedule::command('articles:publish-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Queued emails (welcome, event tickets): the scheduler cron empties the queue each minute, so no daemon is needed.
Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
