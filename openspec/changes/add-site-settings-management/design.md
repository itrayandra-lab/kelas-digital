# Design: Site Settings Management

## Context

Beautyversity currently hardcodes contact information and social media links in Blade templates (`resources/views/layouts/app.blade.php`). This requires developer intervention for simple content updates. The platform has an established admin panel pattern with role-based access control using Spatie Laravel Permission, and uses view composers for injecting shared data (see `CategoryComposer` in `AppServiceProvider`).

**Constraints:**
- Must support non-technical content managers
- Must not break existing header/footer display
- Must maintain current RBAC pattern (permissions via Spatie)
- Should be performant (site-wide data accessed on every page load)

**Stakeholders:**
- Content managers: Primary users of settings management UI
- Developers: Maintainers of settings infrastructure
- End users: Benefit from accurate, up-to-date contact information

## Goals / Non-Goals

**Goals:**
- Enable content managers to update contact and social media info via admin UI
- Support current 4 platforms + add TikTok, WhatsApp Business, LinkedIn (7 total)
- Cache settings for performance (avoid DB hit on every request)
- Provide extensible pattern for future site-wide settings (meta tags, analytics IDs, etc.)
- Maintain clean separation: model (data), composer (injection), view (display)

**Non-Goals:**
- Advanced settings versioning or history tracking (could be added later)
- Multi-language/localized contact info (single instance for now)
- Settings categories or grouping UI (flat key-value sufficient for initial scope)
- Newsletter subscription implementation (separate proposal)

## Decisions

### Decision 1: Key-Value Pattern with Settings Table

**Chosen Approach:** Single `settings` table with columns: `id`, `key` (unique), `value` (text), `type` (enum: string, integer, boolean, array), `timestamps`.

**Why:**
- **Extensibility**: Add new settings without migrations (just insert new key-value pairs)
- **Laravel-idiomatic**: Pattern used by packages like `spatie/laravel-settings`
- **Type-safe**: `type` column enables automatic casting in model accessor
- **Queryable**: Can filter by key, unlike JSON blob approach

**Alternatives Considered:**
1. **Dedicated columns table** (`site_settings` with `email`, `phone`, `facebook_url`, etc.)
   - ❌ Requires migration for every new setting
   - ✅ Type-safe at database level
   - ❌ Not scalable for diverse setting types (booleans, arrays, etc.)

2. **JSON config field** (single row with JSON column for all settings)
   - ❌ Poor queryability and validation
   - ❌ No per-setting type casting
   - ✅ Single DB row (slight perf advantage, negligible with caching)

3. **Config files** (`config/site.php`)
   - ❌ Requires developer to edit and deploy
   - ❌ No UI for content managers
   - ✅ Version controlled, fast access

**Trade-off:** Key-value requires model accessor logic for type casting, but gains massive flexibility and UI manageability.

### Decision 2: View Composer for Global Injection

**Chosen Approach:** Create `SettingsComposer` that binds `$settings` array to `layouts.app` view, similar to existing `CategoryComposer` pattern.

**Why:**
- Consistency with existing `CategoryComposer` pattern (already in `AppServiceProvider`)
- Automatic injection on every page load (header/footer need this data)
- Centralized caching logic in composer (cache `$settings` array for 1 hour)
- Clean separation: composer handles data prep, views just display

**Alternatives Considered:**
1. **Helper function** (`setting('key')`)
   - ✅ Convenient call syntax
   - ❌ Requires global helper file or facade
   - ❌ Harder to batch cache (N queries if called N times per request)

2. **Middleware** (inject into request or view)
   - ✅ Automatic injection
   - ❌ Runs on every route, even API/redirects (wasteful)
   - ❌ Less explicit than composer binding to specific layout

3. **Direct DB queries in views** (`Setting::get('email')`)
   - ❌ N+1 queries (disaster for 10+ settings)
   - ❌ No caching unless manually added everywhere

**Trade-off:** Composer adds one extra registration in `AppServiceProvider`, but provides best performance and maintainability.

### Decision 3: Flat Admin UI (Single Form Page)

**Chosen Approach:** Single page at `/admin/site-settings` with tabbed sections (Contact Info, Social Media) using Alpine.js tabs.

**Why:**
- Simple mental model: all settings visible at once
- No pagination/navigation overhead for ~10 settings
- Follows existing admin panel pattern (similar to `articles/edit`)
- Tabs organize logically without adding complexity
- Single `update()` endpoint handles all changes atomically

**Alternatives Considered:**
1. **Separate pages per category** (`/admin/site-settings/contact`, `/admin/site-settings/social`)
   - ❌ Extra routes and navigation overhead
   - ❌ Harder to see "full picture" of site config
   - ✅ Cleaner URLs (negligible benefit for admin panel)

2. **Table with inline editing** (like hero slider index)
   - ❌ Poor UX for text input (address, email need full forms)
   - ✅ Works well for simple on/off toggles (not applicable here)

3. **Modal-based editing** (click key to edit in modal)
   - ❌ Extra complexity (modals, state management)
   - ❌ Slower workflow (open modal, edit, close, repeat)

**Trade-off:** Single-page form can feel long if settings grow to 50+, but current scope (10 settings) fits comfortably.

### Decision 4: Optional Social Media Fields

**Chosen Approach:** All social media URL fields are `nullable` in DB. If field is empty/null, corresponding link does not render in header/footer.

**Why:**
- Beautyversity may not be active on all 7 platforms immediately
- Clean UI: no broken links or placeholder URLs
- Flexibility: can add/remove platforms without code changes

