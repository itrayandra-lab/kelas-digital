# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Beautyversity is a Laravel 12-based beauty education platform that evolved from a WordPress site. It combines a Learning Management System (LMS) for beauty courses with a comprehensive article management system. The platform uses MVC architecture with role-based access control powered by Spatie Laravel Permission.

## Essential Commands

### Development Setup
```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed
php artisan storage:link

# Development server (runs all services concurrently)
composer dev  # Starts Laravel server, queue listener, Pail logs, and Vite

# Alternative: run services individually
php artisan serve
npm run dev
php artisan queue:listen --tries=1
php artisan pail --timeout=0
```

### Testing
```bash
# Run all tests
php artisan test
composer test  # Clears config cache before running tests

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Feature/ProfileTest.php

# Tests use in-memory SQLite database (see phpunit.xml)
```

### Build & Deployment
```bash
# Development build
npm run dev

# Production build
npm run build

# Production optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### WordPress Migration
```bash
# Migrate WordPress articles (posts and drafts)
php artisan migrate:wordpress-articles

# Migrate WordPress attachments (images and media)
php artisan migrate:wordpress-attachments

# Migrate all WordPress content
php artisan migrate:all-wordpress-content
```

### Content Management
```bash
# Publish scheduled articles (runs automatically via scheduler)
php artisan articles:publish-scheduled

