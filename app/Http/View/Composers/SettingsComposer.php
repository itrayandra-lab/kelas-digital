<?php

namespace App\Http\View\Composers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingsComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $settings = Cache::remember('site_settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        $view->with('settings', $settings);
    }
}
