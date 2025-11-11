# Beautyversity.id - Beauty Academy Platform

A modern, fully functional online learning platform built with Laravel 12 using MVC architecture, relational database storage, and a responsive Tailwind CSS frontend. The platform provides comprehensive course management with video integration, advanced user authentication, and powerful administrative capabilities.

## Platform Evolution

This platform represents a complete evolution from the original Beautyversity.id WordPress website, which focused solely on beauty articles. The new Laravel-based platform expands the educational offering with:

- **WordPress Migration**: Seamless import of existing beauty articles and content from the original WordPress site
- **Course Management**: Full learning management system with video integration and structured lessons
- **Enhanced User Experience**: Modern, responsive design with advanced features and role-based access control
- **Scalable Architecture**: Built on Laravel 12 with MVC architecture for better performance and maintainability

## Features

### Core Learning Platform
-   **User Authentication & Authorization** - Registration/login with username or email, comprehensive role-based access control (5 roles: student, content-manager, instructor, admin, Super-Admin)
-   **Beauty Course Management** - Full CRUD operations with video integration (YouTube embed), lesson management, and course categories (Skincare, Makeup, Hair Care, etc.)
-   **Learning Experience** - Lesson preview functionality, enrollment-based access control, and structured learning paths for beauty education
-   **Payment System** - Manual verification workflow with admin approval process
-   **Beauty Article System** - Content management with categories, tags, and dual-format rendering (WordPress HTML + Trix rich text) covering skincare, makeup, and beauty science topics

### Advanced Features
-   **WordPress Migration** - Comprehensive tools for importing Beautyversity.id WordPress content (posts, drafts, attachments)
-   **Global Search** - Keyword search across beauty courses and articles with dedicated results page
-   **SEO-Friendly URLs** - Slug-based routing for courses and articles with beauty-focused SEO optimization
-   **User Profile Management** - Complete profile editing, password change, and user dashboard
-   **Responsive Design** - Mobile-first design with Tailwind CSS and Alpine.js integration
-   **Admin Panel** - Comprehensive dashboard with role-specific interfaces and data filtering for beauty education management

## Technology Stack

-   **Backend:** Laravel 12 with MVC architecture, PHP 8.2+
-   **Database:** Relational database (MySQL/SQLite) with Eloquent ORM
-   **Frontend:** Blade templating with Tailwind CSS v4, Alpine.js with Splide.js carousels
-   **Video Hosting:** YouTube (embedded videos for beauty tutorials)
-   **Authentication:** Laravel built-in authentication with Spatie Laravel Permission
-   **Security:** Role-based access control with 40+ granular permissions
-   **Content Management:** Trix rich text editor, WordPress block processing for beauty content
-   **Asset Management:** Vite for compilation and optimization
-   **SEO:** ralphjsmit/laravel-seo package for beauty-focused search optimization
-   **Design System:** Comprehensive Tailwind CSS design system with standardized components, typography, spacing, and color palette

## Design System & Style Guide

The platform features a comprehensive, documented design system ensuring visual consistency across all pages and components.

### Style Guide Documentation

A detailed style guide is available at `docs/STYLE_GUIDE.md` covering:

- **Color Palette** - Dusty rose primary theme (primary-400 to primary-700), secondary colors, and custom gray palette
- **Typography** - Poppins font family (weights 300-700), complete type scale with hierarchy
- **Spacing System** - Standardized padding, margin, and gap values (p-2 through p-8, gap-2 through gap-8)
- **Component Styles** - Pre-defined patterns for buttons, cards, badges, forms, and navigation
- **Responsive Design** - Mobile-first approach with sm, md, lg, xl breakpoints
- **Shadows & Elevation** - Consistent shadow scale for depth and visual hierarchy
- **Animations & Transitions** - Smooth transitions (150-300ms) with Alpine.js integration
- **Complete Code Examples** - Copy-paste ready components and layouts

### Design Consistency

All public pages and components have been audited and standardized for visual consistency:

- **Homepage** - 9-section content pyramid with featured category spotlight and article recommendations
- **Article Pages** - Consistent hero headers, typography, and metadata display
- **Article Categories** - Unified card layouts with standardized image heights and spacing
- **Search Results** - Consistent card styling matching article listings
- **Responsive Design** - Full mobile optimization (sm, md, lg breakpoints) with progressive enhancement

### Quick Reference

