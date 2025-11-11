# Change: Enhance Article Discovery with Related Articles, Tag Browsing, and Advanced Search

## Why

Current article discovery is limited to basic keyword search and category browsing. Users have no way to find related articles, explore content by tags, or filter search results. This creates friction in the content discovery journey and misses opportunities for increased engagement. By implementing:

1. **Related Articles** on detail pages - Keeps users on the platform longer, reduces bounce rate
2. **Tag Browsing** - Enables topic-based discovery, leverages existing tag relationships
3. **Enhanced Search** - Gives users granular control, improves search quality with filters

We improve content discoverability, increase time-on-site, and provide multiple pathways for users to find relevant beauty education content.

## What Changes

- **Add Related Articles section** to article detail pages using hybrid algorithm (shared tags + categories)
- **Implement functional tag browsing system** with tag archive pages (`/articles/tag/{slug}`)
- **Enhance search functionality** with category, tag, and date range filters
- **Add AJAX pagination** to search results and tag archive pages for seamless browsing
- **Update tag links** from non-functional `#` to functional tag archive routes
- **Create tag management UI** (tag cloud / tag list page)

## Impact

- Affected specs:
  - `related-articles` (NEW) - Hybrid recommendation algorithm on article detail pages
  - `tag-browsing` (NEW) - Tag archive pages with article filtering by tag
  - `enhanced-search` (NEW) - Advanced search with filters (category, tags, date range) and pagination

- Affected code:
  - `app/Models/Article.php` - Add `getRelatedArticles()` method with hybrid algorithm
  - `app/Http/Controllers/HomeController.php` - Update `showArticle()` to load related articles
  - `app/Http/Controllers/SearchController.php` - Add filter parameters and pagination
  - `app/Http/Controllers/TagController.php` (NEW) - Handle tag index and show pages
  - `database/` - No new migrations needed (all relationships exist)
  - `resources/views/article/show.blade.php` - Add related articles section
  - `resources/views/search/index.blade.php` - Add filter UI
  - `resources/views/tag/index.blade.php` (NEW) - Tag cloud / list page
  - `resources/views/tag/show.blade.php` (NEW) - Tag archive page
  - `routes/web.php` - Add tag routes

- Breaking changes: None (tag link updates are backward compatible)
- New routes:
  - `GET /articles/tags` → TagController@index (tag cloud)
  - `GET /articles/tag/{slug}` → TagController@show (tag archive)
  - `GET /search` (enhanced with filters)
