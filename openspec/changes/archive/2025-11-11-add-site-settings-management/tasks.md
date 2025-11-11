# Implementation Tasks

## 1. Database Schema

- [x] 1.1 Create migration `YYYY_MM_DD_HHMMSS_create_settings_table.php`
  - Add columns: `id`, `key` (string, unique), `value` (text, nullable), `type` (enum: string, integer, boolean, array, default: string), `timestamps`
  - Add unique index on `key` column
- [x] 1.2 Create seeder `database/seeders/SettingsSeeder.php`
  - Seed contact settings: `contact_email`, `contact_phone`, `contact_address` with current hardcoded values
  - Seed social media settings: `social_facebook`, `social_twitter`, `social_instagram`, `social_youtube` with current URLs
  - Seed new platforms as null: `social_tiktok`, `social_whatsapp`, `social_linkedin`
- [x] 1.3 Update `DatabaseSeeder` to call `SettingsSeeder`
- [x] 1.4 Run migration and seeder: `php artisan migrate --seed`

## 2. Model Layer

- [x] 2.1 Create model `app/Models/Setting.php`
  - Define `$fillable`: `['key', 'value', 'type']`
  - Add accessor `getValueAttribute()` for type casting (string, integer, boolean, array based on `type` column)
  - Add static helper method `Setting::get($key, $default = null)` for easy retrieval
- [x] 2.2 Add model event to clear cache on `updated` and `deleted` events
  - In `boot()` method: `static::updated(fn() => Cache::forget('site_settings'));`

## 3. View Composer

- [x] 3.1 Create view composer `app/Http/View/Composers/SettingsComposer.php`
  - Implement `compose(View $view)` method
  - Fetch settings with caching: `Cache::remember('site_settings', 3600, fn() => Setting::pluck('value', 'key')->toArray())`
  - Bind to view: `$view->with('settings', $settings);`
- [x] 3.2 Register composer in `app/Providers/AppServiceProvider.php`
  - Add `View::composer('layouts.app', SettingsComposer::class);` in `boot()` method
  - Ensure `use App\Http\View\Composers\SettingsComposer;` at top

## 4. Permission Setup

- [x] 4.1 Update `database/seeders/RolePermissionSeeder.php`
  - Add `manage site settings` to permissions array
  - Assign to roles: `content-manager`, `admin` (Super-Admin gets via Gate::before)
  - Ensure permission is created in `$allPermissions` loop
- [x] 4.2 Run seeder to add permission: `php artisan db:seed --class=RolePermissionSeeder` (or fresh migration)

## 5. Admin Controller

- [x] 5.1 Create controller `app/Http/Controllers/Admin/SiteSettingsController.php`
  - Namespace: `App\Http\Controllers\Admin`
  - Extend `Controller`
  - Method `index()`: Fetch all settings, return `admin.site-settings.index` view with `$settings` collection
  - Method `update(Request $request)`:
    - Validate all fields (email, phone, address, 7 social URLs with `nullable|url` rules)
    - Loop through validated data, update or create Setting records by key
    - Clear cache: `Cache::forget('site_settings')`
    - Redirect back with success message: `return redirect()->route('admin.site-settings.index')->with('success', 'Settings updated successfully.');`
- [x] 5.2 Add route in `routes/web.php`
  - Inside `admin.` prefix group, after `can:access admin panel` middleware
  - Add nested middleware: `Route::middleware('can:manage site settings')->group(function () { ... });`
  - Route: `Route::get('site-settings', [Admin\SiteSettingsController::class, 'index'])->name('site-settings.index');`
  - Route: `Route::put('site-settings', [Admin\SiteSettingsController::class, 'update'])->name('site-settings.update');`
  - Use statement: `use App\Http\Controllers\Admin\SiteSettingsController;`

## 6. Admin View

- [x] 6.1 Create view `resources/views/admin/site-settings/index.blade.php`
  - Extend `layouts.admin` layout
  - Add page header: "Site Settings" with description "Manage contact information and social media links"
  - Display success flash message if present
  - Create form with `@method('PUT')` targeting `route('admin.site-settings.update')`
  - Add Alpine.js component: `x-data="{ tab: 'contact' }"`
- [x] 6.2 Add tabbed interface
  - Tab buttons: "Contact Info" (`@click="tab = 'contact'"`) and "Social Media" (`@click="tab = 'social'"`)
  - Style active tab with Tailwind (primary color border/background)
- [x] 6.3 Add Contact Info tab content (`x-show="tab === 'contact'"`)
  - Input fields: `contact_email`, `contact_phone`, `contact_address`
  - Each with label, validation error display (`@error` directive), and pre-filled value from `$settings->where('key', 'contact_email')->first()->value ?? ''`
