# Change: Redesign Homepage with Media-First Approach

## Why

The current homepage is course-heavy (6 courses vs 3 articles), which doesn't align with Beautyversity's content pyramid strategy (60% light content, 30% educational, 10% course previews). Users are primarily seeking beauty education articles, not immediately enrolling in courses. A media-first redesign positions articles as primary content and courses as conversion upsells, improving user engagement and natural funnel progression.

## What Changes

- **Transform homepage layout** from course-centric to article-centric with 9 distinct sections
- **Add hero slider** using Splide.js for featured article showcase with autoplay and manual navigation
- **Implement content curation system** with three manual flags: `is_recommended`, `is_featured_section` (for categories)
- **Add trending algorithm** based on views count within 30-day window for dynamic content discovery
- **Create smart content exclusion** to prevent duplicate articles across homepage sections
- **Add view tracking** with `views_count` column for popularity metrics
- **Implement category theming** with dynamic color schemes per article category
- **Add two-column layouts** for Latest and Trending sections (3 horizontal + 3 vertical cards)
- **Create dedicated recommendation page** at `/recommendations` for full curated article list

## Impact

- Affected specs:
  - `homepage-sections` (NEW) - Multi-section layout with hero slider, latest, popular, trending, recommended, and featured category
  - `article-curation` (NEW) - Manual content curation with recommendation flags and view tracking
  - `featured-category` (NEW) - Featured category system with color theming

- Affected code:
  - `app/Http/Controllers/HomeController.php` - Complete rewrite with 9 section queries
  - `app/Models/Article.php` - Add view tracking methods and scopes
  - `app/Models/ArticleCategory.php` - Add featured flag and theme color accessor
  - `database/migrations/` - New columns: `views_count`, `is_recommended`, `recommended_at`, `is_featured_section`, `featured_at`
  - `resources/views/home.blade.php` - Complete layout rebuild with Splide.js integration
  - `resources/views/recommendations/index.blade.php` (NEW) - Dedicated recommendation page
  - `routes/web.php` - Add `/recommendations` route
  - `package.json` - Add @splidejs/splide dependency

- Breaking changes: None (additive only, existing routes remain functional)
