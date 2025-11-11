# Change: Add Dynamic Site Settings Management

## Why

Contact information (email, phone, address) and social media links are currently hardcoded in the header and footer templates. When these need to be updated (rebranding, new campaigns, social media changes), developers must modify Blade files and redeploy. Content managers should be able to update this information through an admin interface without technical intervention.

## What Changes

- Add new `settings` table with key-value storage pattern for site-wide configuration
- Create `Setting` model with casting and caching support
- Build admin panel at `/admin/site-settings` for managing contact info and social media links
- Support 7 social media platforms: Facebook, Twitter/X, Instagram, YouTube, TikTok, WhatsApp Business, LinkedIn
- All social media fields are optional (nullable) - only filled fields appear in frontend
- Create `SettingsComposer` to inject settings into all views alongside existing `CategoryComposer`
- Update header and footer templates to use dynamic settings instead of hardcoded values
- Restrict access to Content-Manager, Admin, and Super-Admin roles via `can:manage site settings` permission

## Impact

- **Affected specs**: Creates new `site-settings` capability
- **Affected code**:
  - New: `app/Models/Setting.php` - Model with key-value pattern
  - New: `app/Http/Controllers/Admin/SiteSettingsController.php` - Admin CRUD
  - New: `app/Http/View/Composers/SettingsComposer.php` - View data injection
  - New: `resources/views/admin/site-settings/index.blade.php` - Management UI
  - New: `database/migrations/YYYY_MM_DD_create_settings_table.php` - Schema
  - New: `database/seeders/SettingsSeeder.php` - Default values from current hardcoded data
  - Modified: `app/Providers/AppServiceProvider.php` - Register composer
  - Modified: `database/seeders/RolePermissionSeeder.php` - Add `manage site settings` permission
  - Modified: `routes/web.php` - Add `/admin/site-settings` route
  - Modified: `resources/views/layouts/app.blade.php` - Use `$settings` instead of hardcoded values
- **Breaking**: None - existing hardcoded values migrated to database as defaults
- **Migration**: Seeder populates current hardcoded values (Bandung address, existing social URLs) as initial data
