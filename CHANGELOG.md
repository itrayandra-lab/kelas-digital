# Changelog

All notable changes to Beautyversity platform will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added - Role & Permission Management System (2025-11-12)

#### Core Feature
- **Role & Permission CRUD**: Super-Admin can now manage roles and permissions through a comprehensive admin interface
  - Full CRUD operations for roles at `/admin/roles` with soft delete support
  - Interactive permission assignment matrix with Alpine.js (40+ granular permissions)
  - Complete activity audit trail at `/admin/activity-log` tracking all role/permission changes
  - Protected role system preventing accidental deletion of critical roles (super-admin, student)
  - Sidebar menu integration in System Management section

#### Backend Infrastructure
- **Database Schema Changes**:
  - Migration `2025_11_12_005407`: Added `soft_deletes` and `description` columns to roles table
  - Index on `deleted_at` for query performance optimization
  - Unique index on `hero_slider_order` for data integrity

- **Authentication & Authorization**:
  - New permission: `manage roles and permissions` (assigned to Super-Admin only)
  - Protected roles defined in `config/authorization.php`: ['super-admin', 'student']
  - Critical permissions mapping prevents accidental revocation of essential permissions
  - Permission-based route middleware: `Route::middleware('can:manage roles and permissions')`

- **Activity Logging**:
  - Spatie Laravel Activity Log integration for complete audit trail
  - Logs all role CRUD operations with causer and timestamp
  - Detailed permission change tracking (permissions_added, permissions_removed arrays)
  - User-based filtering and date range filtering in activity log
  - Expandable activity details with Alpine.js collapse component

- **Model Enhancements**:
  - Custom `Role` model extending Spatie\Permission\Models\Role
  - Added `SoftDeletes` trait for recoverable deletions
  - Added `description` field to fillable array
  - `User` model enhanced with `CausesActivity` trait for activity logging

- **Service Layer**:
  - `RoleService`: Business logic layer with 4 key methods:
    - `getPermissionGroups()`: Groups 40+ permissions into 10 categories
    - `isProtectedRole()`: Validates against protected roles list
    - `getCriticalPermissions()`: Returns required permissions for protected roles
    - `validatePermissionUpdate()`: Prevents removal of critical permissions

- **Form Request Validation**:
  - `StoreRoleRequest`: Validates role creation with unique name constraint
  - `UpdateRoleRequest`: Validates role updates with permission array validation
  - Authorization checks integrated into form requests

- **Controller Implementation**:
  - `RoleController`: Complete CRUD with 6 methods:
    - `index()`: Lists all roles with user counts
    - `create()`: Shows role creation form
    - `store()`: Creates role with activity logging
    - `edit()`: Shows tabbed edit interface (Details | Permissions)
    - `update()`: Updates role with permission sync and validation
    - `destroy()`: Soft deletes role with validation (blocks if users assigned)
  - `ActivityLogController`: Activity log display with filters
    - Supports filtering by causer, date range, and description keyword
    - Paginated display (20 activities per page)

- **New Routes**:
  - `GET /admin/roles` → Role index page
  - `GET /admin/roles/create` → Role creation form
  - `POST /admin/roles` → Store new role
  - `GET /admin/roles/{role}/edit` → Edit role form
  - `PUT /admin/roles/{role}` → Update role
  - `DELETE /admin/roles/{role}` → Soft delete role
  - `GET /admin/activity-log` → Activity log with filters

- **Configuration Files**:
  - `config/authorization.php`: Centralized configuration for protected roles and critical permissions
  - Published `config/activitylog.php` from Spatie package

#### Frontend Components
- **Role Index Page** (`resources/views/admin/roles/index.blade.php`):
  - Responsive table layout with 5 columns: Role Name, Description, Users, Last Modified, Actions
  - "Protected" badge for system roles (yellow background)
  - Delete button conditionally hidden for protected roles
  - User count display with `withCount('users')` optimization
  - Link to activity log at bottom of page
  - Success/error flash messages with color-coded alerts

