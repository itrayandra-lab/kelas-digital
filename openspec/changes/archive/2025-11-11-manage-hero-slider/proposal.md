# Proposal: Manage Hero Slider

## Overview

Enable content managers to manually curate and order articles displayed in the homepage hero slider, while maintaining a smart fallback system when manual curation is not active.

## Problem Statement

Currently, the hero slider automatically shows the 5 most recently published articles (`HomeController.php:12-16`). This approach has limitations:

1. **No editorial control** - Content team cannot prioritize important/evergreen articles
2. **Recency bias** - Older high-quality articles get buried regardless of value
3. **No campaign support** - Cannot spotlight specific content for promotions/events
4. **Stale on slow weeks** - If no new articles published, slider becomes repetitive

## Proposed Solution

Implement a **hybrid managed + fallback system** where:

- Content managers can manually select and order up to 5 articles for the hero slider
- Articles not published are automatically removed from slider
- When manual curation is inactive or incomplete (<5 articles), system falls back to latest published articles
- Simple database schema (single integer column for ordering)
- Minimal UI additions to existing article admin forms

## Approach

### Database Changes
Add `hero_slider_order` column to `articles` table:
- Type: `integer`, nullable
- Null value = article not in hero slider
- Non-null value = display order (1-5)
- Unique constraint to prevent duplicate orders

### Admin Interface
**Option 1 (Checkbox in Article Form):**
- Add "Include in Hero Slider" checkbox in article create/edit forms
- Show order input (1-5) when checkbox enabled
- Validate uniqueness server-side

**Option 2 (Dedicated Manager Page):**
- New admin page: `/admin/hero-slider`
- Drag-n-drop interface for reordering
- Search/filter to add articles
- Quick remove button per article

**Recommended: Implement both** for maximum flexibility - checkbox for quick adds, dedicated page for bulk management.

### Query Logic
```php
// Pseudo-code for HomeController
$heroArticles = Article::published()
    ->whereNotNull('hero_slider_order')
    ->orderBy('hero_slider_order', 'asc')
    ->limit(5)
    ->get();

// Fallback if < 5 manual articles
if ($heroArticles->count() < 5) {
    $needed = 5 - $heroArticles->count();
    $fallbackArticles = Article::published()
        ->whereNull('hero_slider_order')
        ->orderBy('published_at', 'desc')
        ->limit($needed)
        ->get();

    $heroArticles = $heroArticles->merge($fallbackArticles);
}
```

### Auto-cleanup Logic
When article status changes to `draft` or `scheduled`:
- Set `hero_slider_order = null`
- Trigger event/notification to alert content team
- Log removal for audit trail

### Stale Content Warning
In admin panel hero slider manager:
- Show "Last updated: X days ago" indicator
- Warning badge if > 30 days since last manual update
- Visual cue to encourage active curation

## Benefits

1. **Full editorial control** when needed, zero maintenance when not
2. **Backward compatible** - existing behavior preserved for non-managed articles
3. **Simple implementation** - minimal database and code changes
4. **Fail-safe** - fallback ensures slider never empty
5. **Accessible** - content-manager role can manage (existing permission model)

## Alternatives Considered

### Alternative 1: Boolean flag only
- Add `is_hero_featured` boolean without ordering
- Simpler schema, but no control over sequence
- Rejected: Ordering is critical for storytelling/prioritization

### Alternative 2: Dedicated slider content type
- Create separate `hero_slides` table with custom content
- Maximum flexibility (custom titles, CTAs, images)
- Rejected: Overkill for current needs, adds content duplication

### Alternative 3: Time-based scheduling
- Schedule hero slots with start/end dates
- Auto-rotate based on calendar
- Rejected: Too complex for initial iteration, can add later if needed

## Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|-----------|
| Content team forgets to update slider | Medium | Add 30-day stale warning in admin panel |
| Duplicate orders break query | Low | Database unique constraint + form validation |
| Unpublished articles in slider | Medium | Auto-remove on status change with model events |
| Performance degradation | Low | Index on `hero_slider_order`, existing caching |

## Dependencies

None - self-contained feature with no external dependencies.

## Out of Scope

The following are explicitly **not** included in this proposal:

1. Course inclusion in hero slider (articles only)
2. Custom slide content (title/excerpt overrides)
3. Scheduled slider rotation (time-based auto-switching)
4. A/B testing for slider variants
5. Analytics integration (click tracking, impressions)
6. Multiple sliders per page or section

These can be considered in future iterations if user feedback indicates demand.

## Success Criteria

1. Content managers can add/remove articles from hero slider via admin panel
2. Articles can be reordered with explicit order (1-5)
3. Unpublished articles automatically removed from slider
4. Fallback to latest articles when <5 manually curated
5. Stale content warning appears after 30 days without update
6. Zero breaking changes to existing homepage rendering
7. Feature works with existing role/permission system (content-manager access)

## Timeline Estimate

- **Implementation**: 4-6 hours
- **Testing**: 2 hours
- **Documentation**: 1 hour
- **Total**: ~8 hours for solo developer

## References

- Current implementation: `app/Http/Controllers/HomeController.php:12-16`
- Hero slider view: `resources/views/home.blade.php:6-47`
- Article model: `app/Models/Article.php`
- Existing curation: `openspec/specs/article-curation/spec.md`
