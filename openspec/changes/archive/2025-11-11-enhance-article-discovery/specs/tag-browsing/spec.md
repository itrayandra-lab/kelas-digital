# Spec: Tag Browsing System

## Capability

Enable users to discover articles by browsing and filtering through tags, with functional tag links and dedicated tag archive pages showing all articles for a specific tag with AJAX load more functionality.

## ADDED Requirements

### Requirement: Tag Controller with Index and Show Actions

The system SHALL create a new `TagController` (`app/Http/Controllers/TagController.php`) with:

1. **index()** method - Display all tags with article counts
   - Retrieve all tags from database
   - Include article count for each tag
   - Order by: article count DESC (most used tags first)
   - Pass data to `tag.index` view
   - No pagination needed

2. **show($slug)** method - Display articles for specific tag
   - Load tag by slug
   - Retrieve articles with this tag, published only
   - Eager load categories and tags on articles
   - Handle AJAX requests for load more functionality
   - Support pagination: 9 articles per page
   - Return partial view for AJAX requests, full view for initial page load

#### Scenario: User accesses tag index page

When user visits `/articles/tags`:
- Page loads with list/cloud of all tags
- Each tag shows article count (e.g., "Skincare (24)")
- Tags linked to their respective archive pages
- Ordered by popularity (most articles first)

#### Scenario: User views tag archive page

When user visits `/articles/tag/skincare`:
- Page loads with tag hero section (tag name + article count)
- Grid of first 9 articles tagged with "skincare"
- Articles sorted by newest first (published_at DESC)
- "Load More" button at bottom to fetch next 9
- Button updates to show progress: "Loading..." then fetches next page

#### Scenario: AJAX load more request

When user clicks "Load More" on tag archive:
- AJAX request sent with `page` parameter
- Controller returns only articles + pagination data (no layout)
- JavaScript appends articles to page
- Load more button removed if no more pages available

### Requirement: Functional Tag Links on Article Detail Page

The system MUST update article detail view (`resources/views/article/show.blade.php`):
- Change all tag links from `href="#"` to `href="{{ route('tag.show', $tag->slug) }}"`
- Tags now navigate to tag archive pages
- Maintain existing tag styling and layout

#### Scenario: User clicks tag on article

When viewing article with tags and clicking a tag:
- Navigates to `/articles/tag/{tag-slug}`
- Shows all other articles with that tag
- User can discover related content
- Clicking back returns to original article (browser history)

### Requirement: Tag Archive View

The system MUST create `resources/views/tag/show.blade.php` with:

1. **Hero Section**
   - Display tag name prominently
   - Show article count (e.g., "23 articles")
   - Optional: tag description or usage context

2. **Article Grid**
   - Use `article.partials.articles` component
   - Display 9 articles per page
   - Responsive grid: 1 col mobile, 2 cols tablet, 3 cols desktop

3. **Load More Button**
   - Position at bottom of articles
   - Alpine.js functionality (reuse existing pattern)
   - Only show if more articles available
   - Update after each load (remove when no more pages)

4. **Pagination Data**
   - Display current results count (e.g., "Showing 1-9 of 23")
   - Update dynamically with each load

#### Scenario: Tag archive page layout

```html
<!-- Hero Section -->
<div class="bg-gradient-to-r from-primary-600 to-primary-700 py-12">
  <h1 class="text-4xl font-bold text-white">Skincare</h1>
  <p class="text-primary-100 mt-2">23 articles</p>
</div>

<!-- Article Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- Articles component -->
</div>

<!-- Load More -->
<div class="text-center mt-12">
  <button class="load-more-btn">Load More Articles</button>
</div>
```

### Requirement: Tag Index View

The system MUST create `resources/views/tag/index.blade.php` with:

1. **Hero Section** - Heading "Browse by Topic" or "All Tags"
2. **Tag Cloud or List**
   - Display all tags with article counts
   - Option 1: Tag cloud (visual cloud with size variation)
   - Option 2: Alphabetical list with counts
   - Each tag links to its archive page
   - Styled with Tailwind CSS per style guide

3. **Optional Features**
   - Search/filter tag list (client-side Alpine.js)
   - Sort options (alphabetical, by count)

#### Scenario: Tag index page layout

```html
<h1>Browse by Topic</h1>
<p>Explore articles organized by topic</p>

<!-- Tag Cloud / List -->
<div class="flex flex-wrap gap-3">
  <a href="/articles/tag/skincare" class="...">
    Skincare <span class="text-xs">(24)</span>
  </a>
  <!-- More tags -->
</div>
```

### Requirement: Tag Routes

The system MUST add to `routes/web.php`:
- `GET /articles/tags` → `TagController@index` (named: `tag.index`)
- `GET /articles/tag/{slug}` → `TagController@show` (named: `tag.show`)
- Routes should be in public section (no authentication required)
- Support for AJAX requests via proper response handling

#### Scenario: Route behavior

- User navigates to `/articles/tags` → sees all tags
- User navigates to `/articles/tag/skincare` → sees articles with that tag
- AJAX request to `/articles/tag/skincare?page=2` → returns JSON with articles

### Requirement: AJAX Load More Implementation

Tag archive page MUST support AJAX pagination using Alpine.js pattern from existing article listing pages:

1. **JavaScript Handler**
   - Detect "Load More" button clicks
   - Send AJAX GET request with `page` parameter
   - Append new articles to grid
   - Update button state (loading, hidden, etc.)
   - Handle errors gracefully

2. **Controller Response Format**
   - If AJAX request (`request()->expectsJson()`):
     - Return JSON with articles and pagination info
     - Include `has_more_pages` boolean
   - If regular request:
     - Return full page view

#### Scenario: AJAX load more flow

1. User scrolls to bottom and clicks "Load More"
2. Button shows "Loading..." state
3. AJAX request: `GET /articles/tag/skincare?page=2` (with header indicating AJAX)
4. Controller returns JSON: `{ articles: [...], has_more_pages: true }`
5. JavaScript appends articles to DOM
6. Button resets to "Load More" for page 3
7. When `has_more_pages: false`, button is hidden