- **Role Create Form** (`resources/views/admin/roles/create.blade.php`):
  - Simple two-field form: name (required) and description (optional)
  - Back button to roles index
  - Inline validation error display per field
  - Note: "You can assign permissions after creating the role"

- **Role Edit Interface** (`resources/views/admin/roles/edit.blade.php`):
  - Tabbed interface with Alpine.js (Details | Permissions)
  - Details tab: editable name and description fields
  - Permissions tab: includes permission matrix component
  - Yellow warning banner for protected roles
  - Form footer with Save/Cancel buttons in gray-50 background

- **Permission Matrix Component** (`resources/views/admin/roles/_permission-matrix.blade.php`):
  - Alpine.js reactive component with `selectedPermissions` array
  - 10 permission categories with collapsible sections:
    - Course Management, Lesson Management, Article Management
    - Category Management, Tag Management, User Management
    - Enrollment Management, Payment Management, Student Features, System Management
  - "Select All" / "Deselect All" buttons per category
  - Critical permissions disabled for protected roles with visual styling
  - Responsive grid: 1 col mobile, 2 cols sm, 3 cols md
  - Yellow info box for protected role warnings

- **Activity Log Timeline** (`resources/views/admin/activity-log/index.blade.php`):
  - Filter card with 4 inputs: User dropdown, Date from/to, Action keyword
  - Timeline layout with user avatars (ui-avatars.com)
  - Collapsible activity details showing permissions added/removed
  - Green badges for added permissions, red for removed
  - Relative timestamps with `diffForHumans()`
  - Pagination with Laravel links
  - Back button to roles index

#### User Experience
- **Admin Panel Navigation**:
  - Added "Roles & Permissions" link in System Management section (sidebar)
  - Icon: `fas fa-user-shield` (shield icon)
  - Active state for both `admin.roles.*` and `admin.activity-log.*` routes
  - Permission-gated display: `@can('manage roles and permissions')`
  - Positioned between "Manage Users" and "Manage Payments"

- **Design Consistency**:
  - All views follow existing admin design patterns
  - Headers use `@section('title')` for layout title display
  - No duplicate headings in content area
  - Back buttons styled consistently with existing pages
  - Alert boxes use standardized color scheme (green-50, red-50, yellow-50)

- **Validation Feedback**:
  - Inline error messages per field with red-600 text
  - Flash messages for success/error states
  - Unique role name validation with clear error messages
  - Protected role deletion blocked with informative error
  - User assignment validation prevents orphaned users

#### Security & Data Integrity
- **Protected Role System**:
  - Super-Admin role: Cannot delete, cannot revoke "manage roles and permissions"
  - Student role: Cannot delete, cannot revoke "enroll in courses"
  - Validation at both controller and service layer (defense-in-depth)

- **Soft Delete Strategy**:
  - Roles soft deleted instead of hard deleted
  - Relationships preserved for audit trail
  - "Show Deleted" filter support in index method
  - Restoration capability (not implemented in UI yet)

- **Activity Logging**:
  - All role CRUD operations logged with full context
  - Permission changes tracked with before/after arrays
  - Causer (authenticated user) recorded for accountability
  - Timestamps for temporal analysis
  - Properties stored as JSON for detailed audit

- **Authorization Flow**:
  - Route middleware: `can:manage roles and permissions`
  - Form request authorization checks
  - Gate checks in service layer
  - Super-Admin bypass via `Gate::before()` in AuthServiceProvider

#### Performance Optimizations
- **Query Optimization**:
  - `withCount('users')` prevents N+1 queries on role index
  - Eager loading: `with(['causer', 'subject'])` on activity log
  - Index on `deleted_at` for soft delete queries
  - Pagination (20 per page) on activity log

- **Permission Grouping**:
  - Static grouping logic in RoleService
  - Regex-based categorization by permission name patterns
  - No additional database queries for grouping

#### Technical Notes
- **Package Dependencies**:
  - `spatie/laravel-permission`: Already installed (RBAC foundation)
  - `spatie/laravel-activitylog`: Newly installed (audit trail)
  - Alpine.js: Already available (interactive components)

