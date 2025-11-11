# Changelog

All notable changes to Beautyversity platform will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
