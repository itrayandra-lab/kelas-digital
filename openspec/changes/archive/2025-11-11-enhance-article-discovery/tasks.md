# Tasks: Enhance Article Discovery

## Phase 1: Related Articles (7 tasks)

### 1.1 Implement Related Articles Query Algorithm in Article Model
- [ ] Add `getRelatedArticles($limit = 4)` method to `Article` model
- [ ] Implement hybrid algorithm: tag-based matching first, category-based fallback
- [ ] Eager load categories and tags in query
- [ ] Calculate similarity scores for ordering
- [ ] Exclude current article from results
- [ ] Test method with various article scenarios
- **Validation**: Run `php artisan tinker` and verify: `Article::find(1)->getRelatedArticles()` returns up to 4 articles with proper ordering

### 1.2 Update HomeController to Load Related Articles
- [ ] Modify `showArticle()` method in `HomeController`
- [ ] Load related articles using new model method
- [ ] Pass `$relatedArticles` to view via compact()
- [ ] Maintain existing article loading logic
- [ ] No database N+1 queries
- **Validation**: Check controller method compiles and loads articles without errors

### 1.3 Add Related Articles Section to Article Detail View
- [ ] Open `resources/views/article/show.blade.php`
- [ ] Add new section after tags section (before footer)
- [ ] Include "Artikel Terkait" heading
- [ ] Use `article.partials.articles` component for consistent styling
- [ ] Add responsive grid: 2 cols mobile, 4 cols desktop
- [ ] Conditionally display only if related articles exist
- [ ] Match spacing and styling from style guide
- **Validation**: View article detail page in browser, verify related articles section appears below content

### 1.4 Test Related Articles End-to-End
- [ ] Create test article with shared tags
- [ ] View article detail page
- [ ] Verify related articles appear
- [ ] Check mobile and desktop responsive layouts
- [ ] Test with article having no related content (section hidden)
- [ ] Verify algorithm ordering (tag matches before category matches)
- **Validation**: Take screenshots of article detail with related articles on mobile and desktop

---

## Phase 2: Tag Browsing (10 tasks)

### 2.1 Create TagController with Index and Show Methods
- [ ] Create new file: `app/Http/Controllers/TagController.php`
- [ ] Implement `index()` method: retrieve all tags with counts, ordered by count DESC
- [ ] Implement `show($slug)` method: fetch tag by slug, load articles (9 per page)
- [ ] Eager load categories and tags on articles
- [ ] Handle both regular and AJAX requests (check for expectsJson())
- [ ] For AJAX: return JSON with articles and pagination data
- [ ] For regular: return full view
- **Validation**: Controller creates without syntax errors, methods have proper signatures

### 2.2 Add Tag Routes
- [ ] Open `routes/web.php`
- [ ] Add `GET /articles/tags` → `TagController@index` (named: `tag.index`)
- [ ] Add `GET /articles/tag/{slug}` → `TagController@show` (named: `tag.show`)
- [ ] Verify routes in public section (no auth middleware)
- [ ] Test routes with `php artisan route:list | grep tag`
- **Validation**: Routes appear in output, both routes accessible in browser

### 2.3 Create Tag Index View
- [ ] Create `resources/views/tag/index.blade.php`
- [ ] Add hero section with "Browse by Topic" heading
- [ ] Display all tags as tag cloud or list with article counts
- [ ] Each tag links to `route('tag.show', $tag->slug)`
- [ ] Style per style guide: responsive, clean layout
- [ ] Include breadcrumb: Home → Tags
- **Validation**: Page renders without errors, tags display with counts and links work

### 2.4 Create Tag Archive View
- [ ] Create `resources/views/tag/show.blade.php`
- [ ] Add hero section with tag name and article count
- [ ] Display articles using `article.partials.articles` component
- [ ] Show 9 articles per page in responsive grid
- [ ] Include "Load More" button at bottom
- [ ] Add pagination text: "Showing X-Y of Z"
- [ ] Include breadcrumb: Home → Tags → Current Tag
- **Validation**: Page renders, displays 9 articles, load more button present

### 2.5 Implement AJAX Load More on Tag Archive
- [ ] Add Alpine.js event handler to "Load More" button
- [ ] Send AJAX GET request with page parameter
- [ ] Append returned articles to grid
- [ ] Update button state (loading, hidden)
- [ ] Handle edge case: last page (button disappears)
- [ ] Reuse pattern from existing article listing pages
- **Validation**: Click load more, articles append, button hides when complete

