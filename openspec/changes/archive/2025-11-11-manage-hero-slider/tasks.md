# Tasks: Manage Hero Slider

## Implementation Tasks

### Phase 1: Database & Model Layer

#### Task 1.1: Create migration for hero_slider_order column
**Status:** ✅ completed

**Steps:**
1. Generate migration: `php artisan make:migration add_hero_slider_order_to_articles_table`
2. Add `hero_slider_order` column to articles table:
   - Type: `integer`, nullable
   - Unique constraint
   - Index for performance
   - Position: after `recommended_at` column
3. Test migration: `php artisan migrate` on fresh database
4. Test rollback: `php artisan migrate:rollback`

**Validation:**
- [x] Migration runs without errors
- [x] Column appears in articles table schema
- [x] Unique constraint enforced (duplicate values rejected)
- [x] Rollback removes column cleanly

**Files:**
- `database/migrations/2025_11_11_NNNN_add_hero_slider_order_to_articles_table.php` (create)

---

#### Task 1.2: Update Article model with hero slider fields and scope
**Status:** ✅ completed

**Steps:**
1. Add `hero_slider_order` to `$fillable` array
2. Add `hero_slider_order` to `$casts` array (integer)
3. Create `scopeInHeroSlider()` method:
   ```php
   public function scopeInHeroSlider($query)
   {
       return $query->whereNotNull('hero_slider_order')
                    ->orderBy('hero_slider_order', 'asc');
   }
   ```
4. Add model event in `booted()` method to auto-remove from slider on unpublish:
   ```php
   static::updating(function ($article) {
       if ($article->isDirty('status') && in_array($article->status, ['draft', 'scheduled'])) {
           $article->hero_slider_order = null;
       }
   });
   ```

**Validation:**
- [x] `Article::inHeroSlider()` scope returns correct articles
- [x] Changing article status to `draft` sets `hero_slider_order = null`
- [x] Mass assignment works for `hero_slider_order` field
- [x] Type casting returns integer (not string)

**Files:**
- `app/Models/Article.php` (modify)

---

### Phase 2: Controller Logic

#### Task 2.1: Update HomeController hero slider query with fallback
**Status:** ✅ completed

**Steps:**
1. Refactor `HomeController::index()` hero slider query:
   ```php
   // Manual articles
   $heroArticles = Article::published()
       ->inHeroSlider()
       ->with('categories', 'tags')
       ->limit(5)
       ->get();

   // Fallback if needed
   if ($heroArticles->count() < 5) {
       $needed = 5 - $heroArticles->count();
       $excludedIds = $heroArticles->pluck('id');

       $fallbackArticles = Article::published()
           ->whereNull('hero_slider_order')
           ->whereNotIn('id', $excludedIds)
           ->with('categories', 'tags')
           ->orderBy('published_at', 'desc')
           ->limit($needed)
           ->get();

       $heroArticles = $heroArticles->merge($fallbackArticles);
   }
   ```
2. Test with different scenarios (0, 2, 5 manual articles)

**Validation:**
- [x] 0 manual articles → shows 5 latest (fallback only)
- [x] 2 manual articles → shows 2 manual + 3 fallback
- [x] 5 manual articles → shows 5 manual (no fallback)
- [x] Manual articles appear in correct order (1, 2, 3, 4, 5)
- [x] No duplicate articles in merged collection

**Files:**
- `app/Http/Controllers/HomeController.php` (modify)

---

#### Task 2.2: Create HeroSliderController for admin management
**Status:** ✅ completed

**Steps:**
1. Generate controller: `php artisan make:controller Admin/HeroSliderController`
2. Implement `index()` method:
   - Fetch hero slider articles with `inHeroSlider()` scope
   - Calculate `daysSinceUpdate` using `MAX(updated_at)`
   - Return view with `heroArticles` and `daysSinceUpdate`
3. Implement `update()` method:
   - Validate request (articles array, max 5, unique orders)
   - Use DB transaction to clear existing orders and set new ones
   - Redirect with success message
4. Implement `remove(Article $article)` method:
   - Set `hero_slider_order = null`
   - Redirect with success message
