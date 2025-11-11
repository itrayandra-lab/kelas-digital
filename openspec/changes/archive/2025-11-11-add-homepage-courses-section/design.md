# Design Document: Homepage Courses Section

## Overview

This document captures the architectural decisions and design rationale for adding a courses section to the homepage.

## Architecture Context

### Current Homepage Structure

The homepage currently has 7 sections focused entirely on articles:

1. **Hero Slider** - 5 latest published articles with large imagery
2. **Latest Articles** - 6 most recent articles in 70/30 grid split
3. **Terpopuler** - 4 most-viewed articles (all-time) in dark theme
4. **Recommendations** - 4 manually curated articles in 4-column grid
5. **Trending** - 6 articles from last 30 days in 70/30 split
6. **Featured Category** - 3 articles from featured category with overlay
7. **More Articles** - 3 additional articles with large horizontal cards

**Controller Pattern:**
```php
// HomeController::index()
$heroArticles = Article::published()->with(...)->orderBy(...)->limit(5)->get();
$latestArticles = Article::published()->with(...)->orderBy(...)->limit(6)->get();
// ... 5 more similar queries
```

**View Pattern:**
```blade
<section id="section-name" class="py-16 md:py-24 bg-{color}">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-12">
      <h2 class="text-3xl md:text-4xl font-bold">Section Title</h2>
      <p class="text-gray-600">Description</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <!-- cards -->
    </div>
  </div>
</section>
```

### Course Model Context

```php
// app/Models/Course.php
class Course extends Model {
    protected $fillable = [
        'title', 'slug', 'instructor', 'description',
        'price', 'thumbnail', 'trailer_video_id',
        'course_category_id', 'level'
    ];

    public function enrollments() { ... }
    public function lessons() { ... }
    public function category() { ... }
}
```

**Current Gaps:**
- No `is_featured` or `featured_at` column for curation
- No scopes for filtering (featured, popular, newest)
- No enrollment count accessor

## Design Decisions

### Decision 1: Section Placement (Position #5)

**Options Considered:**

| Position | Rationale | Pros | Cons |
|----------|-----------|------|------|
| #2 (After Hero) | High visibility, education-first | Maximum exposure, clear dual platform | Too aggressive, disrupts article flow |
| #4 (After Recommendations) | Mid-page, content-to-course funnel | Balanced, users engaged by articles first | Might be overlooked on mobile |
| #8 (End of page) | Bottom CTA, final conversion | Doesn't disrupt existing flow | Poor visibility, scroll fatigue |

**Decision:** Position #5 (After Recommendations, Before Trending)

**Rationale:**
1. **User Journey Alignment**: Users encounter 4 article sections first (hero, latest, popular, recommendations) totaling ~19 articles, establishing platform credibility
2. **Engagement Timing**: By position 5, users have scrolled 2-3 viewports, indicating interest
3. **Content Pyramid**: Free content → curated content → **premium education** → more content
4. **Visual Break**: Provides contrast between article-heavy sections (recommendations) and (trending)
5. **Precedent**: Similar to how e-commerce sites place "Featured Products" mid-scroll after category listings

### Decision 2: Curation Strategy (Manual via is_featured)

**Options Considered:**

| Strategy | Implementation | Pros | Cons |
|----------|----------------|------|------|
| Manual (`is_featured` flag) | Boolean column + admin toggle | Full control, quality assurance | Requires admin maintenance |
| Algorithmic (newest) | `->orderBy('created_at', 'desc')` | Zero maintenance, always fresh | Might show low-quality courses |
| Algorithmic (popular) | Order by enrollment count | Social proof, conversion-focused | New courses never appear |
| Hybrid (featured + newest) | Featured first, then newest | Balances control and freshness | More complex logic |

**Decision:** Manual curation via `is_featured` flag

**Rationale:**
1. **Consistency**: Matches existing article recommendation system (also uses `is_recommended` flag)
2. **Quality Control**: Platform has academic partnership (UNPAD), needs editorial oversight
3. **Revenue Optimization**: Admins can feature courses strategically (seasonality, promotions, instructor availability)
4. **Simplicity**: Straightforward implementation, clear semantics
5. **Flexibility**: Can easily add algorithmic fallback later if needed