### 2.6 Update Tag Links on Article Detail Page
- [ ] Open `resources/views/article/show.blade.php`
- [ ] Find all tag links (currently href="#")
- [ ] Change to `href="{{ route('tag.show', $tag->slug) }}"`
- [ ] Verify tag styling unchanged
- [ ] Test links navigate to tag archive page
- **Validation**: Click tag on article detail, navigates to tag archive page

### 2.7 Update TagController for AJAX Responses
- [ ] Modify `show()` method to detect AJAX requests
- [ ] Return JSON format for AJAX:
  ```json
  {
    "articles": [...],
    "has_more_pages": boolean,
    "current_page": int
  }
  ```
- [ ] Include article HTML or JSON depending on request type
- [ ] Maintain proper pagination calculation
- **Validation**: Use browser dev tools to inspect AJAX requests, verify JSON response format

### 2.8 Handle Tag Not Found Edge Cases
- [ ] In TagController `show()`: handle missing tag slug
- [ ] Return 404 if tag not found
- [ ] Return empty articles if tag exists but has no published articles
- [ ] Test both scenarios
- **Validation**: Visit `/articles/tag/nonexistent`, get 404 page

### 2.9 Test Tag Browsing End-to-End
- [ ] Navigate to `/articles/tags`
- [ ] Verify all tags display with counts
- [ ] Click tag name, navigate to archive page
- [ ] Verify 9 articles load
- [ ] Click "Load More", verify next 9 articles append
- [ ] Click article, verify back navigation works
- [ ] From article detail, click tag, navigate to archive
- **Validation**: All navigation flows work smoothly, mobile and desktop responsive

### 2.10 Add Tag Links to Navigation (Optional Enhancement)
- [ ] Consider adding "Browse Tags" link to header navigation or footer
- [ ] Link to `route('tag.index')`
- [ ] Update styling per navigation patterns in style guide
- **Validation**: Navigation link visible and clickable

---

## Phase 3: Enhanced Search (12 tasks)

### 3.1 Add Filter Parameters to SearchController
- [ ] Open `app/Http/Controllers/SearchController.php`
- [ ] Add parameter handling for: category_id, tag_id[], date_from, date_to
- [ ] Retrieve values from request: `request('category_id')`, `request('tag_id')`, etc.
- [ ] Validate parameter types and values
- [ ] Plan query modifications for filtering
- **Validation**: Controller accepts new parameters without errors

### 3.2 Enhance Article Search Query in SearchController
- [ ] Modify article query to include whereHas filters:
  - `whereHas('categories', fn($q) => $q->where('id', $categoryId))` if category_id provided
  - `whereHas('tags', fn($q) => $q->where('id', $tagId))` for tag filtering (if tag_id in array)
  - `whereBetween('published_at', [$dateFrom, $dateTo])` if both dates provided
- [ ] Maintain existing keyword search in title, excerpt, content
- [ ] Keep published() scope to filter only published articles
- [ ] Test with various filter combinations
- **Validation**: Query builder executes without syntax errors, returns filtered results

### 3.3 Add Pagination to Search Results
- [ ] Modify article search to use paginate(12) instead of limit(12)
- [ ] Update return data to include pagination info
- [ ] Support page parameter: `request('page', 1)`
- [ ] Calculate pagination for display
- **Validation**: Article results paginate properly, page parameter works

### 3.4 Handle AJAX vs Regular Requests in SearchController
- [ ] Add logic to detect AJAX requests: `request()->expectsJson()`
- [ ] For regular requests: return full view with filters
- [ ] For AJAX requests: return JSON with articles only
- [ ] Return pagination data in both responses
- **Validation**: Test both regular and AJAX requests return correct format

### 3.5 Build Filter Data for Search View
- [ ] Retrieve all categories for filter dropdown
- [ ] Retrieve top 10-15 popular tags for checkboxes
- [ ] Include article counts for each category/tag
- [ ] Pass to view: `$categories`, `$tags`, `$activeFilters`
- [ ] Calculate active filters from request parameters
- **Validation**: View receives all necessary filter data

### 3.6 Update Search View with Filter UI
- [ ] Open `resources/views/search/index.blade.php`
- [ ] Add filter form section above results
- [ ] Create category dropdown: `<select name="category_id">`
  - Default option: "All Categories"
  - Options for each category with count
- [ ] Create tag checkboxes: `<input type="checkbox" name="tag_id[]">`
  - Show top tags with counts
- [ ] Create date range inputs: `<input type="date" name="date_from">` and `date_to`
- [ ] Add "Apply Filters" button and "Clear Filters" link
- [ ] Style per style guide (form cards, button styling)
- **Validation**: Form renders without errors, inputs functional