**Primary Colors:**
- Action: `bg-primary-600` (#d18a9b)
- Hover: `bg-primary-700` (#b96f80)
- Light backgrounds: `bg-gray-50`
- Dark backgrounds: `bg-gray-900`

**Spacing Standards:**
- Card padding: `p-6` (24px)
- Grid gaps: `gap-8` (32px)
- Section vertical: `py-16 md:py-24` (64px / 96px)
- Input padding: `px-4 py-2.5`

**Typography:**
- Font: Poppins (weights 300-700)
- Headings: `font-bold` with appropriate size scale
- Body text: `text-gray-900` for primary, `text-gray-600` for secondary
- Metadata: `text-gray-500` with `text-xs` or `text-sm`

**Component Patterns:**
- Card border: `border border-gray-100` (light) or `border border-gray-700` (dark)
- Card radius: `rounded-lg` (8px) for standard radius
- Shadows: `shadow-sm` (cards), `shadow-lg` (hover/dropdowns)
- Transitions: `transition` (150ms default)

### Developer Guide

For detailed architecture, permissions, content management patterns, and development workflows, see `CLAUDE.md` which includes:

- Essential commands for development, testing, and deployment
- Role-based access control implementation details
- Content management system architecture (articles, courses, lessons)
- SEO strategy and slug generation
- Database structure and relationships
- Testing strategy and configuration
- WordPress migration patterns

## Installation & Setup

### Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js & npm
-   MySQL or SQLite

### Installation Steps

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd kelas-digital-laravel
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install --optimize-autoloader --no-dev
    ```

3. **Install JavaScript dependencies:**
    ```bash
    npm install
    ```

4.  **Set up environment file:**
    -   Copy `.env.example` to `.env`.
    -   Configure your database connection (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    -   Configure your YouTube API key if needed.
    -   Set `APP_ENV=local` and `APP_DEBUG=true` for development.

5.  **Generate application key:**
    ```bash
    php artisan key:generate
    ```

6.  **Run database migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```

7.  **Compile frontend assets:**
    ```bash
    # For development
    npm run dev
    
    # For production
    npm run build
    ```

8.  **Create storage link (for file uploads):**
    ```bash
    php artisan storage:link
    ```

9.  **Set proper permissions:**
    ```bash
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache
    ```

10. **Start the development server:**
    ```bash
    php artisan serve
    ```

11. **Configure your web server** (Apache/Nginx) to point to the `public` directory for production.

## User Roles & Permissions

The platform includes a comprehensive role-based access control system designed for beauty education:

-   **Student** - Can browse beauty courses, enroll, and access enrolled content
-   **Content Manager** - Manages beauty articles, categories, and tags
-   **Instructor** - Manages beauty courses and lessons (limited to their own content)
-   **Admin** - Manages all content, users, enrollments, and payments with full dashboard access
-   **Super-Admin** - All Admin permissions plus system management (roles and permissions)

## WordPress Migration

The platform includes powerful WordPress migration tools to seamlessly import content from the original Beautyversity.id WordPress site:

```bash
# Migrate WordPress articles (posts and drafts)
php artisan migrate:wordpress-articles

# Migrate WordPress attachments (images and media files)
php artisan migrate:wordpress-attachments

# Migrate all WordPress content (comprehensive migration)
php artisan migrate:all-wordpress-content
```

### Migration Features
- **Content Preservation**: Maintains original article formatting and structure
- **Media Handling**: Downloads and rehosts all images and attachments
- **Taxonomy Migration**: Imports categories and tags with proper relationships
- **SEO Preservation**: Maintains original slugs and meta data
- **Dual Format Support**: Supports both WordPress HTML and modern rich text content

## Development Commands

```bash
# Start development with all services (Laravel, Queue, Logs, Vite)
composer dev

# Or run services individually:
php artisan serve                    # Laravel development server
npm run dev                          # Vite asset compilation
php artisan queue:listen --tries=1   # Queue worker
php artisan pail --timeout=0         # Log viewer

# Run tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Assign user roles
php artisan user:assign-role

# Publish scheduled articles (runs every minute automatically)
php artisan articles:publish-scheduled

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database reset (development only)
php artisan migrate:fresh --seed
```

## Deployment

For production deployment, ensure the following:

-   Set `APP_ENV=production` and `APP_DEBUG=false` in your `.env` file.
-   Run `php artisan config:cache` to cache configuration.
-   Run `php artisan route:cache` to cache routes.
-   Run `php artisan view:cache` to cache views.
-   Ensure the `storage` and `bootstrap/cache` directories are writable by the web server.
-   Use a process monitor like Supervisor to run queue workers if using queues.
-   Configure HTTPS for secure communication.
-   Run `php artisan storage:link` to expose media storage.


## Contributing

1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/your-feature`).
3.  Make your changes following the project's coding standards.
4.  Write tests for new features.
5.  Commit your changes (`git commit -m 'feat: add some feature'`).
6.  Push to the branch (`git push origin feature/your-feature`).
7.  Open a pull request with a clear description.

## About Beautyversity.id

Beautyversity.id is an innovative cosmetics education startup founded by four Master's students from Universitas Padjadjaran (UNPAD) Faculty of Pharmacy. The platform combines academic expertise with practical education to address misinformation in the cosmetics industry while providing evidence-based beauty education and product development.

### Mission & Vision
- **Mission**: To educate the public about safe, high-quality, and beneficial cosmetic products through evidence-based education
- **Vision**: Become a trusted source of information in the cosmetics industry and leading beauty education platform in Indonesia
- **Tagline**: "Where Beauty Meets Science"

### Academic Foundation
- **Origin**: Business Development and Management Cosmetic course at UNPAD
- **Founded**: July 1, 2021
- **Institution**: Universitas Padjadjaran (UNPAD) Faculty of Pharmacy
- **Research Foundation**: Evidence-based approach using scientific literature
- **Validation**: Peer-reviewed research and academic supervision

## License

This project is licensed under the MIT License.
