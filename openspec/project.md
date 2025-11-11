# Project Context - Beautyversity

## Purpose

Beautyversity is a beauty education platform that evolved from a WordPress site into a Laravel-based LMS (Learning Management System). The platform combines:

- **Content Hub**: Comprehensive article management system with beauty education content
- **Course Platform**: Interactive courses with lessons, enrollments, and payment integration
- **Knowledge Base**: Evidence-based beauty information grounded in science (partnered with UNPAD Faculty of Pharmacy)

**Vision**: "Where Beauty Meets Science" - Democratize quality beauty education through accessible, scientifically-validated content.

## Tech Stack

### Backend
- **Framework**: Laravel 12 (PHP 8.2+)
- **ORM**: Eloquent with advanced relationships
- **Queue**: Laravel Queues (Redis/database drivers)
- **Authentication**: Laravel sanctum + Spatie Laravel Permission (roles/permissions)
- **Media**: Spatie Laravel Media Library (file uploads, image conversions, responsive images)
- **SEO**: ralphjsmit/laravel-seo (dynamic SEO data generation)
- **Slugs**: cviebrock/eloquent-sluggable (auto-generated URL slugs)
- **Rich Text**: tonysm/rich-text-laravel (Trix editor integration)

### Frontend
- **Template Engine**: Blade (Laravel templating)
- **CSS Framework**: Tailwind CSS v4 (utility-first, custom primary color theme)
- **JS Framework**: Alpine.js (lightweight interactivity, collapse plugin)
- **Build Tool**: Vite (asset bundling, hot module replacement)
- **Icons**: Font Awesome (SVG icons via CDN)
- **Carousel**: Splide.js (lightweight slider library)

### Database
- **Primary**: MySQL 8.0+
- **Migrations**: Laravel Migrations (YYYY_MM_DD_NNNN format)
- **Testing**: In-memory SQLite (phpunit.xml configuration)

### DevOps
- **Package Manager**: npm (for frontend assets)
- **Composer**: PHP dependency management
- **Vite**: Development server with HMR
- **Laravel Pail**: Real-time log monitoring
- **Laravel Scheduler**: Cron job management (article publishing)

## Project Conventions

### Code Style

**PHP (Laravel)**
- PSR-12 standard (similar to PSR-2)
- Snake_case for database columns and variables
- CamelCase for class names and method names
- Type hints on all method parameters and return types
- Controllers use resource routing (Route::resource)
- Models use Eloquent scopes for query building

**Blade Templates**
- Use Blade directives (@if, @foreach, @include)
- Avoid inline PHP logic in views
- Use view composers for shared data injection
- Component-based structure for reusable elements