**Implementation:**
```blade
@if(!empty($settings['facebook_url']))
    <a href="{{ $settings['facebook_url'] }}" ...>
        <i class="fab fa-facebook-f"></i>
    </a>
@endif
```

**Validation:** Basic URL format (`url` validation rule), but no platform-specific regex (e.g., enforcing `facebook.com` domain). This allows flexibility (e.g., redirects via short URLs like `bit.ly/beautyversity-fb`) while preventing typos via basic URL structure check.

## Data Model

### Settings Table Schema

```sql
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT NULL,
    type ENUM('string', 'integer', 'boolean', 'array') DEFAULT 'string',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Index:** Unique index on `key` for fast lookups.

### Setting Keys

**Contact Info:**
- `contact_email` (string): info@kelasdigital.com
- `contact_phone` (string): +62 123 456 7890
- `contact_address` (string): Bandung, Jawa Barat, Indonesia

**Social Media URLs (all nullable):**
- `social_facebook` (string): https://facebook.com/beautyversitydotid
- `social_twitter` (string): https://x.com/beautyversityid
- `social_instagram` (string): https://instagram.com/beautyversity_id
- `social_youtube` (string): https://youtube.com/@beautyversitydotid
- `social_tiktok` (string): null (new)
- `social_whatsapp` (string): null (new, format: https://wa.me/62123456789)
- `social_linkedin` (string): null (new)

**Type field:** All current settings are `string` type. Future settings could use `integer` (max_upload_size), `boolean` (maintenance_mode), or `array` (serialized JSON for complex config).

## Caching Strategy

**Pattern:** Cache settings array for 1 hour, clear on update.

```php
// In SettingsComposer
public function compose(View $view)
{
    $settings = Cache::remember('site_settings', 3600, function () {
        return Setting::pluck('value', 'key')->toArray();
    });

    $view->with('settings', $settings);
}

// In SiteSettingsController@update
Cache::forget('site_settings');
```

**Why 1 hour TTL:**
- Settings change infrequently (days/weeks between updates)
- 1 hour balances freshness vs. performance
- Manual cache clear on update provides immediate feedback to content managers

**Alternative:** Forever cache with manual invalidation only. Rejected because accidental cache issues harder to debug (requires manual `php artisan cache:clear`).

## Risks / Trade-offs

### Risk: Cache Staleness in Multi-Server Environments

**Problem:** If deployed on multiple web servers with separate cache layers (file-based cache), updating settings on one server won't invalidate cache on others for up to 1 hour.

**Mitigation:**
- Use centralized cache driver (Redis/Memcached) in production (recommended)
- Or: Reduce TTL to 5 minutes in production config
- Or: Trigger cache clear via deployment hook

**Decision:** Document in deployment guide to use Redis cache in production. Development (single server) unaffected.

### Risk: Missing Validation on Setting Keys

**Problem:** Typo in seeder or manual DB insert creates unused setting (`contact_emial` instead of `contact_email`).

**Mitigation:**
- Use constants or enum class for setting keys: `Setting::KEY_CONTACT_EMAIL`
- Form validates against known keys (dropdown or hidden field, not free text)
- Admin UI only exposes predefined settings (no arbitrary key creation yet)

**Decision:** Phase 1 (this proposal) uses predefined form fields. Future enhancement could add "custom settings" with key validation.

### Trade-off: Performance vs. Flexibility

**Choice:** Key-value pattern adds minor overhead (model accessor for type casting, cache serialization) vs. config file or dedicated columns.

**Impact:** Negligible with caching (1 DB query per hour). Benefits (extensibility, UI manageability) far outweigh ~5ms overhead.

## Migration Plan

### Phase 1: Database & Model (No UI Yet)

1. Create migration for `settings` table
2. Create `Setting` model with casting
3. Seed with current hardcoded values (no changes to views yet)

**Validation:** Run `php artisan tinker` and query `Setting::where('key', 'contact_email')->value('value')` to confirm data.

### Phase 2: Admin UI

1. Create controller, routes, views for `/admin/site-settings`
2. Add permission to `RolePermissionSeeder` and run migration
3. Test CRUD operations, verify cache clears on update

**Validation:** Log in as content-manager, edit email, verify change appears in DB and cache cleared.

### Phase 3: Frontend Integration

1. Create `SettingsComposer` and register in `AppServiceProvider`
2. Update `layouts/app.blade.php` header (lines 35-44) and footer (lines 343-391) to use `$settings` array
3. Remove hardcoded values from Blade

**Validation:** Load homepage, inspect source to confirm dynamic values, edit setting in admin and verify live change.

### Rollback Plan

If critical bug found after deployment:

1. **Immediate:** Revert commits for Phase 3 (frontend integration) - restores hardcoded values
2. **Optional:** Keep admin UI live (Phase 2) for content managers to prep data
3. **Hotfix:** Fix bug, redeploy Phase 3

No data loss risk (settings table persists even if code reverted).

## Open Questions

1. **Should settings have descriptions in DB?** (e.g., `description` column: "Contact email displayed in footer")
   - **Decision:** Not in Phase 1. Add if content managers request tooltips in UI.

2. **Do we need audit log for settings changes?** (who changed what, when)
   - **Decision:** Not in Phase 1. Use Laravel model events + logging package if requested later.

3. **What happens if setting key doesn't exist?** (e.g., typo in view: `$settings['contact_emial']`)
   - **Decision:** Blade fails silently (null), no error. Document to use `$settings['contact_email'] ?? 'default@example.com'` for safety.

4. **Should we add validation preview in admin UI?** (test URL reachable before saving)
   - **Decision:** Not in Phase 1. Basic URL validation sufficient. Could add JavaScript fetch test later.
