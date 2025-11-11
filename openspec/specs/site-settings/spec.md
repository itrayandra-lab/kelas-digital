# site-settings Specification

## Purpose
TBD - created by archiving change add-site-settings-management. Update Purpose after archive.
## Requirements
### Requirement: Settings Storage

The system SHALL store site-wide configuration settings in a `settings` database table with key-value pattern.

#### Scenario: Setting stored with key and value

- **WHEN** a setting is created with key `contact_email` and value `info@kelasdigital.com`
- **THEN** the setting is persisted in the `settings` table with columns: `key`, `value`, `type`, `timestamps`

#### Scenario: Setting retrieved by key

- **WHEN** a setting with key `contact_email` exists in the database
- **THEN** the system returns the value `info@kelasdigital.com` when queried by key

#### Scenario: Setting key is unique

- **WHEN** attempting to create a duplicate setting with the same key
- **THEN** the database constraint prevents insertion and raises a unique violation error

### Requirement: Setting Model

The system SHALL provide a `Setting` Eloquent model to interact with the settings table.

#### Scenario: Get setting value by key

- **WHEN** calling `Setting::where('key', 'contact_email')->value('value')`
- **THEN** the system returns the stored value for that key

#### Scenario: Update setting value

- **WHEN** calling `Setting::where('key', 'contact_phone')->update(['value' => '+62 999 888 7777'])`
- **THEN** the phone setting is updated in the database
- **AND** the cache is cleared to reflect the change

### Requirement: Settings View Injection

The system SHALL inject all settings into the `layouts.app` view via a `SettingsComposer`.

#### Scenario: Settings available in layout

- **WHEN** the `layouts.app` view is rendered
- **THEN** a `$settings` array is available containing all settings as key-value pairs
- **AND** the settings are retrieved from cache if available (1 hour TTL)

#### Scenario: Settings cached for performance

- **WHEN** settings are requested for the first time
- **THEN** the system queries the database and caches the result for 3600 seconds
- **AND** subsequent requests within 1 hour use the cached data

#### Scenario: Cache cleared on settings update

- **WHEN** any setting is updated through the admin panel
- **THEN** the `site_settings` cache key is cleared
- **AND** the next page load fetches fresh data from the database

### Requirement: Contact Information Management

The system SHALL allow content managers to update contact information through an admin interface.

#### Scenario: Edit contact email

- **WHEN** a content manager navigates to `/admin/site-settings`
- **AND** updates the `contact_email` field to `hello@beautyversity.id`
- **AND** submits the form
- **THEN** the setting is saved to the database
- **AND** the new email appears in the footer on the next page load

#### Scenario: Contact information displayed in header and footer

- **WHEN** the header or footer is rendered
- **THEN** contact email, phone, and address are displayed using values from `$settings['contact_email']`, `$settings['contact_phone']`, `$settings['contact_address']`

### Requirement: Social Media Links Management

The system SHALL allow content managers to update social media URLs for 7 platforms: Facebook, Twitter/X, Instagram, YouTube, TikTok, WhatsApp Business, LinkedIn.

#### Scenario: Add Facebook URL

- **WHEN** a content manager enters `https://facebook.com/beautyversitydotid` in the Facebook URL field
- **AND** submits the form
- **THEN** the setting `social_facebook` is saved with that URL
- **AND** the Facebook icon link appears in the header and footer

#### Scenario: Social media link hidden when not configured

- **WHEN** the `social_tiktok` setting is null or empty
- **THEN** the TikTok icon does not render in the header or footer

#### Scenario: Social media links validated as URLs

- **WHEN** a content manager enters an invalid URL format (e.g., `not-a-url`)
- **THEN** the form validation fails with an error message
- **AND** the setting is not saved

#### Scenario: All social platforms are optional

- **WHEN** only Facebook and Instagram URLs are provided
- **THEN** only Facebook and Instagram icons appear in the header/footer
- **AND** other platforms are hidden

### Requirement: Admin Interface for Settings