**CSS (Tailwind)**
- Mobile-first approach (base classes apply to mobile)
- Use responsive prefixes: sm:, md:, lg:, xl:, 2xl:
- Follow Beautyversity Style Guide for colors, spacing, typography
- Avoid custom CSS; leverage Tailwind utilities
- Primary color: dusty rose theme (#d18a9b)

**JavaScript (Alpine.js)**
- x-data for component state
- x-model for two-way binding
- Inline event handlers with @click, @submit, etc.
- Keep logic minimal (server-side first philosophy)
- Use x-show for visibility (not DOM removal)

### Architecture Patterns

**MVC Pattern**
- Models: Eloquent models with relationships and scopes
- Controllers: Handle HTTP requests, business logic, data preparation
- Views: Blade templates with minimal logic

**Role-Based Access Control (RBAC)**
- 5 roles: student, content-manager, instructor, admin, super-admin
- 40+ granular permissions (view, create, edit, delete, publish, etc.)
- Super-Admin gets all permissions via Gate::before() in AuthServiceProvider
- Other roles use explicit permission assignments from RolePermissionSeeder
- Middleware: can:permission-name in routes, $this->authorize('permission') in controllers

**Content Management**
- Dual content formats: WordPress HTML blocks OR Trix rich text
- content_format field determines rendering path (wordpress vs rich_text)
- WordPress blocks processed via regex (Article::processWordPressBlocks)
- Trix content uses HasRichText trait (attachments at storage/trix-attachments)
- Three article states: draft → scheduled → published

**Article Curation System**
- Recommendation: Articles marked with is_recommended (top 4 on homepage)
- Featured Category: One category at a time (theme colors via accessor)
- Views Tracking: views_count increments without updating updated_at
- Scopes: published(), scheduled(), draft(), recommended(), popular(), trending()

**Relationship Management**
- Articles have many Categories (many-to-many via pivot)
- Articles have many Tags (many-to-many via pivot)
- Courses have many Lessons (one-to-many with order field)
- Users enroll in Courses (many-to-many with pivot: enrollments)
- Media relationships via Spatie Media Library

**Query Optimization**
- Eager loading with ->with('relationships')
- Pagination with ->paginate(per_page)
- Scopes for reusable query logic
- Avoid N+1 queries (use relationship loading)

### Testing Strategy

**Framework**: PHPUnit (Laravel's default)

**Test Types**:
- **Feature Tests**: HTTP integration tests (routes, controllers, middleware)
- **Unit Tests**: Isolated component tests (models, services, helpers)

**Configuration** (phpunit.xml):
- In-memory SQLite database for test speed
- Array cache driver (no external services)
- Sync queue connection (no queuing during tests)
- Test DB created fresh per test run

**Conventions**:
- Test files in tests/Feature/ and tests/Unit/
- Test methods start with test_ or use #[Test] attribute
- Setup: setUp() or setUpBeforeClass() methods
- Assertions: $this->assertTrue(), $this->assertEquals(), etc.
- Database factories for test data creation

**Running Tests**:
```bash
php artisan test                    # All tests
composer test                       # Clear config cache + run tests
php artisan test --testsuite=Feature
php artisan test tests/Feature/ProfileTest.php
```

### Git Workflow

**Branching Strategy**:
- main: Production-ready code
- Feature branches: feature/feature-name
- Bugfix branches: bugfix/bug-name
- Hotfix branches: hotfix/critical-issue (for production emergencies)

**Commit Conventions**:
- Feature commits: Descriptive, focused changes
- Squash before merge to main
- Include ticket/issue reference if applicable
- Format: `feat: add feature X` or `fix: resolve issue Y`

**OpenSpec Integration**:
- Major features use OpenSpec proposals
- Changes documented in openspec/changes/
- Specs updated when change archived
- Proposals contain implementation tasks with status tracking

**PR Guidelines**:
- Include descriptive title and summary
- List testing checklist
- Reference related issues
- Request code review before merge
- Squash commits before merging

### Database Strategy

**Migrations**:
- YYYY_MM_DD_NNNN format (legacy: 001_01_01_0000)
- Use ->nullable() for optional fields
- Use ->default() for common values
- Use ->unique() for uniqueness constraints
- Include ->constrained() for foreign keys (cascade delete/update)

**Seeding**:
- RolePermissionSeeder: Defines all roles and permissions
- Always run with --seed to populate roles
- Use factories for test data generation

**Performance**:
- Add indexes on frequently queried columns
- Use eager loading to prevent N+1 queries
- Denormalize sparingly (prefer relationships)

## Domain Context

### Beauty Education Focus

**Content Categories** (8 primary):
- SKINCARE: Skin health, routines, treatments
- MYTHBUSTER: Debunking beauty myths with evidence
- HAIRCARE: Hair treatments, styling, maintenance
- PERSONALCARE: General body care and hygiene
- DECORATIVE: Makeup, cosmetics, techniques
- MENZONE: Male-specific beauty/grooming
- BAHANAKTIF: Active ingredients, chemistry
- BEAUTYLIFE: Lifestyle, wellness, holistic beauty

**Academic Foundation**:
- Partnership with UNPAD Faculty of Pharmacy
- Evidence-based content (scientific backing required)
- Peer review process for articles (implicit in content-manager approval)
- Expert contributors with credentials

**Content Workflow**:
- Content managers create/edit articles in rich text (Trix) or import WordPress HTML
- Scheduling system allows pre-publication (scheduled_at, publish cron job)
- Categories and tags organize content for discovery
- Manual curation via recommendation and featured sections

### WordPress Legacy

**Migration Context**:
- Original site was WordPress (articles only)
- Laravel platform adds LMS functionality
- Commands preserve original content:
  - migrate:wordpress-articles (posts + drafts)
  - migrate:wordpress-attachments (images/media)
  - migrate:all-wordpress-content (both)

**Content Format Support**:
- WordPress HTML blocks rendered via processWordPressBlocks()
- Regex-based block parsing (maintains styling)
- Trix rich text used for new articles
- Dual-format support indefinitely (no forced migration)

## Important Constraints

### Security
- OWASP Top 10 compliance required
- No direct SQL queries (use Eloquent)
- Input validation on all forms
- Output escaping in views (Blade auto-escapes)
- CSRF protection via middleware (enabled by default)
- Password hashing with bcrypt (Laravel default)

### Performance
- Article pages cached where appropriate (use Laravel caching)
- Database indexes on foreign keys and query filters
- Eager load relationships to prevent N+1 queries
- Pagination default 12-15 items per page
- Image optimization via Spatie Media Library

### Data Integrity
- All relationships use foreign key constraints
- Soft deletes optional (current: hard deletes)
- No direct timestamp manipulation (use created_at, updated_at automatically)
- Transaction management for multi-step operations

### Accessibility
- WCAG 2.1 AA compliance minimum
- Color contrast ratio 4.5:1 for text
- Semantic HTML (proper heading levels, alt text for images)
- Form labels for all inputs
- Focus states for keyboard navigation
- ARIA labels for complex components

### Business Logic
- Role-based access strictly enforced
- Content approval workflow for some operations
- Payment processing for course enrollments
- Email notifications for user actions
- Audit trail for content changes (implicit via timestamps)

## External Dependencies

### Spatie Packages
- **laravel-permission** (roles, permissions, guards, teams)
- **laravel-media-library** (file uploads, image conversions, collections)

### Third-Party Services
- **Google Fonts** (Poppins font family, loaded via CDN)
- **Font Awesome** (icon library via CDN)
- **Vimeo/YouTube** (video embeds for courses)

### Payment Integration
- Payment gateway integration (TBD: Stripe, Midtrans, or local solution)
- Payment method storage (payment_method in enrollments pivot)
- Payment proof uploads (payment_proof in enrollments pivot)

### Email Service
- Laravel Mail (configurable driver: SMTP, Sendmail, SES, etc.)
- Notifications for user registration, password reset, etc.

## Development Setup

### Prerequisites
```
PHP 8.2+
Composer
Node.js 18+
npm or yarn
MySQL 8.0+ (or SQLite for local dev)
```

### Quick Start
```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed
php artisan storage:link

# Development server (all services)
composer dev    # Laravel + Vite + Queue + Pail (concurrent)

# Or individually
php artisan serve
npm run dev
php artisan queue:listen
php artisan pail
```

### Key Commands
```bash
# Testing
composer test
php artisan test --testsuite=Feature

# Build
npm run build

# Database
php artisan migrate --seed
php artisan migrate:fresh --seed
php artisan migrate:rollback

# Caching
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue
php artisan queue:work
php artisan queue:listen --tries=1

# Scheduling
php artisan schedule:work    # Test scheduler locally
php artisan articles:publish-scheduled    # Manual publish

# WordPress Migration
php artisan migrate:wordpress-articles
php artisan migrate:wordpress-attachments
php artisan migrate:all-wordpress-content
```

## Related Documentation

- **CLAUDE.md**: Project instructions, architecture overview, routes, patterns
- **STYLE_GUIDE.md**: Design system, component patterns, Tailwind utilities
- **openspec/**: Feature proposals, implementation specs, change history
