# Spec: Related Articles on Detail Pages

## Capability

Display a "Related Articles" section on article detail pages showing semantically similar articles using a hybrid algorithm that prioritizes shared tags and falls back to shared categories.

## ADDED Requirements

### Requirement: Related Articles Query Algorithm

The system SHALL calculate article similarity using a hybrid algorithm:

1. **Tag-based matching** (primary): Find articles sharing the most tags with current article
   - Weight: Each shared tag counts as similarity score increment
   - Articles with 3+ shared tags ranked higher than those with 1-2 tags
2. **Category-based matching** (fallback): If insufficient tag matches, include articles from same categories
3. **Exclusions**: Exclude current article from results
4. **Ordering**: Sort by (tag overlap count DESC, views_count DESC) to show most relevant + popular first
5. **Limit**: Return maximum 4 articles per request

#### Scenario: Article with multiple shared tags

Given an article about "Skincare Myths" tagged with [Mythology, Skincare, Science]:
- When user views detail page
- System finds articles tagged with [Skincare] (1 match), [Skincare, Science] (2 matches), [Skincare, Mythology, Science] (3 matches)
- Related articles shown in order: 3-match article first, then 2-match, then 1-match
- All 4 results displayed if available

#### Scenario: Article with no shared tags

Given an article about "Foundation Techniques" with no overlapping tags with other articles:
- System falls back to category-based matching
- Shows articles from same category (e.g., Decorative)
- Ordered by popularity (views_count DESC)
- Ensures users always see relevant category-based alternatives

#### Scenario: Article with limited matches

Given an article where only 2 articles share tags:
- System shows those 2 tagged matches
- Fills remaining slots (up to 4) with category-based articles
- Maintains single algorithm output (4 articles if available, fewer otherwise)

### Requirement: Model Method for Related Articles

The Article model MUST provide `getRelatedArticles($limit = 4)` method that:
- Returns Collection of Article models
- Eager loads `categories` and `tags` relationships
- Implements hybrid algorithm described above
- Returns empty Collection if no related articles exist
- Does not modify current article state

#### Scenario: Method called on published article

```php
$article = Article::find(5);
$related = $article->getRelatedArticles();
// Returns: Collection of up to 4 Article objects
// Each article has eager-loaded categories and tags
```

### Requirement: Display Related Articles Section on Detail Page

Article detail view (`resources/views/article/show.blade.php`) MUST:
- Display "Related Articles" section below article content (after tags section)
- Show section title in Indonesian: "Artikel Terkait"
- Use responsive grid layout: 2 columns on mobile, 4 columns on desktop
- Reuse `article.partials.articles` component for consistency
- Only display section if related articles exist (hide if empty)
- Include proper spacing/padding matching style guide

#### Scenario: Article detail page with related articles

When user views article detail:
- Page loads with main article content
- "Artikel Terkait" section appears below with 4 related articles
- Cards styled identically to homepage article cards
- Grid responsive: stacks to 2 cols on mobile, 4 cols on desktop

#### Scenario: Article with no related articles

When article has no related content:
- "Artikel Terkait" section not rendered
- Page shows main content only
- No empty state message displayed

### Requirement: Controller Integration

HomeController's `showArticle()` method (line 90-102) MUST:
- Load related articles using `Article::getRelatedArticles()`
- Pass `$relatedArticles` to view
- Not change existing article loading logic
- Maintain performance with eager loading

#### Scenario: Controller passes data to view

```php
// In showArticle() method
$article = Article::published()->with('categories', 'tags')->findBySlug($slug);
$relatedArticles = $article->getRelatedArticles();
return view('article.show', compact('article', 'relatedArticles'));
```