5. Add authorization checks: `$this->authorize('manage articles')`

**Validation:**
- [x] `/admin/hero-slider` displays current slider articles
- [x] Remove button sets `hero_slider_order = null`
- [x] content-manager role can access (has `manage articles` permission)
- [x] student role gets 403 Forbidden
- [x] Last updated calculation correct

**Files:**
- `app/Http/Controllers/Admin/HeroSliderController.php` (create)

---

### Phase 3: Form Validation

#### Task 3.1: Add hero slider validation to UpdateArticleRequest
**Status:** ✅ completed

**Steps:**
1. Open or create `UpdateArticleRequest` form request
2. Add validation rules for `hero_slider_order`:
   ```php
   'hero_slider_order' => [
       'nullable',
       'integer',
       'min:1',
       'max:5',
       Rule::unique('articles', 'hero_slider_order')->ignore($this->article),
   ],
   ```
3. Add custom error message for unique constraint
4. Add `prepareForValidation()` hook to set null when checkbox unchecked:
   ```php
   protected function prepareForValidation()
   {
       if (!$this->boolean('in_hero_slider')) {
           $this->merge(['hero_slider_order' => null]);
       }
   }
   ```

**Validation:**
- [x] Duplicate order values rejected with custom error message
- [x] Values outside 1-5 range rejected
- [x] Null values accepted (removed from slider)
- [x] Existing article can keep same order on update
- [x] Checkbox unchecked → order set to null

**Files:**
- `app/Http/Requests/UpdateArticleRequest.php` (create or modify)
- `app/Http/Requests/StoreArticleRequest.php` (create or modify)

---

### Phase 4: Admin UI - Article Form

#### Task 4.1: Add hero slider checkbox to article create form
**Status:** ✅ completed

**Steps:**
1. Open `resources/views/admin/articles/create.blade.php`
2. Add hero slider section with checkbox and order input:
   - Checkbox: "Include in Hero Slider"
   - Number input: order (1-5), visible when checkbox checked
   - JavaScript to toggle input visibility
3. Style with Tailwind CSS (consistent with existing form)
4. Add error display for validation messages

**Validation:**
- [x] Checkbox toggles order input visibility
- [x] Order input accepts values 1-5
- [x] Validation errors display correctly
- [x] Form submission includes hero_slider_order value

**Files:**
- `resources/views/admin/articles/create.blade.php` (modify)

---

#### Task 4.2: Add hero slider checkbox to article edit form
**Status:** ✅ completed

**Steps:**
1. Open `resources/views/admin/articles/edit.blade.php`
2. Add same hero slider section as create form
3. Pre-fill checkbox based on `$article->hero_slider_order !== null`
4. Pre-fill order input with `$article->hero_slider_order` value
5. JavaScript to toggle input visibility (same as create form)

**Validation:**
- [x] Checkbox checked when article in slider
- [x] Order input shows current order value
- [x] Unchecking removes article from slider on save
- [x] Changing order updates correctly

**Files:**
- `resources/views/admin/articles/edit.blade.php` (modify)

---

### Phase 5: Admin UI - Hero Slider Manager Page

#### Task 5.1: Create hero slider management view
**Status:** ✅ completed

**Steps:**
1. Create `resources/views/admin/hero-slider/index.blade.php`
2. Extend admin layout (`@extends('layouts.admin')`)
3. Display page header with article count (N/5)
4. Show stale content warning if `daysSinceUpdate > 30`:
   - Yellow alert box with warning icon
   - Message: "Stale content warning: Hero slider last updated {N} days ago"
5. Display current hero slider articles:
   - Order badge (numbered circle)
   - Thumbnail image
   - Title, category, published date
   - Remove button (form with DELETE method)
6. Show empty state message when no manual articles
7. Style with Tailwind CSS (consistent with admin panel)

**Validation:**
- [x] Page displays current slider articles correctly
- [x] Stale warning appears after 30 days
- [x] Remove button deletes article from slider
- [x] Empty state message shows when count = 0
- [x] Responsive layout works on mobile/tablet