**Implementation:**
```php
// Migration
$table->boolean('is_featured')->default(false);
$table->timestamp('featured_at')->nullable();

// Model scope
public function scopeFeatured($query) {
    return $query->where('is_featured', true)
                 ->orderBy('featured_at', 'desc');
}

// Controller
$featuredCourses = Course::featured()
    ->with('category', 'enrollments')
    ->limit(4)
    ->get();
```

### Decision 3: Card Design (Consistent with Articles)

**Options Considered:**

| Approach | Visual Identity | Pros | Cons |
|----------|----------------|------|------|
| Identical to articles | Same card structure, colors | Perfect consistency, minimal CSS | No differentiation, might confuse |
| Distinct design | Different colors, layout | Clear separation, premium feel | Breaks visual harmony, more CSS |
| Subtle differences | Same structure, different badge/metadata | Balanced recognition and consistency | Requires careful design |

**Decision:** Consistent structure with subtle differences

**Visual Specifications:**

```blade
<div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
  <img src="..." class="w-full h-48 object-cover">
  <div class="p-6">
    {{-- DIFFERENCE: Badge shows "COURSE" instead of category --}}
    <span class="text-xs font-bold uppercase text-primary-600">COURSE</span>

    <h3 class="text-lg font-bold text-gray-800 mt-2 mb-3 line-clamp-2">
      {{ $course->title }}
    </h3>

    {{-- DIFFERENCE: Metadata shows instructor + level instead of date --}}
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
      <span>{{ $course->instructor }}</span>
      <span>•</span>
      <span>{{ $course->level }}</span>
    </div>

    {{-- DIFFERENCE: Price display --}}
    <div class="flex items-center justify-between">
      <span class="text-xl font-bold text-primary-600">
        Rp {{ number_format($course->price, 0, ',', '.') }}
      </span>
      @if($course->enrollments_count > 0)
        <span class="text-xs text-gray-500">
          {{ $course->enrollments_count }} enrolled
        </span>
      @endif
    </div>
  </div>
</div>
```

**Rationale:**
1. **Pattern Reuse**: Leverages existing card patterns from style guide (reduces CSS, maintains consistency)
2. **Clear Differentiation**: "COURSE" badge and price immediately signal this is paid education
3. **Metadata Alignment**: Shows course-relevant info (instructor, level, price) instead of article metadata (date, author)
4. **Style Guide Compliance**: Uses documented patterns (p-6, gap-8, text-lg, rounded-lg, primary-600)

### Decision 4: Responsive Layout (1-2-4 Grid)

**Grid Breakpoints:**

| Breakpoint | Columns | Gap | Container Padding |
|------------|---------|-----|-------------------|
| Mobile (0-767px) | 1 | gap-4 | px-4 |
| Tablet (768-1023px) | 2 | gap-6 | px-6 |
| Desktop (1024+px) | 4 | gap-8 | px-8 |

**Implementation:**
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
  @foreach($featuredCourses as $course)
    {{-- card --}}
  @endforeach
</div>
```

**Rationale:**
1. **Consistency**: Matches Recommendations (4 items, 4 columns) and Terpopuler (4 items, 4 columns) sections
2. **Mobile-First**: Single column on mobile prevents cramped cards
3. **Tablet Balance**: 2 columns on tablet provides good readability without excessive scrolling
4. **Desktop Efficiency**: 4 columns fills width, shows all courses above fold on large screens
5. **Gap Scaling**: Larger gaps on desktop leverage available space

### Decision 5: Section Styling (Light Background)

**Background Options:**

| Option | Class | Rationale |
|--------|-------|-----------|
| White | `bg-white` | Clean, minimal |
| Light gray | `bg-gray-50` | Subtle separation |
| Primary tint | `bg-primary-50` | Premium feel |
| Dark | `bg-gray-800` | High contrast |

**Decision:** Light gray (`bg-gray-50`)

**Section Pattern:**
```blade
<section id="featured-courses" class="py-16 md:py-24 bg-gray-50">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-12">
      <div>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          Featured Courses
        </h2>
        <p class="text-gray-600">
          Structured learning programs by expert instructors
        </p>
      </div>
      <a href="{{ route('course.index') }}"
         class="hidden md:inline-block px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
        Browse All Courses
      </a>
    </div>
    <!-- grid -->
  </div>
