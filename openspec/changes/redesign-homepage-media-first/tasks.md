# Implementation Tasks

## 1. Database Schema Changes

- [ ] 1.1 Create migration for `articles` table: add `views_count`, `is_recommended`, `recommended_at`
- [ ] 1.2 Create migration for `article_categories` table: add `is_featured_section`, `featured_at`, `theme_color`
- [ ] 1.3 Run migrations with `php artisan migrate`
- [ ] 1.4 Verify schema changes with `php artisan migrate:status`

## 2. Model Updates

- [ ] 2.1 Update Article model: add `$fillable` for new columns, create `scopeTrending()` method
- [ ] 2.2 Update Article model: create `incrementViews()` method for view tracking
- [ ] 2.3 Update ArticleCategory model: add `$fillable` for new columns
- [ ] 2.4 Update ArticleCategory model: create `getThemeColorAttribute()` accessor for dynamic colors
- [ ] 2.5 Write unit tests for Article trending scope
- [ ] 2.6 Write unit tests for view tracking

## 3. Controller Implementation

- [ ] 3.1 Rewrite HomeController::index() with 9 section queries (hero, latest, popular, trending, recommended, featured category, more articles)
- [ ] 3.2 Implement smart exclusion logic for "More Articles" section
- [ ] 3.3 Create RecommendationController with index() method for `/recommendations` page
- [ ] 3.4 Add view tracking middleware or implement in show() method
- [ ] 3.5 Write feature tests for homepage sections data
- [ ] 3.6 Write feature tests for recommendation page

## 4. Frontend Assets

- [ ] 4.1 Install Splide.js: `npm install @splidejs/splide --save`
- [ ] 4.2 Import Splide CSS in `resources/css/app.css`
- [ ] 4.3 Initialize Splide in `resources/js/app.js` or dedicated hero-slider.js
- [ ] 4.4 Configure Splide options: autoplay, type carousel, perPage 1, gap 0
- [ ] 4.5 Run `npm run build` and verify assets compile

## 5. View Templates

- [ ] 5.1 Create header component with top bar (location + social icons)
- [ ] 5.2 Create hero slider section with Splide markup
- [ ] 5.3 Create Latest Article section (2-column layout: 3 horizontal + 3 vertical)
- [ ] 5.4 Create Terpopuler section (4-column grid with dark background)
- [ ] 5.5 Create Recommendation section (4-column grid + "Lihat Lainnya" button)
- [ ] 5.6 Create Trending section (2-column layout: 3 horizontal + 3 vertical)
- [ ] 5.7 Create Featured Category section (3 full-width cards with dynamic theme color)
- [ ] 5.8 Create More Articles section (3 horizontal cards + "See More" button)
- [ ] 5.9 Create article card components (horizontal and vertical variants)
- [ ] 5.10 Create recommendations/index.blade.php with pagination
- [ ] 5.11 Update home.blade.php to use all new sections

## 6. Routing

- [ ] 6.1 Add route: `Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index')`
- [ ] 6.2 Verify route with `php artisan route:list | grep recommendations`

## 7. Admin Panel Integration

- [ ] 7.1 Add "Recommend" checkbox to article create/edit forms
- [ ] 7.2 Add "Featured Section" radio button to category list view
- [ ] 7.3 Update ArticleController@store/update to handle `is_recommended` and `recommended_at`
- [ ] 7.4 Update CategoryController to handle `is_featured_section` and `featured_at`
- [ ] 7.5 Add validation: only 1 category can be featured at a time
- [ ] 7.6 Test admin panel: create recommended article and verify checkbox behavior
- [ ] 7.7 Test admin panel: select featured category and verify radio button behavior

## 8. Testing & Validation

- [ ] 8.1 Manual test: verify hero slider displays featured articles with navigation
- [ ] 8.2 Manual test: verify Latest section shows 6 newest articles in correct layout
- [ ] 8.3 Manual test: verify Terpopuler shows 4 highest all-time viewed articles
- [ ] 8.4 Manual test: verify Recommendation shows 4 manually curated articles
- [ ] 8.5 Manual test: verify Trending shows 6 articles from last 30 days by views
- [ ] 8.6 Manual test: verify Featured Category displays with correct theme color
- [ ] 8.7 Manual test: verify More Articles excludes duplicates from previous sections
- [ ] 8.8 Manual test: verify "Lihat Lainnya" redirects to `/recommendations`
- [ ] 8.9 Manual test: verify "See More" redirects to `/articles`
- [ ] 8.10 Run full test suite: `php artisan test`
- [ ] 8.11 Test responsive design on mobile (single column layout)
- [ ] 8.12 Test Splide autoplay and manual navigation

## 9. Documentation

- [ ] 9.1 Update CLAUDE.md with new homepage sections query patterns
- [ ] 9.2 Document view tracking implementation
- [ ] 9.3 Document content curation workflow (how to recommend articles and feature categories)
- [ ] 9.4 Add Splide.js configuration notes to CLAUDE.md

## 10. Deployment Checklist

- [ ] 10.1 Run migrations on production: `php artisan migrate --force`
- [ ] 10.2 Compile production assets: `npm run build`
- [ ] 10.3 Clear caches: `php artisan optimize:clear`
- [ ] 10.4 Smoke test: verify homepage loads without errors
- [ ] 10.5 Monitor error logs for 24 hours post-deployment
