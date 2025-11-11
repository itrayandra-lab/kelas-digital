# Design: Manage Hero Slider

## Architecture Overview

This feature adds manual curation capabilities to the hero slider while preserving automatic fallback behavior. The design prioritizes simplicity, backward compatibility, and fail-safe operation.

## System Components

### 1. Database Layer

**Schema Change:**
```php
// Migration: 2025_11_11_NNNN_add_hero_slider_order_to_articles_table.php
Schema::table('articles', function (Blueprint $table) {
    $table->integer('hero_slider_order')->nullable()->unique()->after('recommended_at');
    $table->index('hero_slider_order'); // For query performance
});
```

**Design Decisions:**
- **Integer type** (not boolean) allows explicit ordering (1-5)
- **Nullable** enables clear distinction: null = not in slider, 1-5 = in slider at position
- **Unique constraint** prevents duplicate orders (enforced at database level)
- **Index added** for query performance on hero slider queries
- **Positioned after recommended_at** maintains logical grouping with other curation fields

**Why not a separate table?**
- Slider links directly to articles (no custom content)
- Avoids JOIN overhead for every page load
- Simpler data model with fewer moving parts
- Easier to understand and maintain

### 2. Model Layer

**Article Model Changes:**

```php
// app/Models/Article.php

protected $fillable = [
    // ... existing fields
    'hero_slider_order',
];

protected $casts = [
    // ... existing casts
    'hero_slider_order' => 'integer',
];

// Scope for hero slider articles
public function scopeInHeroSlider($query)
{
    return $query->whereNotNull('hero_slider_order')
                 ->orderBy('hero_slider_order', 'asc');
}

// Model event: auto-remove from slider when unpublished
protected static function booted()
{
    static::updating(function ($article) {
        // If status changing to draft/scheduled, remove from hero slider
        if ($article->isDirty('status') && in_array($article->status, ['draft', 'scheduled'])) {
            $article->hero_slider_order = null;
        }
    });
}
```

**Design Decisions:**
- **Scope method** encapsulates hero slider query logic for reusability
- **Model event** ensures automatic cleanup (no manual intervention needed)
- **Status change detection** via `isDirty()` prevents unnecessary updates
- **Null assignment** on unpublish maintains data integrity without manual cleanup

### 3. Controller Layer

**HomeController Refactor:**

```php
// app/Http/Controllers/HomeController.php

public function index()
{
    // 1. Hero Slider - Hybrid approach
    $heroArticles = \App\Models\Article::published()
        ->inHeroSlider()
        ->with('categories', 'tags')
        ->limit(5)
        ->get();

    // Fallback: fill remaining slots with latest articles
    if ($heroArticles->count() < 5) {
        $needed = 5 - $heroArticles->count();
        $excludedIds = $heroArticles->pluck('id');

        $fallbackArticles = \App\Models\Article::published()
            ->whereNull('hero_slider_order')
            ->whereNotIn('id', $excludedIds)
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc')
            ->limit($needed)
            ->get();

        $heroArticles = $heroArticles->merge($fallbackArticles);
    }

    // ... rest of homepage logic
}
```

**New Admin Controller:**

```php
// app/Http/Controllers/Admin/HeroSliderController.php

class HeroSliderController extends Controller
{
    public function index()
    {
        $this->authorize('manage articles'); // Reuse existing permission

        $heroArticles = Article::inHeroSlider()
            ->with('categories')
            ->get();

        $lastUpdated = Article::whereNotNull('hero_slider_order')
            ->max('updated_at');

        $daysSinceUpdate = $lastUpdated ? now()->diffInDays($lastUpdated) : null;

        return view('admin.hero-slider.index', compact('heroArticles', 'daysSinceUpdate'));
    }

    public function update(Request $request)
    {
        $this->authorize('manage articles');

        $validated = $request->validate([
            'articles' => 'required|array|max:5',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.order' => 'required|integer|min:1|max:5|distinct',
        ]);

        DB::transaction(function () use ($validated) {
            // Clear all existing hero slider orders
            Article::whereNotNull('hero_slider_order')->update(['hero_slider_order' => null]);

            // Set new orders
            foreach ($validated['articles'] as $articleData) {
                Article::find($articleData['id'])->update([
                    'hero_slider_order' => $articleData['order']
                ]);
            }
        });

        return redirect()->route('admin.hero-slider.index')
            ->with('success', 'Hero slider updated successfully');
    }

    public function remove(Article $article)
    {
        $this->authorize('manage articles');

        $article->update(['hero_slider_order' => null]);

        return redirect()->route('admin.hero-slider.index')
            ->with('success', 'Article removed from hero slider');
    }
}
```

