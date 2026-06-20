<?php

use Illuminate\Support\Facades\Schedule;

// Schedule the article publishing command to run every minute
Schedule::command('articles:publish-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