The system SHALL provide an admin interface at `/admin/site-settings` for managing settings with tabbed sections for Contact Info and Social Media.

#### Scenario: Access restricted to authorized roles

- **WHEN** a user with `manage site settings` permission visits `/admin/site-settings`
- **THEN** the settings management page is displayed

#### Scenario: Unauthorized access denied

- **WHEN** a student (without `manage site settings` permission) attempts to visit `/admin/site-settings`
- **THEN** the system returns a 403 Forbidden error

#### Scenario: Settings form displays current values

- **WHEN** a content manager opens `/admin/site-settings`
- **THEN** the form is pre-filled with existing setting values from the database

#### Scenario: Tabbed interface for organization

- **WHEN** the settings page loads
- **THEN** tabs for "Contact Info" and "Social Media" are visible
- **AND** clicking a tab displays the corresponding form fields using Alpine.js

#### Scenario: Success message after save

- **WHEN** a content manager updates settings and submits the form
- **THEN** a success flash message "Settings updated successfully" is displayed
- **AND** the page redirects back to `/admin/site-settings`

### Requirement: Default Settings Seeded

The system SHALL seed default settings with current hardcoded values from the templates during database migration.

#### Scenario: Contact info seeded from current values

- **WHEN** the settings seeder runs
- **THEN** the following settings are created:
  - `contact_email`: `info@kelasdigital.com`
  - `contact_phone`: `+62 123 456 7890`
  - `contact_address`: `Bandung, Jawa Barat, Indonesia`

#### Scenario: Social media URLs seeded from current values

- **WHEN** the settings seeder runs
- **THEN** the following social settings are created:
  - `social_facebook`: `https://www.facebook.com/beautyversitydotid`
  - `social_twitter`: `https://x.com/beautyversityid`
  - `social_instagram`: `https://www.instagram.com/beautyversity_id`
  - `social_youtube`: `https://www.youtube.com/@beautyversitydotid`
  - `social_tiktok`: `null` (new platform)
  - `social_whatsapp`: `null` (new platform)
  - `social_linkedin`: `null` (new platform)

### Requirement: Permission for Settings Management

The system SHALL define a `manage site settings` permission assigned to Content-Manager, Admin, and Super-Admin roles.

#### Scenario: Content-Manager can manage settings

- **WHEN** a user with the Content-Manager role attempts to access settings
- **THEN** the user has the `manage site settings` permission
- **AND** can view and update settings

#### Scenario: Student cannot manage settings

- **WHEN** a user with the Student role attempts to access settings
- **THEN** the user lacks the `manage site settings` permission
- **AND** receives a 403 error on `/admin/site-settings`

#### Scenario: Super-Admin implicitly granted all permissions

- **WHEN** a Super-Admin accesses `/admin/site-settings`
- **THEN** the system grants access via `Gate::before()` check in `AuthServiceProvider`
- **AND** the Super-Admin can manage all settings

### Requirement: Header and Footer Template Updates

The system SHALL replace hardcoded contact and social media values in `resources/views/layouts/app.blade.php` with dynamic values from `$settings`.

#### Scenario: Header address uses dynamic setting

- **WHEN** the header top bar is rendered (line 35-36 in `app.blade.php`)
- **THEN** the address displays `{{ $settings['contact_address'] ?? 'Bandung, Jawa Barat, Indonesia' }}`
- **AND** uses fallback if setting missing

#### Scenario: Header social links use dynamic settings

- **WHEN** the header top bar social icons are rendered (lines 38-44)
- **THEN** each icon link uses the corresponding `$settings['social_*']` value
- **AND** only renders if the setting is not empty

#### Scenario: Footer contact info uses dynamic settings

- **WHEN** the footer contact section is rendered (lines 346-366)
- **THEN** email, phone, and address display dynamic values from `$settings`

#### Scenario: Footer social links use dynamic settings

- **WHEN** the footer social section is rendered (lines 372-389)
- **THEN** each platform icon uses the corresponding `$settings['social_*']` value
- **AND** only renders if the setting is not empty