</section>
```

**Rationale:**
1. **Alternating Pattern**: Homepage alternates white/gray backgrounds (Latest=white, Terpopuler=dark, Recommendations=gray-50, **Courses=gray-50**, Trending=white)
2. **Visual Grouping**: Light gray creates subtle section separation without harsh contrast
3. **Card Pop**: White cards on gray-50 background have subtle depth
4. **Consistency**: Matches Recommendations section (also uses bg-gray-50 with 4-column grid)

### Decision 6: Enrollment Count Display

**Options:**

| Display | Example | Pros | Cons |
|---------|---------|------|------|
| Always show | "0 enrolled" | Transparency | Signals low popularity |
| Conditional (if >0) | "15 enrolled" or nothing | Avoids zero display | Inconsistent |
| Threshold (if >5) | "15 enrolled" or nothing | Only shows social proof | Arbitrary threshold |
| Generic range | "10+ enrolled" | Always shows something | Potentially misleading |

**Decision:** Conditional display (show if >0, hide if 0)

**Implementation:**
```blade
@if($course->enrollments_count > 0)
  <span class="text-xs text-gray-500">
    {{ $course->enrollments_count }} enrolled
  </span>
@endif
```

**Rationale:**
1. **Avoids Negative Signal**: New courses with 0 enrollments don't appear unpopular
2. **Social Proof When Available**: Courses with enrollments benefit from displaying count
3. **Honest Representation**: No misleading "10+" claims when actual count is 2
4. **Graceful Degradation**: Layout doesn't break if enrollment count missing

## Data Flow

### Query Strategy

```php
// HomeController::index()

// Query featured courses with relationships
$featuredCourses = Course::query()
    ->where('is_featured', true)
    ->with(['category', 'enrollments'])  // Eager load to prevent N+1
    ->withCount('enrollments')           // Get enrollment count efficiently
    ->orderBy('featured_at', 'desc')     // Most recently featured first
    ->limit(4)                           // Show top 4
    ->get();

// Fallback if <4 featured courses (optional enhancement)
if ($featuredCourses->count() < 4) {
    $additionalCourses = Course::query()
        ->where('is_featured', false)
        ->with(['category', 'enrollments'])
        ->withCount('enrollments')
        ->orderBy('created_at', 'desc')
        ->limit(4 - $featuredCourses->count())
        ->get();

    $featuredCourses = $featuredCourses->merge($additionalCourses);
}
```

**Performance Considerations:**
- **Eager Loading**: `->with(['category', 'enrollments'])` prevents N+1 queries
- **Count Optimization**: `->withCount('enrollments')` uses SQL COUNT() instead of loading all enrollment records
- **Query Limit**: Only fetch 4 courses maximum
- **Index Requirements**: Add index on `is_featured` column for faster filtering

### View Data Structure

```php
// Passed to view
compact(
    'heroArticles',           // Existing
    'latestArticles',         // Existing
    'popularArticles',        // Existing
    'recommendedArticles',    // Existing
    'featuredCourses',        // NEW
    'trendingArticles',       // Existing
    'featuredCategory',       // Existing
    'featuredCategoryArticles', // Existing
    'moreArticles'            // Existing
)
```

## Migration Strategy

### Database Changes

```php
// Migration: 2025_11_11_000001_add_featured_fields_to_courses_table.php

public function up()
{
    Schema::table('courses', function (Blueprint $table) {
        $table->boolean('is_featured')->default(false)->after('level');
        $table->timestamp('featured_at')->nullable()->after('is_featured');

        // Add index for performance
        $table->index('is_featured');
    });
}

public function down()
{
    Schema::table('courses', function (Blueprint $table) {
        $table->dropIndex(['is_featured']);
        $table->dropColumn(['is_featured', 'featured_at']);
    });
}
```

### Model Changes

```php
// app/Models/Course.php

protected $fillable = [
    'title', 'slug', 'instructor', 'description',
    'price', 'thumbnail', 'trailer_video_id',
    'course_category_id', 'level',
    'is_featured', 'featured_at'  // NEW
];

protected $casts = [
    'featured_at' => 'datetime',  // NEW
];

// NEW: Featured scope
public function scopeFeatured($query)
{
    return $query->where('is_featured', true)
                 ->orderBy('featured_at', 'desc');
}
```

### Seeding Strategy

```php
// database/seeders/CourseSeeder.php (or in RolePermissionSeeder)