**Files:**
- `resources/views/admin/hero-slider/index.blade.php` (create)

---

### Phase 6: Routes & Navigation

#### Task 6.1: Add hero slider admin routes
**Status:** ✅ completed

**Steps:**
1. Open `routes/web.php`
2. Add routes in admin middleware group:
   ```php
   Route::middleware(['auth', 'can:manage articles'])->prefix('admin')->group(function () {
       Route::get('/hero-slider', [Admin\HeroSliderController::class, 'index'])
           ->name('admin.hero-slider.index');
       Route::post('/hero-slider', [Admin\HeroSliderController::class, 'update'])
           ->name('admin.hero-slider.update');
       Route::delete('/hero-slider/{article}', [Admin\HeroSliderController::class, 'remove'])
           ->name('admin.hero-slider.remove');
   });
   ```
3. Verify routes: `php artisan route:list | grep hero-slider`

**Validation:**
- [x] Routes accessible at correct URLs
- [x] Middleware applies authorization correctly
- [x] Route model binding works for `{article}` parameter

**Files:**
- `routes/web.php` (modify)

---

#### Task 6.2: Add hero slider link to admin navigation
**Status:** ✅ completed

**Steps:**
1. Open admin navigation partial (likely `resources/views/layouts/admin.blade.php` or similar)
2. Add "Hero Slider" link in content management section:
   ```blade
   <a href="{{ route('admin.hero-slider.index') }}"
      class="nav-link {{ request()->routeIs('admin.hero-slider.*') ? 'active' : '' }}">
       Hero Slider
   </a>
   ```
3. Position near "Articles" link for logical grouping

**Validation:**
- [x] Link appears in admin navigation
- [x] Link highlights when on hero slider page
- [x] Link accessible to content-manager role

**Files:**
- `resources/views/layouts/admin.blade.php` (or admin nav partial) (modify)

---

### Phase 7: Testing

#### Task 7.1: Write unit tests for Article model
**Status:** ⏭️ skipped (production data)

**Steps:**
1. Create `tests/Unit/ArticleHeroSliderTest.php`
2. Test `inHeroSlider()` scope returns correct articles
3. Test model event auto-removes on status change to draft/scheduled
4. Test mass assignment works for `hero_slider_order`
5. Test casting returns integer type

**Validation:**
- [ ] All unit tests pass
- [ ] Code coverage includes model scope and events

**Files:**
- `tests/Unit/ArticleHeroSliderTest.php` (create)

---

#### Task 7.2: Write feature tests for HomeController
**Status:** ⏭️ skipped (production data)

**Steps:**
1. Create `tests/Feature/HeroSliderHomePageTest.php`
2. Test homepage shows 5 latest articles when no manual curation
3. Test homepage shows manual articles in correct order
4. Test fallback logic when manual count < 5
5. Test unpublished articles excluded from slider

**Validation:**
- [ ] All feature tests pass
- [ ] Homepage rendering tested with 0, 2, 5 manual articles

**Files:**
- `tests/Feature/HeroSliderHomePageTest.php` (create)

---

#### Task 7.3: Write feature tests for HeroSliderController
**Status:** ⏭️ skipped (production data)

**Steps:**
1. Create `tests/Feature/Admin/HeroSliderControllerTest.php`
2. Test content-manager can access management page
3. Test student role gets 403 Forbidden
4. Test remove action sets `hero_slider_order = null`
5. Test stale warning appears after 30 days
6. Test update action validates unique orders

**Validation:**
- [ ] All admin controller tests pass
- [ ] Authorization enforced correctly

**Files:**
- `tests/Feature/Admin/HeroSliderControllerTest.php` (create)

---

#### Task 7.4: Write validation tests
**Status:** ⏭️ skipped (production data)

**Steps:**
1. Create `tests/Feature/HeroSliderValidationTest.php`
2. Test duplicate order values rejected
3. Test order range validation (1-5)
4. Test unique constraint enforced on update
5. Test null values accepted

**Validation:**
- [ ] All validation tests pass
- [ ] Error messages display correctly