**Design Decisions:**
- **Reuse existing permission** (`manage articles`) - content-manager role already has this
- **Transaction wrapper** ensures atomicity when updating multiple orders
- **Clear-then-set pattern** prevents unique constraint violations
- **Separate remove method** for explicit article removal (cleaner than update with null)
- **Last updated tracking** enables stale content warnings

### 4. View Layer

**Admin Form Enhancement (Article Edit):**

```blade
{{-- resources/views/admin/articles/edit.blade.php --}}

<div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hero Slider</h3>

    <div class="flex items-center gap-4">
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="in_hero_slider"
                   id="in_hero_slider"
                   {{ old('in_hero_slider', $article->hero_slider_order !== null) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="text-sm font-medium text-gray-700">Include in Hero Slider</span>
        </label>

        <div id="order-input-container" class="{{ old('in_hero_slider', $article->hero_slider_order !== null) ? '' : 'hidden' }}">
            <label for="hero_slider_order" class="text-sm font-medium text-gray-700">Order (1-5)</label>
            <input type="number"
                   name="hero_slider_order"
                   id="hero_slider_order"
                   min="1"
                   max="5"
                   value="{{ old('hero_slider_order', $article->hero_slider_order) }}"
                   class="w-20 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
    </div>

    @error('hero_slider_order')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
    // Toggle order input visibility
    document.getElementById('in_hero_slider').addEventListener('change', function() {
        const orderContainer = document.getElementById('order-input-container');
        const orderInput = document.getElementById('hero_slider_order');

        if (this.checked) {
            orderContainer.classList.remove('hidden');
            orderInput.required = true;
        } else {
            orderContainer.classList.add('hidden');
            orderInput.required = false;
            orderInput.value = '';
        }
    });
</script>
```

**Dedicated Manager Page:**

```blade
{{-- resources/views/admin/hero-slider/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Manage Hero Slider')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Hero Slider Management</h1>
        <p class="mt-2 text-gray-600">Manage articles displayed in the homepage hero slider (max 5)</p>

        @if($daysSinceUpdate && $daysSinceUpdate > 30)
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <p class="text-sm text-yellow-800">
                    ⚠️ <strong>Stale content warning:</strong> Hero slider last updated {{ $daysSinceUpdate }} days ago.
                    Consider refreshing with new content.
                </p>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Hero Slider Articles ({{ $heroArticles->count() }}/5)</h2>

            @if($heroArticles->isEmpty())
                <p class="text-gray-500 italic">No articles manually added. Slider will show 5 latest published articles.</p>
            @else
                <div class="space-y-4">
                    @foreach($heroArticles as $article)
                        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold">
                                {{ $article->hero_slider_order }}
                            </div>

                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/100' }}"
                                 alt="{{ $article->title }}"
                                 class="w-20 h-20 object-cover rounded">

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $article->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $article->categories->first()->name ?? 'Uncategorized' }} •
                                    {{ $article->published_at->format('d M Y') }}
                                </p>
                            </div>

                            <form action="{{ route('admin.hero-slider.remove', $article) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- TODO: Add article search/selection UI for adding new articles --}}
</div>
@endsection
```