### 3.7 Add Active Filters Display to Search Results
- [ ] Display active filters as removable chips above results
- [ ] Format: "Category: Skincare ✕" "Tags: Science, Myth ✕"
- [ ] Each chip clickable to remove individual filter
- [ ] "Clear All Filters" option when multiple filters applied
- [ ] Update dynamically when filters change
- **Validation**: Active filters display correctly, clicking ✕ removes filter

### 3.8 Update Search Results Display with AJAX Load More
- [ ] Modify results section to support AJAX load more
- [ ] Add "Load More Articles" button
- [ ] Implement Alpine.js handler (reuse from article listing)
- [ ] Append loaded articles to existing grid
- [ ] Update pagination info
- [ ] Hide button when no more results
- **Validation**: Load more button works, articles append, button hides at end

### 3.9 Implement Search Results Header with Metadata
- [ ] Display query highlighted: "Found X articles matching 'skincare'"
- [ ] Show active filters inline
- [ ] Update count dynamically after filtering
- [ ] Format: "Found 24 articles matching 'skincare' • Category: Skincare ✕"
- **Validation**: Results header updates with filter selections

### 3.10 Handle No Results State
- [ ] Add condition: if no articles found
- [ ] Display friendly message: "No articles found matching 'keyword'"
- [ ] Suggest action: "Try different keywords or remove some filters"
- [ ] Still show filter UI for user to refine search
- **Validation**: Search with filters returning 0 results shows message

### 3.11 Test Enhanced Search End-to-End
- [ ] Search without filters
- [ ] Search with category filter only
- [ ] Search with tag filter only
- [ ] Search with date range filter only
- [ ] Search with all filters combined
- [ ] Test "Load More" functionality with filters applied
- [ ] Test "Clear Filters" resets to base search
- [ ] Test mobile and desktop layouts
- [ ] Test browser back button preserves filters
- **Validation**: All search scenarios work correctly, URLs are shareable

### 3.12 Refine Search UI Based on Testing
- [ ] Adjust filter form layout if needed (mobile collapsible)
- [ ] Optimize performance if many categories/tags
- [ ] Consider debouncing or live preview
- [ ] Final styling pass per style guide
- [ ] Test keyboard navigation and accessibility
- **Validation**: Search UI is polished and user-friendly

---

## Phase 4: Optional Enhancements & Polish (5 tasks)

### 4.1 Add Article Count Badges to Navigation
- [ ] (Optional) Display count next to category names in header
- [ ] Format: "Skincare (24)"
- [ ] Use existing $categories composer data
- [ ] Update `layouts/app.blade.php` if categories dropdown exists
- **Validation**: Navigation shows article counts

### 4.2 Add View Count to Article Cards (Optional)
- [ ] (Optional) Display views on article cards in partials
- [ ] Format: "1.2K views" using number_format()
- [ ] Position: next to date in metadata
- [ ] Style consistently per style guide
- **Validation**: Article cards display view counts on all pages

### 4.3 Create Unit Tests for Related Articles Algorithm
- [ ] Write test: articles with shared tags return in correct order
- [ ] Write test: fallback to categories when no shared tags
- [ ] Write test: current article excluded from results
- [ ] Write test: limit to 4 articles
- [ ] Run: `php artisan test`
- **Validation**: Tests pass, algorithm tested comprehensively

### 4.4 Create Feature Tests for Tag Pages
- [ ] Write test: tag index page loads
- [ ] Write test: tag archive page loads with articles
- [ ] Write test: tag archive AJAX load more works
- [ ] Write test: non-existent tag returns 404
- [ ] Run: `php artisan test`
- **Validation**: Tests pass, tag functionality validated

### 4.5 Create Feature Tests for Enhanced Search
- [ ] Write test: search with category filter
- [ ] Write test: search with tag filter
- [ ] Write test: search with date range filter
- [ ] Write test: AJAX load more on search results
- [ ] Run: `php artisan test`
- **Validation**: Tests pass, search filters validated

---

## Deployment Checklist

- [ ] All tasks completed and validated
- [ ] Unit and feature tests passing: `composer test`
- [ ] No database migrations needed (relationships exist)
- [ ] Performance verified (queries optimized, eager loading used)
- [ ] Mobile and desktop layouts responsive and tested
- [ ] Accessibility checked (color contrast, keyboard nav, ARIA labels)
- [ ] Style guide compliance verified
- [ ] Git diff reviewed for unintended changes
- [ ] Ready for merge to main branch