- **Permission Categories**: 10 logical groups based on naming conventions
  - Pattern matching: `* courses` → Course Management, `manage *` → System Management
  - Fallback: "Other" category for ungrouped permissions

- **Extensibility**:
  - Permission registry immutable (no UI for permission CRUD)
  - New permissions added via `RolePermissionSeeder` modification
  - Category grouping automatically handles new permissions via regex

#### Migration Instructions
1. Install Spatie Activity Log: `composer require spatie/laravel-activitylog`
2. Publish migrations: `php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"`
3. Publish config: `php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"`
4. Run migrations: `php artisan migrate`
5. Update permissions: `php artisan db:seed --class=RolePermissionSeeder`
6. Clear caches: `php artisan config:clear && php artisan route:clear`
7. Access as Super-Admin: Navigate to `/admin/roles`

#### OpenSpec Documentation
- **Archived Change**: `openspec/changes/archive/2025-11-12-add-role-permission-crud/`
- **Authoritative Spec**: `openspec/specs/authorization-management/spec.md`
- **Requirements Coverage**: 6 main requirements with 30+ test scenarios
  - Role CRUD Operations
  - Permission Assignment Matrix
  - Activity Audit Trail
  - Protected Role System
  - Role Soft Delete Management
  - Permission Registry Immutability

### Added - Dynamic Site Settings Management (2025-11-11)

#### Core Feature
- **Site Settings Management**: Content managers can now dynamically update contact information and social media links via admin panel
  - Centralized management page at `/admin/site-settings` with tabbed interface (Contact Info | Social Media)
  - Supports 7 social media platforms: Facebook, Twitter/X, Instagram, YouTube, TikTok, WhatsApp Business, LinkedIn
  - All social media fields optional - only filled URLs render icons on frontend
  - Real-time updates across entire site (header and footer)
  - No code deployment needed for contact info changes

#### Backend Infrastructure
- **Database Schema Changes**:
  - Migration `2025_11_11_232101`: Created `settings` table with key-value pattern
  - Columns: `id`, `key` (string, unique), `value` (text, nullable), `type` (enum: string, integer, boolean, array), `timestamps`
  - Unique index on `key` column for fast lookups

- **Model Enhancements** (`app/Models/Setting.php`):
  - Key-value storage with automatic type casting based on `type` column
  - `Setting::get($key, $default)`: Static helper for easy retrieval
  - Automatic cache clearing on model `updated` and `deleted` events
  - Accessor/mutator pair for type serialization (JSON arrays, boolean casting, etc.)

- **View Composer Pattern**:
  - `SettingsComposer`: Injects `$settings` array into all `layouts.app` views
  - Cache-first approach: Settings cached for 1 hour (3600 seconds)
  - Cache automatically invalidated on settings update
  - Registered in `AppServiceProvider` alongside existing `CategoryComposer`

- **Permission System**:
  - New permission: `manage site settings` assigned to Content-Manager, Admin, Super-Admin roles
  - Permission-based route middleware: `Route::middleware('can:manage site settings')`
  - Students and Instructors blocked from accessing settings (403 Forbidden)

- **Controller Updates**:
  - `SiteSettingsController`: New admin controller with 2 methods
    - `index()`: Display settings management page with pre-filled form
    - `update()`: Validate and save all settings, clear cache, redirect with success message
  - Form validation: Email format check, URL validation for social media, required fields for contact info

- **New Routes**:
  - `GET /admin/site-settings` → Settings management page
  - `PUT /admin/site-settings` → Bulk update all settings

- **New Seeder**:
  - `SettingsSeeder`: Populates default values from currently hardcoded data
    - Contact: info@kelasdigital.com, +62 123 456 7890, Bandung address
    - Social: Existing Facebook, Twitter, Instagram, YouTube URLs
    - New platforms (TikTok, WhatsApp, LinkedIn) seeded as null

