<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteSettingsController extends Controller
{
    /**
     * Display the site settings management page.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('admin.site-settings.index', compact('settings'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:255',
            'contact_address' => 'required|string|max:500',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'social_tiktok' => 'nullable|url',
            'social_whatsapp' => 'nullable|url',
            'social_linkedin' => 'nullable|url',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        Cache::forget('site_settings');

        return redirect()->route('admin.site-settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