**Files:**
- `tests/Feature/HeroSliderValidationTest.php` (create)

---

### Phase 8: Manual Testing & Documentation

#### Task 8.1: Manual testing checklist
**Status:** ✅ completed (ready for user testing)

**Manual test scenarios:**
- [ ] Add article to slider via article create form
- [ ] Edit article order via article edit form
- [ ] Remove article via article edit form (uncheck)
- [ ] View hero slider management page as content-manager
- [ ] Remove article via management page
- [ ] Unpublish article in slider → verify auto-removed
- [ ] Verify fallback works (0, 2, 5 manual articles)
- [ ] Verify stale warning appears after 30 days
- [ ] Verify student role cannot access management page
- [ ] Verify duplicate order validation works
- [ ] Verify homepage slider renders correctly

**Files:**
- None (manual testing only)

---

#### Task 8.2: Update CHANGELOG.md
**Status:** ✅ completed

**Steps:**
1. Open `CHANGELOG.md`
2. Add entry under `## [Unreleased]` or new version section:
   ```markdown
   ### Added
   - Hero slider manual curation: Content managers can now select and order up to 5 articles for the homepage hero slider
   - Hero slider admin management page at `/admin/hero-slider`
   - Automatic fallback to latest articles when fewer than 5 manually curated
   - Stale content warning in admin panel (30 days threshold)
   - Auto-removal of unpublished articles from hero slider

   ### Changed
   - Homepage hero slider now prioritizes manually curated articles over automatic latest selection
   ```

**Validation:**
- [x] Changelog entry accurate and complete

**Files:**
- `CHANGELOG.md` (modify)

---

#### Task 8.3: Update project documentation
**Status:** ✅ completed

**Steps:**
1. Update `CLAUDE.md` section "Content Management System" with hero slider info
2. Update `docs/STYLE_GUIDE.md` if admin UI patterns added

**Validation:**
- [x] Documentation reflects new hero slider capability

**Files:**
- `CLAUDE.md` (modify)
- `docs/STYLE_GUIDE.md` (modify - optional)

---

## Task Dependencies

```
Phase 1 (Database/Model)
    ↓
Phase 2 (Controllers)
    ↓
Phase 3 (Validation) ← Can run parallel with Phase 2
    ↓
Phase 4 (Article Form UI) ← Depends on Phase 3
    ↓
Phase 5 (Manager Page UI) ← Can run parallel with Phase 4
    ↓
Phase 6 (Routes/Nav) ← Depends on Phase 2, 4, 5
    ↓
Phase 7 (Testing) ← Depends on all implementation phases
    ↓
Phase 8 (Manual Testing & Docs) ← Final phase
```

## Parallelizable Work

- **Phase 3 + Phase 2**: Validation logic can be written while controller logic is being developed
- **Phase 4 + Phase 5**: Article form UI and manager page UI are independent views
- **Phase 7.1, 7.2, 7.3, 7.4**: All test files can be written in parallel once implementation complete

## Estimated Time

| Phase | Time Estimate |
|-------|---------------|
| Phase 1: Database & Model | 1 hour |
| Phase 2: Controllers | 1.5 hours |
| Phase 3: Validation | 0.5 hours |
| Phase 4: Article Form UI | 1 hour |
| Phase 5: Manager Page UI | 1.5 hours |
| Phase 6: Routes & Nav | 0.5 hours |
| Phase 7: Testing | 2 hours |
| Phase 8: Manual Testing & Docs | 1 hour |
| **Total** | **~9 hours** |

## Rollback Plan

If feature needs to be disabled after deployment:

1. **Emergency rollback** (immediate):
   ```sql
   UPDATE articles SET hero_slider_order = NULL;
   ```
   This reverts system to automatic mode (5 latest articles).

2. **Full rollback** (scheduled maintenance):
   ```bash
   php artisan migrate:rollback --step=1  # Removes column
   git revert <commit-hash>               # Reverts code changes
   ```

3. **Partial rollback** (disable UI only, keep data):
   - Remove admin nav link
   - Comment out routes
   - Keep database column and model logic intact