- **New Views**:
  - `resources/views/admin/site-settings/index.blade.php`: Tabbed form interface
    - Alpine.js for tab switching (no page reload)
    - Contact Info tab: email, phone, address fields
    - Social Media tab: 7 URL input fields with platform icons
    - Success flash message display
    - Form validation error display per field

#### Frontend Integration
- **Header Top Bar** (`resources/views/layouts/app.blade.php`):
  - Dynamic address display with fallback values
  - Conditional social media icon rendering: `@if(!empty($settings['social_*']))`
  - Added support for TikTok, WhatsApp, LinkedIn icons
  - All platforms use target="_blank" for new tab behavior

- **Footer Contact Section**:
  - Dynamic email, phone, address display with fallback values
  - Preserves icon structure and styling
  - Uses `$settings` array injected by SettingsComposer

- **Footer Social Section**:
  - Conditional rendering for all 7 platforms
  - Only renders icon links if URL is filled (prevents broken links)
  - Consistent hover states and transition effects

#### User Experience
- **Admin Panel Navigation**:
  - Added "Site Settings" link in Content Management section (sidebar)
  - Icon: `fas fa-cog` (gear icon)
  - Active state highlighting when on settings page
  - Permission-gated display: `@can('manage site settings')`

- **Tabbed Interface Design**:
  - Clean separation: Contact info vs Social media
  - Active tab styling: Primary color border and text
  - All fields pre-filled with current values

- **Validation Feedback**:
  - Inline error messages per field
  - Email format validation, URL validation for social media
  - Success banner: "Settings updated successfully"

#### Security & Performance
- **Caching Strategy**:
  - Settings cached for 1 hour to minimize database queries
  - Cache cleared automatically on update via model events
  - Performance: ~0 queries per page load (cache hit), 1 query per hour (cache miss)

- **Access Control**:
  - Route protected by `can:manage site settings` middleware
  - Super-Admin gets implicit access via `Gate::before()`
  - Fail-safe: Fallback values if settings missing

#### Technical Notes
- **Settings Storage**: 10 default settings seeded (3 contact + 7 social)
- **Cache TTL**: 1 hour (3600 seconds) balances freshness vs performance
- **Extensible Pattern**: Key-value table allows adding settings without migrations
- **Type Safety**: Enum column for type field, supports string/integer/boolean/array

#### Migration Instructions
1. Run migration: `php artisan migrate`
2. Run seeder: `php artisan db:seed --class=SettingsSeeder`
3. Update permissions: `php artisan db:seed --class=RolePermissionSeeder`
4. Clear caches: `php artisan config:clear && php artisan route:clear`
5. Access settings: Login as content-manager → Navigate to `/admin/site-settings`

### Added - Hero Slider Manual Curation (2025-11-11)

#### Core Feature
- **Hero Slider Management**: Content managers can now manually select and order articles for the homepage hero slider
  - Add/remove articles via admin panel with explicit order control (1-5)
  - Dedicated management page at `/admin/hero-slider` for overview and quick removal
  - Checkbox in article create/edit forms for quick inclusion
  - Automatic fallback to latest published articles when <5 manually curated
  - Stale content warning after 30 days without update

#### Backend Infrastructure
- **Database Schema Changes**:
  - Migration `2025_11_11_160537`: Added `hero_slider_order` column (integer, nullable, unique, indexed)
  - Positioned after `recommended_at` for logical grouping with curation fields

- **Model Enhancements** (`app/Models/Article.php`):
  - `scopeInHeroSlider()`: Filters articles by non-null `hero_slider_order`, orders ASC
  - Model event: Auto-removes from slider when status changes to draft/scheduled
  - Added to fillable array and casts as integer

- **Controller Updates**:
  - `HomeController::index()`: Hybrid query (manual + fallback logic)
    - Fetches manually curated articles first
    - Fills remaining slots (up to 5) with latest published articles
    - Excludes already-shown articles from fallback
  - `HeroSliderController`: New admin controller with 3 methods:
    - `index()`: Displays current slider + stale warning
    - `update()`: Bulk reorder (uses DB transaction)
    - `remove()`: Quick article removal
  - `ArticleController`: Added `hero_slider_order` validation with unique constraint