# Assign roles to users
php artisan user:assign-role
```

### Queue Worker (for production)
```bash
php artisan queue:work
php artisan queue:listen --tries=1
```

## Architecture Overview

### Role-Based Access Control (RBAC)

The platform uses Spatie Laravel Permission with 5 distinct roles and 40+ granular permissions:

1. **student** - Browse courses, enroll, access enrolled content
2. **content-manager** - Manage articles, categories, and tags only
3. **instructor** - Manage courses and lessons (own content only)
4. **admin** - Full access to content, users, enrollments, and payments
5. **Super-Admin** - All admin permissions plus system role/permission management

**Permission Flow:**
- Super-Admin gets all permissions via `Gate::before()` in `AuthServiceProvider`
- Other roles use explicit permission assignments from `RolePermissionSeeder`
- Admin routes wrapped with `middleware('can:access admin panel')` in `routes/web.php`
- Individual resource routes further protected with specific permissions (e.g., `can:view courses`)

**Key Implementation Files:**
- `database/seeders/RolePermissionSeeder.php` - Defines all roles and permissions
- `app/Http/Middleware/RolePermissionMiddleware.php` - Custom middleware for permission checks
- `routes/web.php:63-111` - Admin route groups with nested permission middleware

### Content Management System

**Articles (WordPress Integration):**
- Supports dual content formats: WordPress HTML blocks and Trix rich text
- `content_format` field determines rendering: `wordpress` vs `rich_text`
- WordPress blocks processed via regex in `Article::processWordPressBlocks()` (app/Models/Article.php:96-178)
- Trix content uses `tonysm/rich-text-laravel` package with `HasRichText` trait
- Rich text attachments stored at `storage/trix-attachments` (routes/web.php:49-59)

**Article Scheduling:**
- Three states: `draft`, `scheduled`, `published`
- Scheduled articles auto-publish via `articles:publish-scheduled` command
- Runs every minute via Laravel scheduler (routes/console.php:6-9)
- Status scopes: `published()`, `scheduled()`, `draft()`, `readyToPublish()` (app/Models/Article.php:213-246)
- Manual publish/unschedule actions available in admin panel (routes/web.php:98-99)

**Courses & Lessons:**
- Courses have many Lessons with `order` field for sequencing
- Lessons belong to Courses (removed full_video_ids JSON column in migration)
- Video integration via YouTube embeds (trailer_video_id field)
- Enrollments use pivot table with payment_status, status, enrolled_at, payment_method, payment_proof

### SEO Strategy

Uses `ralphjsmit/laravel-seo` package with dynamic SEO data generation:

**Implementation Pattern:**
```php
// Both Course and Article models use HasSEO trait
public function getDynamicSEOData(): SEOData
{
    return new SEOData(
        title: $this->title,
        description: $this->description ?: 'fallback',
        author: $this->author,
        image: $this->thumbnail ?: '/logo.webp',
        url: route('...', $this->slug),
        published_time: $this->created_at,
        modified_time: $this->updated_at,
        // Articles also include tags and section
    );
}
```

**Slug Generation:**
- Auto-generated using `cviebrock/eloquent-sluggable` package
- Configuration in `Model::sluggable()` method
- `onUpdate: false` prevents slug changes on updates
- Max length 100 chars, preserves whole words

### View Composition

**Global Data Injection:**
- `CategoryComposer` injects article categories into all views
- Configured in `AppServiceProvider::boot()`
- Fetches 8 specific categories with article counts
- Ordered by predefined sequence for header navigation
- Only shows categories that have articles (`whereHas('articles')`)

### Database Structure

**Core Tables:**
- `users` - Authentication with username/email, uses Spatie roles
- `courses` - title, slug, instructor, description, price, thumbnail, trailer_video_id, course_category_id, level
- `lessons` - belongs to courses, has order field
- `enrollments` - pivot table: course_id, user_id, payment_status, status, enrolled_at, payment_method, payment_proof
- `articles` - title, content (WordPress HTML), body (Trix), thumbnail, author, excerpt, post_type, content_format, slug, scheduled_at, status, published_at
- `course_categories` - for organizing courses
- `article_categories` - many-to-many with articles via `article_article_category` pivot
- `tags` - many-to-many with articles via `article_tag` pivot
- `rich_texts` - stores Trix editor content and attachments

**Migration Pattern:**
- Migrations use YYYY_MM_DD_NNNN format
- Some use legacy format (001_01_01_0000)
- Always run with `--seed` to populate roles/permissions

### Frontend Stack

**Asset Compilation:**
- Vite for bundling (vite.config.js)
- Entry points: `resources/css/app.css`, `resources/js/app.js`
- Tailwind CSS v4 with typography plugin
- Alpine.js for interactivity (with collapse plugin)
- Trix editor for rich text editing

**Blade Component Structure:**
- Main layouts in `resources/views/layouts/`
- Admin panel separate from public views
- Uses Blade components and view composers for shared data

### Testing Strategy

**Configuration (phpunit.xml):**
- In-memory SQLite database for tests
- Array cache and session drivers
- Sync queue connection
- Two test suites: Unit and Feature

**Test Organization:**
- Feature tests: `tests/Feature/` - Integration and HTTP tests
- Unit tests: `tests/Unit/` - Isolated component tests
- Base test case: `tests/TestCase.php`

## Important Patterns & Conventions

### Route Organization
- Public routes at top of `routes/web.php`
- Authenticated routes in `middleware('auth')` group
- Admin routes prefixed with `/admin`, nested permission middleware
- Resource routes used for CRUD operations (e.g., `Route::resource('courses', AdminCourseController::class)`)

### Controller Naming
- Admin controllers use `Admin\` namespace
- Avoid naming conflicts with namespaced imports (e.g., `use ... as AdminCourseController`)

### Content Format Handling
When working with articles, always check `content_format` field:
- `wordpress` - Process through `processWordPressBlocks()`
- `rich_text` - Render via Trix `body` attribute
- Use `getProcessedContentAttribute()` accessor for automatic handling

### Permission Checks in Controllers
Controllers should use:
- `$this->authorize('permission-name')` for controller methods
- Gate checks: `Gate::allows('permission-name')`
- Middleware: `can:permission-name` in routes

### Scheduled Tasks
Scheduler defined in `routes/console.php`:
- Article publishing runs every minute with `withoutOverlapping()` and `runInBackground()`
- To test scheduler locally: `php artisan schedule:work`

## Platform-Specific Context

**Beauty Education Focus:**
- Categories: MYTHBUSTER, SKINCARE, PERSONALCARE, HAIRCARE, DECORATIVE, MENZONE, BAHANAKTIF, BEAUTYLIFE
- Tagline: "Where Beauty Meets Science"
- Academic foundation from UNPAD Faculty of Pharmacy
- Evidence-based content approach

**WordPress Legacy:**
- Original site was WordPress-only (articles)
- Laravel platform adds LMS functionality
- Migration commands preserve original content structure
- Block processing maintains WordPress HTML styling with Tailwind classes