- [x] 6.4 Add Social Media tab content (`x-show="tab === 'social'"`)
  - Input fields for 7 platforms: `social_facebook`, `social_twitter`, `social_instagram`, `social_youtube`, `social_tiktok`, `social_whatsapp`, `social_linkedin`
  - Each with label, icon (Font Awesome), placeholder URL, and pre-filled value
  - Mark as optional with "(Optional)" hint
- [x] 6.5 Add submit button: "Save Settings" with primary button styling

## 7. Frontend Template Updates

- [x] 7.1 Update header top bar in `resources/views/layouts/app.blade.php` (lines 32-46)
  - Replace hardcoded address (line 36) with `{{ $settings['contact_address'] ?? 'Bandung, Jawa Barat, Indonesia' }}`
  - Replace social link URLs (lines 39-43) with conditional rendering:
    ```blade
    @if(!empty($settings['social_facebook']))
        <a href="{{ $settings['social_facebook'] }}" class="..." target="_blank">
            <i class="fab fa-facebook-f"></i>
        </a>
    @endif
    ```
  - Repeat for Twitter, Instagram, YouTube
- [x] 7.2 Update footer contact section (lines 343-367)
  - Replace email (line 350) with `{{ $settings['contact_email'] ?? 'info@kelasdigital.com' }}`
  - Replace phone (line 357) with `{{ $settings['contact_phone'] ?? '+62 123 456 7890' }}`
  - Replace address (line 364) with `{{ $settings['contact_address'] ?? 'Bandung, Jawa Barat, Indonesia' }}`
- [x] 7.3 Update footer social section (lines 369-390)
  - Replace social URLs (lines 373-388) with conditional rendering using `@if(!empty($settings['social_*']))`
  - Add new platforms: TikTok, WhatsApp, LinkedIn with appropriate Font Awesome icons
    - TikTok: `fab fa-tiktok`
    - WhatsApp: `fab fa-whatsapp`
    - LinkedIn: `fab fa-linkedin-in`

## 8. Testing

- [x] 8.1 Manual test: Database and seeding
  - Run `php artisan migrate:fresh --seed`
  - Verify `settings` table exists with 10 seeded rows
  - Query in tinker: `Setting::where('key', 'contact_email')->value('value')` returns correct value
- [x] 8.2 Manual test: Admin access
  - Log in as content-manager user
  - Navigate to `/admin/site-settings`
  - Verify page loads with tabbed interface
  - Verify form fields pre-filled with current values
- [x] 8.3 Manual test: Settings update
  - Change contact email to `test@beautyversity.id`
  - Submit form
  - Verify success message appears
  - Verify database updated: `Setting::where('key', 'contact_email')->value('value')` returns new value
  - Verify cache cleared (check with `Cache::get('site_settings')` in tinker - should be null after update)
- [x] 8.4 Manual test: Frontend display
  - Load homepage
  - Inspect footer email - should show updated value from step 8.3
  - Change TikTok URL in admin to `https://tiktok.com/@beautyversity`
  - Reload homepage
  - Verify TikTok icon appears in header and footer with correct link
- [x] 8.5 Manual test: Optional fields
  - Clear WhatsApp URL in admin (leave blank)
  - Submit form
  - Reload homepage
  - Verify WhatsApp icon does NOT appear
- [x] 8.6 Manual test: Validation
  - Enter invalid URL format (e.g., `not-a-url`) in Facebook field
  - Submit form
  - Verify validation error message displays
  - Verify form does not save
- [x] 8.7 Manual test: Permission check
  - Log in as student user
  - Attempt to visit `/admin/site-settings`
  - Verify 403 Forbidden error

## 9. Documentation

- [x] 9.1 Update `CLAUDE.md` with new feature
  - Add section under "Content Management System" describing Site Settings Management
  - Document permission: `manage site settings` for Content-Manager, Admin, Super-Admin
  - Document caching strategy (1 hour TTL, clears on update)
- [x] 9.2 Update `openspec/project.md` if needed
  - Add settings management to "Architecture Overview" under RBAC section
  - Note view composer pattern for settings injection

## 10. Code Review Checklist

- [x] 10.1 Verify all hardcoded values removed from `layouts/app.blade.php`
- [x] 10.2 Verify settings cache clears on update (test with `Cache::get('site_settings')` before/after save)
- [x] 10.3 Verify permission correctly restricts student access (403 error)
- [x] 10.4 Verify all 7 social platforms render correctly when URLs provided
- [x] 10.5 Verify empty social fields do not render icons (no broken links)
- [x] 10.6 Verify validation prevents invalid URLs
- [x] 10.7 Verify form pre-fills with existing settings values
- [x] 10.8 Verify Alpine.js tabs work (click to switch between Contact and Social)