- **New Routes**:
  - `GET /admin/hero-slider` → Hero slider management page
  - `POST /admin/hero-slider` → Bulk update slider order
  - `DELETE /admin/hero-slider/{article}` → Remove article from slider

- **New Views**:
  - `resources/views/admin/hero-slider/index.blade.php`: Management interface
  - Updated article forms with hero slider checkbox + order input (Alpine.js toggle)

#### User Experience
- **Admin Panel Navigation**: Added "Hero Slider" link in Content Management section
- **Validation**: Unique order constraint with clear error message
- **Permission**: Uses existing `manage articles` permission (content-manager role)
- **Fail-Safe Design**: Slider never empty (automatic fallback ensures 5 articles always shown)

#### Security & Data Integrity
- Auto-cleanup: Unpublished articles automatically removed via model events
- Transaction-wrapped updates prevent duplicate orders
- Frontend always filters by published status (defense-in-depth)

### Added - Homepage Courses Section (2025-11-11)

#### Core Feature
- **Featured Courses Section**: New dedicated section on homepage (position #5, between Recommendations and Trending)
  - Manual course curation via `is_featured` flag with `featured_at` timestamp
  - Displays up to 4 featured courses in responsive grid (1-col mobile, 2-col tablet, 4-col desktop)
  - Consistent card design matching article cards with course-specific metadata
  - "Browse All Courses" CTA button (desktop header, mobile footer)

#### Course Display Components
- **Course Cards**: Purpose-built display with relevant metadata
  - "COURSE" badge (uppercase, primary-600) instead of category
  - Instructor name and level (Beginner/Intermediate/Advanced)
  - Prominently displayed price (formatted Rp X.XXX or "Free" for price=0)
  - Conditional enrollment count (only shows if >0 to avoid negative signal)
  - Hover effects: shadow elevation and title color change
  - Full card clickable, links to `/courses/{slug}`

#### Backend Infrastructure
- **Database Schema Changes**:
  - Migration `2025_11_11_145312`: Added `is_featured`, `featured_at` to courses table
  - Index on `is_featured` for query performance optimization

- **Model Enhancements**:
  - `Course::scopeFeatured()`: Filters courses by `is_featured=true`, orders by `featured_at` DESC
  - Added `is_featured`, `featured_at` to fillable array
  - Datetime casting for `featured_at` field

- **Controller Updates**:
  - `HomeController::index()`: Added featured courses query with eager loading
  - Eager loads `category` and `enrollments` relationships to prevent N+1 queries
  - Uses `withCount('enrollments')` for efficient enrollment count calculation
  - Limits to 4 courses maximum
  - `CourseController::index()`: New method for course listing page
  - Paginates all courses (12 per page) with category and enrollment count

- **New Routes**:
  - `GET /courses` → Course index page with pagination
  - Links homepage "Browse All Courses" button to dedicated courses listing

- **New Views**:
  - `resources/views/course/index.blade.php`: Course listing page with consistent card design

#### Frontend Design
- **Responsive Grid Layout**: Mobile-first approach
  - Mobile (0-767px): 1 column, gap-4 (16px)
  - Tablet (768-1023px): 2 columns, gap-6 (24px)
  - Desktop (1024px+): 4 columns, gap-8 (32px)

- **Visual Consistency**: Follows Beautyversity Style Guide
  - Section background: `bg-gray-50` (alternating pattern with article sections)
  - Section padding: `py-16 md:py-24` (64px/96px vertical)
  - Card styling: `bg-white rounded-lg border border-gray-100 hover:shadow-lg`
  - Dusty rose theme: primary-600 for badges/price, primary-700 for hover states
  - Typography: text-lg bold for titles, text-sm for metadata, text-xl bold for price

- **Edge Case Handling**:
  - Section hidden if zero featured courses (graceful degradation)
  - Free courses display "Free" instead of "Rp 0"
  - Long titles truncate with `line-clamp-2` ellipsis
  - Missing thumbnails show placeholder image
  - Enrollment count conditionally rendered (prevents "0 enrolled" display)

#### Developer Experience
- **OpenSpec Proposal**: Full spec-driven development documentation
  - `openspec/changes/add-homepage-courses-section/proposal.md`: Strategic overview
  - `openspec/changes/add-homepage-courses-section/design.md`: Architecture decisions and rationale
  - `openspec/changes/add-homepage-courses-section/tasks.md`: Detailed implementation checklist
  - `openspec/specs/course-homepage-showcase/spec.md`: Requirements with test scenarios

### Changed

- **Homepage Layout**: Updated from 7-section to 8-section architecture
  - Inserted "Featured Courses" between Recommendations (#4) and Trending (#5→#6)
  - Other sections shifted: Featured Category (#6→#7), More Articles (#7→#8)

- **Content Strategy**: Evolved from article-only to hybrid content + education showcase
  - Content pyramid now: Free articles → Curated articles → **Premium courses** → More content
  - Natural funnel: Article readers discover courses mid-scroll (optimal conversion point)

### Technical Notes

- **Performance**: Query optimization with eager loading and withCount
  - 3 queries maximum for courses section (courses + categories + enrollment counts)
  - No N+1 queries (verified via Laravel Debugbar)
  - Page load time increase <50ms (acceptable threshold)

- **Scalability**: Manual curation approach
  - Admin control via `is_featured` flag (consistent with article recommendations)
  - Future enhancement: Algorithmic fallback if <4 featured courses

- **UX Pattern**: Consistent with article sections
  - Same hover transitions (150ms default)
  - Same card structure (image + content padding)
  - Different metadata (course-specific: instructor, level, price)

### Migration Instructions

1. Run migration: `php artisan migrate`
2. Feature 2-4 courses via admin panel or tinker:
   ```php
   Course::limit(4)->update(['is_featured' => true, 'featured_at' => now()]);
   ```
3. Clear caches: `php artisan config:cache && php artisan view:cache`
4. Verify homepage displays courses section

---

### Added - Homepage Media-First Redesign (2025-11-10)

#### Core Features
- **9-Section Homepage Architecture**: Transformed from course-centric to article-centric layout
  - Hero Slider with Splide.js (5 featured articles)
  - Latest Article (6 articles in 2-column asymmetric layout)
  - Terpopuler (4 all-time most-viewed articles with dark theme)
  - Recommendation for You (4 manually curated articles)
  - Trending (6 articles from last 30 days)
  - Featured Category (3 articles from one featured category with dynamic theming)
  - More Articles (3 additional articles with smart exclusion)

#### Article Curation System
- **Recommendation System**: Manual article curation by content managers
  - `is_recommended` boolean flag with `recommended_at` timestamp
  - Admin checkbox: "Recommend this article" in create/edit forms
  - Homepage displays top 4 most recent recommended articles
  - Dedicated `/recommendations` page with pagination (12 per page)
  - Soft limit approach: no validation errors, always takes most recent 4

- **View Tracking**: Article popularity metrics
  - `views_count` column with automatic increment on article view
  - Increment without updating `updated_at` timestamp
  - Powers Terpopuler (all-time) and Trending (30-day) sections

- **Article Query Scopes**: New Eloquent scopes for content filtering
  - `scopeRecommended()`: Articles flagged for recommendation
  - `scopePopular()`: All-time most-viewed articles
  - `scopeTrending()`: Most-viewed in last 30 days
  - `scopeFeatured()`: Category featured section query

#### Category Featured Section
- **Featured Category System**: One category spotlight at a time
  - `is_featured_section` boolean with `featured_at` timestamp on categories
  - Dynamic theme colors via `getThemeColorAttribute()` accessor
  - Color mapping: SKINCARE→pink-600, MYTHBUSTER→purple-600, HAIRCARE→blue-600,
    DECORATIVE→rose-600, BAHANAKTIF→green-600, MENZONE→indigo-600,
    PERSONALCARE→teal-600, BEAUTYLIFE→amber-600
  - Section displays 3 most-viewed articles from featured category
  - Conditional rendering: only shows if category is featured

#### Frontend Enhancements
- **Splide.js Integration**: Modern carousel/slider library
  - Hero slider with autoplay (5s interval)
  - Pause on hover/focus
  - Arrow navigation and dot indicators
  - Loop mode for seamless transitions

- **Smart Content Exclusion**: Prevents duplicate articles across homepage sections
  - Collects all displayed article IDs from hero, latest, popular, trending, recommended, featured
  - "More Articles" section excludes all previously shown content

- **Responsive Design**: Mobile-first approach
  - Two-column asymmetric layouts (3 horizontal + 3 vertical cards)
  - 4-column grids collapse to 2-column (tablet) and 1-column (mobile)
  - Full-width feature cards with image overlays
  - Dark-themed sections with proper contrast

#### Backend Infrastructure
- **Database Schema Changes**:
  - Migration `2025_11_10_000001`: Added `views_count`, `is_recommended`, `recommended_at` to articles
  - Migration `2025_11_10_000002`: Added `is_featured_section`, `featured_at`, `theme_color` to article_categories

- **New Controllers**:
  - `RecommendationController`: Handles `/recommendations` dedicated page
  - Updated `HomeController`: 9-section query optimization with smart exclusion
  - Updated `Admin\ArticleController`: Handles recommendation checkbox state

- **New Routes**:
  - `GET /recommendations` → Full list of recommended articles with pagination

- **Model Enhancements**:
  - `Article::incrementViews()`: Direct query builder for view tracking
  - `ArticleCategory::getThemeColorAttribute()`: Dynamic color mapping
  - Multiple new Eloquent scopes for content filtering

#### Developer Experience
- **Documentation**: Comprehensive CLAUDE.md updates
  - Homepage architecture patterns
  - Article curation system workflows
  - Splide.js configuration
  - Admin panel integration guide
  - Query optimization examples

### Changed

- **HomeController**: Complete rewrite from 3 queries to 9 optimized section queries
- **home.blade.php**: Total redesign with media-first layout (replaced course-heavy design)
- **Admin Article Forms**: Added recommendation checkbox in both create and edit views
- **package.json**: Added `@splidejs/splide` dependency
- **app.css**: Imported Splide.js styles
- **app.js**: Added Splide initialization on DOMContentLoaded

### Technical Notes

- **Content Pyramid Strategy**: 60% light content, 30% educational, 10% course previews
- **Performance**: Eager loading with `->with('categories', 'tags')` on all queries
- **Scalability**: Soft limit recommendation system prevents admin frustration
- **SEO**: Dedicated recommendation page for better content discoverability
- **UX**: Autoplay slider with pause-on-hover for accessibility

### Requirements

- PHP >= 8.2.0
- Laravel 12.x
- Node.js & npm (for Splide.js compilation)

### Migration Instructions

1. Upgrade PHP to 8.2+ if needed
2. Install npm dependencies: `npm install`
3. Run migrations: `php artisan migrate`
4. Compile assets: `npm run build`
5. (Optional) Seed recommended articles and feature a category for testing

---

## [Previous Versions]

### [1.0.0] - 2025-10-XX

#### Added
- Initial Laravel 12 platform launch
- WordPress article migration system
- Course management with LMS functionality
- User authentication with role-based access control (Spatie Permission)
- Article scheduling system
- Rich text editor (Trix) integration
- SEO optimization with dynamic meta tags
- Responsive Tailwind CSS design
- Admin panel with CRUD operations

#### Features
- Article categories and tags system
- Course enrollment with payment tracking
- WordPress HTML block processing
- Slug auto-generation
- Image upload and storage management
- Search functionality
- Profile management

---

**Legend:**
- **Added**: New features
- **Changed**: Changes in existing functionality
- **Deprecated**: Soon-to-be removed features
- **Removed**: Removed features
- **Fixed**: Bug fixes
- **Security**: Security improvements