// Feature 2-4 courses for initial homepage display
Course::whereIn('id', [1, 2, 3, 4])->update([
    'is_featured' => true,
    'featured_at' => now()
]);
```

## Rollback Plan

If this change needs to be reverted:

1. **Immediate Rollback** (No Data Loss):
   - Comment out courses section in `home.blade.php` (lines to be added)
   - Remove `$featuredCourses` query from `HomeController::index()`
   - No database changes needed yet

2. **Full Rollback** (After Deployment):
   - Run migration down: `php artisan migrate:rollback --step=1`
   - Remove featured scope from Course model
   - Remove view section

**Risk:** Low - isolated changes, no breaking modifications to existing features

## Future Enhancements

### Phase 2 (Not in Scope)

1. **Course Popularity Tracking**
   - Add `views_count` column to courses table
   - Implement view tracking similar to articles
   - Create `popular()` and `trending()` scopes

2. **Dynamic Course Sections**
   - "Most Popular Courses" section (by enrollments)
   - "New Courses" section (by created_at)
   - "Trending Courses" section (by recent enrollments)

3. **Advanced Filtering**
   - Filter by course category
   - Filter by level (Beginner/Intermediate/Advanced)
   - Filter by price range

4. **Instructor Showcase**
   - Link instructor name to instructor profile
   - Show instructor avatar/thumbnail
   - Display instructor credentials

5. **Course Cards Enhancement**
   - Show lesson count (e.g., "12 lessons")
   - Show duration (e.g., "3 hours")
   - Show completion rate
   - Add "Preview" button for trailer video modal

## Testing Strategy

### Manual Testing Checklist

- [ ] Homepage loads without errors
- [ ] Courses section appears at position 5 (after Recommendations)
- [ ] 4 featured courses displayed (or fewer if <4 available)
- [ ] Course cards show: thumbnail, title, instructor, level, price, enrollment count (if >0)
- [ ] "Browse All Courses" button links to `/courses`
- [ ] Course cards link to `/courses/{slug}`
- [ ] Hover states work (shadow-lg transition)
- [ ] Responsive layout: 1 col mobile, 2 col tablet, 4 col desktop
- [ ] No N+1 queries (check Laravel Debugbar or `DB::listen()`)
- [ ] No visual regression in existing sections

### Edge Cases

- [ ] Zero featured courses: Section should not appear (or show empty state)
- [ ] One featured course: Grid still renders correctly
- [ ] Long course titles: `line-clamp-2` truncates properly
- [ ] Missing thumbnails: Default image or placeholder
- [ ] Free courses (price=0): Display "Free" instead of "Rp 0"
- [ ] Very long instructor names: Truncate with ellipsis

### Performance Testing

- [ ] Query count: Homepage should add only 1 additional query
- [ ] Page load time: No significant increase (<50ms acceptable)
- [ ] Image loading: Lazy load course thumbnails if needed

## Deployment Notes

### Pre-Deployment

1. Run migration: `php artisan migrate`
2. Feature 2-4 courses: Update via admin panel or tinker
3. Clear caches: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
4. Test on staging environment

### Post-Deployment

1. Monitor error logs for any issues
2. Check analytics: Track "Browse All Courses" button clicks
3. Gather feedback: A/B test different section titles if needed
4. Iterate: Adjust course selection based on conversion data

## Open Technical Questions

1. **Lazy Loading Images**: Should course thumbnails use lazy loading? (Not critical for 4 images)
   - **Recommendation**: No lazy loading for simplicity, only 4 images won't impact performance significantly

2. **Cache Strategy**: Should featured courses be cached?
   - **Recommendation**: No caching initially (admin changes should reflect immediately)

3. **Admin Interface**: How should admins toggle `is_featured`?
   - **Recommendation**: Add checkbox in course edit form, no complex UI needed

4. **SEO Impact**: Does adding courses section affect article-focused SEO?
   - **Recommendation**: Neutral impact, courses section doesn't change article metadata

## References

- **Style Guide**: `docs/STYLE_GUIDE.md`
- **Project Context**: `openspec/project.md`
- **Homepage Sections Spec**: `openspec/specs/homepage-sections/spec.md`
- **Course Model**: `app/Models/Course.php`
- **Home Controller**: `app/Http/Controllers/HomeController.php`
- **Home View**: `resources/views/home.blade.php`
