# Spec: Enhanced Search with Filters and Pagination

## Capability

Provide users with advanced search functionality including category, tag, and date range filters on the search results page, with paginated results using AJAX load more pattern for seamless browsing.

## ADDED Requirements

### Requirement: Search Controller Filter Parameters

The system MUST update `SearchController` (`app/Http/Controllers/SearchController.php`) to accept and process filter parameters:

1. **Query Parameter Support**
   - `q` (string, required) - Search keyword
   - `category_id` (integer, optional) - Filter by article category
   - `tag_id` (integer, optional) - Filter by tag (single or multiple)
   - `date_from` (date, optional) - Articles published on or after this date
   - `date_to` (date, optional) - Articles published on or before this date

2. **Article Query Enhancement**
   - Base query: Search title, excerpt, content, author with keyword
   - Add whereHas('categories') when category_id provided
   - Add whereHas('tags') when tag_id provided
   - Add whereBetween('published_at', [date_from, date_to]) when dates provided
   - Maintain publication status filter (published articles only)
   - Eager load categories and tags for display

3. **Pagination**
   - 12 articles per page (matching existing article listing limit)
   - Support AJAX requests for "Load More" functionality
   - Return both full page view and JSON for AJAX

#### Scenario: User searches with filters

When user searches "skincare" filtered by category "SKINCARE" and from last 30 days:
- Query finds articles matching "skincare" keyword
- Filtered to category with id=X
- Filtered to articles published in last 30 days
- First 12 results shown
- Remaining results available via "Load More"

#### Scenario: User applies multiple filters

When user searches "myth" with tags [Mythology, Skincare] and category [MYTHBUSTER]:
- Query matches keyword across all filter criteria
- Articles must match ALL specified filters
- Results shown with first 12, pagination available

#### Scenario: User clears filters

When user searches with filters applied, then clears all:
- URL changes from `/search?q=term&category_id=X&tag_id=Y` to `/search?q=term`
- Query resets to base keyword search
- Page reloads with unfiltered results

### Requirement: Search View Filter Interface

The system MUST update `resources/views/search/index.blade.php` with filter UI section:

1. **Filter Section** (collapsible on mobile)
   - Title: "Filter Results" or "Refine Search"
   - Hidden toggle on mobile (revealed with button click)
   - Full width layout on desktop

2. **Category Filter**
   - Dropdown select: "All Categories" (default)
   - Options: List all article categories from database
   - Shows category name and article count
   - Selected value persisted in URL
   - Example: `<option value="3">Skincare (24)</option>`

3. **Tag Filter**
   - Multi-select checkboxes: Show popular tags (top 10 by usage)
   - Checkbox label includes tag name and count
   - Multiple tags can be selected (OR logic)
   - Selected tags persist in URL as `tag_id[]=1&tag_id[]=2`
   - Example: `<input type="checkbox" name="tag_id[]" value="5"> Skincare (24)`

4. **Date Range Filter**
   - Two input fields: "From Date" and "To Date"
   - Type: date (HTML5 date picker)
   - Format: YYYY-MM-DD
   - Both optional, both must be valid if provided
   - Example: `<input type="date" name="date_from" value="{{ request('date_from') }}">`

5. **Filter Actions**
   - "Apply Filters" button (primary action)
   - "Clear Filters" link (resets to base keyword search)
   - Both update URL and reload results

6. **Active Filters Display**
   - Show as removable chips/tags above results
   - Format: "Category: Skincare ✕" "Tags: Skincare, Science ✕"
   - Each chip is clickable to remove individual filter
   - Clear all option when multiple filters active

#### Scenario: Filter UI with selections

```html
<form method="GET" action="/search" class="filters">
  <input type="hidden" name="q" value="{{ request('q') }}">

  <!-- Category -->
  <div class="filter-group">
    <label>Category</label>
    <select name="category_id">
      <option value="">All Categories</option>
      <option value="1">Skincare (24)</option>
      <option value="2">Mythbuster (18)</option>
      <!-- More categories -->
    </select>
  </div>

  <!-- Tags -->
  <div class="filter-group">
    <label>Tags</label>
    <label><input type="checkbox" name="tag_id[]" value="1"> Skincare (24)</label>
    <label><input type="checkbox" name="tag_id[]" value="2"> Science (15)</label>
    <!-- More tags -->
  </div>

  <!-- Date Range -->
  <div class="filter-group">
    <label>From Date</label>
    <input type="date" name="date_from" value="{{ request('date_from') }}">
    <label>To Date</label>
    <input type="date" name="date_to" value="{{ request('date_to') }}">
  </div>

  <!-- Actions -->
  <button type="submit" class="btn btn-primary">Apply Filters</button>
  <a href="/search?q={{ request('q') }}" class="btn-link">Clear Filters</a>
</form>
```

