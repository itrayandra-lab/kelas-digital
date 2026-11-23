<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Tells Bing, Yandex, Naver and Seznam (and through Bing, ChatGPT Search and Copilot) that a page changed.
class IndexNow
{
    public static function ping(array $urls): void
    {
        $key = config('seo.indexnow_key');
        if (! $key || ! app()->environment('production') || ! $urls) {
            return;
        }

        $host = config('seo.primary_host');
        // Runs after the admin's response is sent, so a slow or down endpoint never blocks saving.
        dispatch(function () use ($urls, $key, $host) {
            try {
                Http::timeout(5)->post('https://api.indexnow.org/indexnow', [
                    'host' => $host,
                    'key' => $key,
                    'keyLocation' => "https://{$host}/indexnow.txt",
                    'urlList' => array_values($urls),
                ]);
            } catch (\Throwable $e) {
                Log::warning('IndexNow ping failed: '.$e->getMessage());
            }
        })->afterResponse();
    }
}