**Design Decisions:**
- **Checkbox pattern** in article form for quick inclusion (familiar UX)
- **Conditional visibility** for order input (reduces clutter)
- **Dedicated page** provides overview and bulk management
- **Stale content warning** uses visual indicator (yellow alert box)
- **Order badge** (numbered circles) provides clear visual hierarchy
- **Inline remove action** for quick cleanup

### 5. Validation Layer

**Form Request Validation:**

```php
// app/Http/Requests/UpdateArticleRequest.php

public function rules()
{
    return [
        // ... existing rules
        'hero_slider_order' => [
            'nullable',
            'integer',
            'min:1',
            'max:5',
            Rule::unique('articles', 'hero_slider_order')->ignore($this->article),
        ],
    ];
}

public function messages()
{
    return [
        'hero_slider_order.unique' => 'This slider position is already taken. Please choose a different order (1-5).',
    ];
}

protected function prepareForValidation()
{
    // If checkbox unchecked, set order to null
    if (!$this->boolean('in_hero_slider')) {
        $this->merge(['hero_slider_order' => null]);
    }
}
```

**Design Decisions:**
- **Unique rule with ignore** prevents conflicts on update (can keep same order)
- **Custom error message** provides clear guidance to user
- **prepareForValidation hook** handles checkbox-to-value conversion
- **Nullable rule** allows explicit removal from slider

## Data Flow

### Adding Article to Hero Slider

```
User Action (Admin Panel)
    ↓
Check "Include in Hero Slider" + set order (1-5)
    ↓
Submit form → UpdateArticleRequest validation
    ↓
Unique constraint check (database + Laravel validation)
    ↓
Article model update (hero_slider_order = N)
    ↓
Redirect with success message
    ↓
Homepage query includes article in hero slider
```

### Unpublishing Article in Slider

```
User Action (Change status to draft/scheduled)
    ↓
Article model updating event fires
    ↓
isDirty('status') check passes
    ↓
Auto-set hero_slider_order = null
    ↓
Article saved with null order
    ↓
Homepage query excludes article (whereNotNull filter)
```

### Homepage Rendering

```
HomeController::index() called
    ↓
Query 1: Get manual hero articles (whereNotNull hero_slider_order, orderBy asc, limit 5)
    ↓
Count < 5? → Query 2: Fallback articles (whereNull hero_slider_order, latest, limit = 5 - count)
    ↓
Merge collections (manual first, then fallback)
    ↓
Pass to view → Splide.js renders slider
```

## Performance Considerations

### Query Optimization
- **Index on hero_slider_order**: Speeds up `whereNotNull()` and `orderBy()` queries
- **Eager loading**: `with('categories', 'tags')` prevents N+1 queries
- **Limit clause**: Always fetch max 5 articles
- **Simple WHERE clauses**: No complex JOINs or subqueries

**Expected Performance:**
- Hero slider query: <10ms (indexed column, small result set)
- Fallback query: <15ms (indexed published_at, limit 5)
- Total overhead: <5ms compared to current implementation

### Caching Strategy (Optional Future Enhancement)
```php
// Cache hero slider for 1 hour
$heroArticles = Cache::remember('hero_slider_articles', 3600, function () {
    // ... query logic
});

// Invalidate cache on article update
Article::updated(function ($article) {
    if ($article->wasChanged('hero_slider_order')) {
        Cache::forget('hero_slider_articles');
    }
});
```

## Security Considerations

### Authorization
- **Existing permission reuse**: `can:manage articles` (content-manager role)
- **No new permissions needed**: Reduces complexity, leverages existing RBAC
- **Controller authorization**: `$this->authorize('manage articles')` in all methods

### Input Validation
- **Type safety**: Integer casting prevents injection
- **Range validation**: Min 1, max 5 enforced at validation layer
- **Unique constraint**: Prevents duplicate orders (DB + Laravel)
- **Mass assignment protection**: Only `hero_slider_order` added to fillable

### Data Integrity
- **Automatic cleanup**: Model events prevent orphaned slider entries
- **Transaction wrapper**: Ensures atomic updates when reordering multiple articles
- **Published filter**: Frontend always filters by published status (security defense-in-depth)