### Requirement: Search Results Layout with AJAX Load More

The system MUST update search results display to:

1. **Results Header**
   - Display search query highlighted
   - Show result count: "Found X articles matching 'skincare'"
   - Show active filters with removable chips
   - Example: "Found 24 articles matching 'skincare' • Category: Skincare ✕ • Tags: Science ✕"

2. **Results Grid**
   - Use `article.partials.articles` component (consistent styling)
   - Show 12 articles per page
   - Responsive layout: 1 col mobile, 2 cols tablet, 3 cols desktop
   - Maintained spacing per style guide

3. **Load More Button**
   - Position at bottom of results
   - Alpine.js AJAX handler
   - Only visible if more results available
   - Loading state: "Loading..." while fetching
   - Removed when all results shown
   - Example: `<button @click="loadMore" class="...">Load More Articles</button>`

4. **No Results State**
   - When search returns 0 articles
   - Friendly message: "No articles found matching 'keyword'"
   - Suggest: "Try different keywords or remove some filters"

5. **Course Results (Existing)**
   - Maintain separate courses section (unchanged)
   - Same 12-per-page limit

#### Scenario: Search results with filters and load more

When user searches "skincare" with Skincare category filter:
- Page shows: "Found 24 articles matching 'skincare' • Category: Skincare ✕"
- First 12 articles displayed in grid
- "Load More Articles" button shown
- Click button triggers AJAX request to `/search?q=skincare&category_id=3&page=2`
- Next 12 articles appended to grid
- Button remains visible if page 3 exists, removed if at end

### Requirement: Controller Response Handling

The SearchController MUST handle both full page and AJAX requests:

1. **Full Page Request** (initial page load)
   - Return complete view: `search/index.blade.php`
   - Include filter data: categories, tags, selected filters
   - Include article results (paginated)

2. **AJAX Request** (load more)
   - Detect: `request()->expectsJson()` or `request('_ajax') === '1'`
   - Return JSON response:
     ```json
     {
       "articles": [...],
       "has_more_pages": true,
       "current_page": 2,
       "total_articles": 24
     }
     ```
   - Include articles with eager-loaded relationships

3. **Course Results** (existing functionality)
   - Maintain current course search
   - Apply filters if search scope includes courses
   - Keep separate from article results

#### Scenario: AJAX response structure

```json
{
  "articles": [
    {
      "id": 1,
      "title": "Article Title",
      "slug": "article-slug",
      "thumbnail": "/storage/...",
      "excerpt": "Short description",
      "published_at": "2025-11-10",
      "categories": [
        {"id": 1, "name": "Skincare", "slug": "skincare"}
      ],
      "author": "Jane Doe"
    }
    // ... 11 more articles
  ],
  "has_more_pages": true,
  "current_page": 2
}
```

### Requirement: URL and State Management

The search functionality MUST maintain URL consistency:

1. **URL Structure**
   - Base: `/search?q=keyword`
   - With filters: `/search?q=keyword&category_id=1&tag_id=2&tag_id=5&date_from=2025-10-01&date_to=2025-11-30`
   - With pagination: `/search?q=keyword&category_id=1&page=2`

2. **State Persistence**
   - Filter selections persist across page reloads
   - Back button returns to previous filter state
   - Shareable URLs preserve filters

3. **Input Validation**
   - category_id: integer, must exist in database
   - tag_id: integer array, must exist in database
   - date_from/date_to: valid dates, date_from <= date_to
   - Invalid filters silently ignored (treated as not provided)

#### Scenario: User navigates search

1. User searches: `/search?q=skincare`
2. Selects category "Skincare" → URL: `/search?q=skincare&category_id=3`
3. Selects tags "Science", "Myth" → URL: `/search?q=skincare&category_id=3&tag_id=2&tag_id=4`
4. Clicks "Load More" → URL: `/search?q=skincare&category_id=3&tag_id=2&tag_id=4&page=2`
5. Browser back button → Returns to page 1 with same filters