## Error Handling

### Unique Order Conflict
**Scenario**: User tries to set order = 2, but another article already has order = 2

**Handling**:
1. Laravel validation fails with custom message: "This slider position is already taken"
2. Form redisplays with error message
3. User chooses different order or removes existing article first

### Unpublished Article in Slider
**Scenario**: Article in slider gets unpublished

**Handling**:
1. Model `updating` event detects status change
2. Auto-set `hero_slider_order = null`
3. Article removed from slider on next page load
4. No admin intervention required

### All Slider Articles Unpublished
**Scenario**: All 5 manual articles get unpublished at once

**Handling**:
1. Hero slider query returns 0 articles
2. Fallback logic activates: fetch 5 latest published articles
3. Slider continues working with automatic content
4. No blank/broken slider state possible

## Migration Path

### From Current (Automatic) to New (Managed)

**Phase 1: Deploy**
1. Run migration to add `hero_slider_order` column (all null initially)
2. Deploy code changes
3. Homepage continues working identically (all orders null → fallback to latest)

**Phase 2: Gradual Adoption**
1. Content team optionally adds articles to slider via admin panel
2. Mixed mode: Some manual, some automatic
3. No forced migration required

**Phase 3: Full Adoption (Optional)**
1. Content team manages all 5 slider slots manually
2. Automatic fallback becomes safety net only
3. Warning system encourages active curation

**Rollback Plan:**
If feature causes issues, simply set all `hero_slider_order` values to null via SQL:
```sql
UPDATE articles SET hero_slider_order = NULL;
```
System immediately reverts to old behavior (latest 5 articles).

## Alternative UI Patterns Considered

### Drag-and-Drop Reordering
**Pros**: Intuitive, visual, fast reordering
**Cons**: Requires JavaScript library (Sortable.js), more complex state management
**Decision**: Defer to future iteration (v2) - number input sufficient for v1

### Inline Editing (Hero Slider Page)
**Pros**: All edits in one place, no need to visit article edit form
**Cons**: Duplicate UI logic, harder to maintain
**Decision**: Keep edit in article form for consistency with existing admin patterns

### Schedule-based Rotation
**Pros**: Time-based auto-switching (e.g., Monday-Wednesday show article A)
**Cons**: Complex scheduling logic, timezone handling, edge cases
**Decision**: Out of scope - manual ordering sufficient for current needs

## Open Questions & Future Enhancements

### Questions for Future Consideration
1. **Analytics**: Should we track clicks/impressions per slider article?
2. **A/B Testing**: Would content team benefit from variant testing?
3. **Internationalization**: How does slider work with multi-language content?
4. **Custom CTAs**: Should slider allow custom button text per article?

### Potential v2 Features
1. **Drag-and-drop UI**: Replace number input with visual reordering
2. **Preview mode**: See slider changes before publishing
3. **Scheduled rotation**: Time-based auto-switching of slider content
4. **Slider templates**: Pre-defined layouts (full-width, split, carousel variants)
5. **Course inclusion**: Allow courses in hero slider alongside articles
6. **Click analytics**: Track which slider items get most engagement

## Testing Strategy

### Unit Tests
- Model scope `inHeroSlider()` returns correct articles
- Model event auto-removes unpublished articles
- Validation rules enforce uniqueness and range

### Feature Tests
- HomeController returns correct hero slider articles
- Fallback logic activates when <5 manual articles
- Admin controller enforces authorization
- Form submission updates hero_slider_order correctly
- Unique order constraint prevents duplicates

### Manual Testing Checklist
- [ ] Add article to slider via article edit form
- [ ] Reorder articles in slider manager page
- [ ] Unpublish article in slider → auto-removed
- [ ] All 5 slots filled manually → no fallback
- [ ] 2 slots filled manually → 3 fallback articles shown
- [ ] Stale warning appears after 30 days
- [ ] content-manager role can access slider management
- [ ] student role cannot access slider management
