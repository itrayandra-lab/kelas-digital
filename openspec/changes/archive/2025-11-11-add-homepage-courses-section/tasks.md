# Implementation Tasks: Add Homepage Courses Section

**Change ID**: `add-homepage-courses-section`
**Status**: ✅ Completed
**Estimated Effort**: 2-3 hours

---

## Task Order & Status

### Phase 1: Database & Model Setup

**Status**: ✅ Completed

#### Task 1.1: Create migration for featured course fields

**Status**: ✅ Completed
**Estimated**: 15 minutes
**Blocking**: Task 1.2, 1.3

**Description**: Create a migration to add `is_featured` and `featured_at` columns to the `courses` table with proper indexing.

**Implementation Steps**:
1. Create migration file: `php artisan make:migration add_featured_fields_to_courses_table`
2. Add columns in `up()` method:
   - `$table->boolean('is_featured')->default(false)->after('level')`
   - `$table->timestamp('featured_at')->nullable()->after('is_featured')`
   - `$table->index('is_featured')`
3. Add column drops in `down()` method:
   - `$table->dropIndex(['is_featured'])`
   - `$table->dropColumn(['is_featured', 'featured_at'])`

**Validation**:
- [x] Run `php artisan migrate` successfully
- [x] Verify columns exist: `php artisan tinker` → `Schema::hasColumn('courses', 'is_featured')`
- [x] Verify index exists in database: Check `SHOW INDEXES FROM courses`
- [x] Run `php artisan migrate:rollback --step=1` successfully

**Files**:
- `database/migrations/2025_11_11_000001_add_featured_fields_to_courses_table.php` (new)

---

#### Task 1.2: Add featured fields to Course model fillable

**Status**: ✅ Completed
**Estimated**: 5 minutes
**Depends On**: Task 1.1

**Description**: Update the Course model to include `is_featured` and `featured_at` in fillable array and add casts.

**Implementation Steps**:
1. Open `app/Models/Course.php`
2. Add to `$fillable` array:
   ```php
   'is_featured',
   'featured_at'
   ```
3. Add to `$casts` array:
   ```php
   'featured_at' => 'datetime',
   ```

**Validation**:
- [x] No syntax errors: Run `php artisan tinker` → `new App\Models\Course()`
- [x] Mass assignment works: `Course::create(['is_featured' => true, ...])`
- [x] Datetime casting works: Create course, verify `$course->featured_at instanceof Carbon`

**Files**:
- `app/Models/Course.php` (modified, lines ~17-28)

---

#### Task 1.3: Add featured scope to Course model

**Status**: ✅ Completed
**Estimated**: 10 minutes
**Depends On**: Task 1.1

**Description**: Implement `scopeFeatured()` method to filter courses marked as featured.

**Implementation Steps**:
1. Open `app/Models/Course.php`
2. Add method after `sluggable()` method:
   ```php
   /**
    * Scope a query to only include featured courses
    */
   public function scopeFeatured($query)
   {
       return $query->where('is_featured', true)
                    ->orderBy('featured_at', 'desc');
   }
   ```

**Validation**:
- [x] Scope callable: `Course::featured()->get()` works
- [x] Scope chainable: `Course::featured()->limit(4)->get()` works
- [x] Ordering correct: Verify `featured_at` descending order
- [x] Empty result: Returns empty collection when no featured courses

**Files**:
- `app/Models/Course.php` (modified, add after line ~50)

---

#### Task 1.4: Seed featured courses for testing

**Status**: ✅ Completed
**Estimated**: 10 minutes
**Depends On**: Task 1.1, 1.2, 1.3
**Optional**: Can defer to manual testing

**Description**: Mark 2-4 existing courses as featured for homepage testing.

**Implementation Steps**:
1. Run `php artisan tinker`
2. Execute:
   ```php
   Course::limit(4)->update([
       'is_featured' => true,
       'featured_at' => now()
   ]);
   ```
3. Verify: `Course::featured()->count()` returns 4

**Validation**:
- [x] At least 2 courses have `is_featured = true`
- [x] Featured courses have `featured_at` timestamps
- [x] `Course::featured()->get()` returns expected courses

**Files**:
- None (manual database update via tinker)

---

### Phase 2: Controller & Data Fetching

**Status**: ✅ Completed

#### Task 2.1: Add featured courses query to HomeController

**Status**: ✅ Completed
**Estimated**: 15 minutes
**Depends On**: Task 1.3
**Blocking**: Task 3.1

**Description**: Fetch featured courses in `HomeController::index()` with proper eager loading.

**Implementation Steps**:
1. Open `app/Http/Controllers/HomeController.php`
2. Add query after `$recommendedArticles` (after line ~37):
   ```php
   // 4.5. Featured Courses - 4 manually curated courses
   $featuredCourses = \App\Models\Course::featured()
       ->with(['category', 'enrollments'])
       ->withCount('enrollments')
       ->limit(4)
       ->get();
   ```
3. Update `compact()` return (line ~78):
   ```php
   return view('home', compact(
       'heroArticles',
       'latestArticles',
       'popularArticles',
       'recommendedArticles',
       'featuredCourses',        // NEW
       'trendingArticles',
       'featuredCategory',
       'featuredCategoryArticles',
       'moreArticles'
   ));
   ```

**Validation**:
- [x] Homepage loads without errors
- [x] `$featuredCourses` variable available in view
- [x] Eager loading works: No N+1 queries (check Laravel Debugbar)
- [x] Query limited to 4 courses max
- [x] Enrollments count accessible: `$course->enrollments_count`

**Files**:
- `app/Http/Controllers/HomeController.php` (modified, lines ~38-45, ~78-88)

---

### Phase 3: View & Frontend

**Status**: ✅ Completed

#### Task 3.1: Add featured courses section to home view

**Status**: ✅ Completed
**Estimated**: 45 minutes
**Depends On**: Task 2.1
**Blocking**: None

**Description**: Create the featured courses section HTML in `home.blade.php` following style guide patterns.

**Implementation Steps**:
1. Open `resources/views/home.blade.php`
2. Insert section after Recommendations section (after line ~236, before Trending)
3. Add section code:

```blade
{{-- Featured Courses Section --}}
@if($featuredCourses->isNotEmpty())
    <section id="featured-courses" class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Featured Courses</h2>
                    <p class="text-gray-600">Structured learning programs by expert instructors</p>
                </div>
                <a href="{{ route('course.index') }}"
                   class="hidden md:inline-block px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                    Browse All Courses
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
                @foreach($featuredCourses as $course)
                    <a href="{{ route('course.show', $course->slug) }}"
                       class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition block">
                        <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/400x300' }}"
                             alt="{{ $course->title }}"
                             class="w-full h-48 object-cover">
                        <div class="p-6">
                            {{-- Badge --}}
                            <span class="inline-block text-xs font-bold uppercase text-primary-600 mb-2">
                                COURSE
                            </span>

                            {{-- Title --}}
                            <h3 class="text-lg font-bold text-gray-800 mt-2 mb-3 line-clamp-2 hover:text-primary-600 transition">
                                {{ $course->title }}
                            </h3>

                            {{-- Instructor & Level --}}
                            <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                                <span>{{ $course->instructor }}</span>
                                <span>•</span>
                                <span>{{ $course->level }}</span>
                            </div>

                            {{-- Price & Enrollment --}}
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-primary-600">
                                    @if($course->price > 0)
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    @else
                                        Free
                                    @endif
                                </span>
                                @if($course->enrollments_count > 0)
                                    <span class="text-xs text-gray-500">
                                        {{ $course->enrollments_count }} enrolled
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Mobile CTA --}}
            <div class="text-center mt-8 md:hidden">
                <a href="{{ route('course.index') }}"
                   class="inline-block px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                    Browse All Courses
                </a>
            </div>
        </div>
    </section>
@endif
```

**Validation**:
- [x] Section appears on homepage between Recommendations and Trending
- [x] Section only appears if `$featuredCourses->isNotEmpty()`
- [x] All Tailwind classes render correctly
- [x] Course cards display all required information
- [x] Images load (or show placeholder)
- [x] Links work: Cards link to `/courses/{slug}`, button links to `/courses`

**Files**:
- `resources/views/home.blade.php` (modified, insert after line ~236)

---

#### Task 3.2: Test responsive layout across breakpoints

**Status**: ⬜ Not Started
**Estimated**: 20 minutes
**Depends On**: Task 3.1

**Description**: Manually test the courses section on mobile, tablet, and desktop viewports.

**Testing Steps**:
1. Open homepage in browser
2. Test mobile (375px width):
   - [ ] 1 column layout
   - [ ] Gap is 16px (gap-4)
   - [ ] Desktop CTA hidden
   - [ ] Mobile CTA visible below grid
3. Test tablet (768px width):
   - [ ] 2 column layout
   - [ ] Gap is 24px (gap-6)
   - [ ] Desktop CTA visible
   - [ ] Mobile CTA hidden
4. Test desktop (1440px width):
   - [ ] 4 column layout
   - [ ] Gap is 32px (gap-8)
   - [ ] All courses visible in one row

**Validation**:
- [ ] No horizontal scrolling at any breakpoint
- [ ] Cards maintain aspect ratio and readability
- [ ] Hover states work (shadow increases)
- [ ] Text truncation works (line-clamp-2 on titles)

**Files**:
- None (manual testing)

---

#### Task 3.3: Test hover and interaction states

**Status**: ⬜ Not Started
**Estimated**: 10 minutes
**Depends On**: Task 3.1

**Description**: Verify hover states and click interactions work correctly.

**Testing Steps**:
1. Hover over course card:
   - [ ] Shadow transitions from default to `shadow-lg`
   - [ ] Title color changes to `text-primary-600`
   - [ ] Transition is smooth (150ms)
2. Click card:
   - [ ] Navigates to `/courses/{slug}`
   - [ ] Page loads correctly
   - [ ] Opens in same window (not new tab)
3. Click "Browse All Courses" button:
   - [ ] Navigates to `/courses`
   - [ ] Opens in same window

**Validation**:
- [ ] All hover states provide visual feedback
- [ ] No broken links
- [ ] Navigation works as expected

**Files**:
- None (manual testing)

---

### Phase 4: Edge Cases & Polish

**Status**: ⬜ Not Started

#### Task 4.1: Test with varying course counts

**Status**: ⬜ Not Started
**Estimated**: 15 minutes
**Depends On**: Task 3.1

**Description**: Verify section behaves correctly with 0, 1, 2, 3, and 4 featured courses.

**Testing Steps**:
1. Set 0 featured courses:
   - [ ] Section does not appear on homepage
   - [ ] No errors in logs
   - [ ] Trending section appears right after Recommendations
2. Set 1 featured course:
   - [ ] Section appears with 1 card
   - [ ] Grid doesn't break (card not stretched)
3. Set 2 featured courses:
   - [ ] Section appears with 2 cards
   - [ ] Layout correct: 1 col mobile, 2 col tablet, 2 cards desktop
4. Set 3 featured courses:
   - [ ] Section appears with 3 cards
   - [ ] Layout correct: 3 cards on desktop (one row)
5. Set 4 featured courses:
   - [ ] Section appears with 4 cards (full display)

**Validation**:
- [ ] No layout breaks at any count
- [ ] Section gracefully hidden when empty
- [ ] Grid adapts to content

**Files**:
- None (manual testing with database updates)

---

#### Task 4.2: Test enrollment count display logic

**Status**: ⬜ Not Started
**Estimated**: 10 minutes
**Depends On**: Task 3.1

**Description**: Verify enrollment count shows/hides correctly based on value.

**Testing Steps**:
1. Create course with 0 enrollments:
   - [ ] Enrollment count text does not appear
   - [ ] Price still displays correctly
   - [ ] Card layout not broken
2. Create course with 1+ enrollments:
   - [ ] Enrollment count appears: "X enrolled"
   - [ ] Text uses correct styling: `text-xs text-gray-500`
3. Create course with 100+ enrollments:
   - [ ] Number formatted correctly: "123 enrolled" (not "123enrolled")

**Validation**:
- [ ] Conditional rendering works via `@if($course->enrollments_count > 0)`
- [ ] No empty space when hidden

**Files**:
- None (manual testing)

---

#### Task 4.3: Test free course display

**Status**: ⬜ Not Started
**Estimated**: 5 minutes
**Depends On**: Task 3.1

**Description**: Verify courses with price=0 display "Free" instead of "Rp 0".

**Testing Steps**:
1. Create/update course with `price = 0`
2. View homepage
3. Verify:
   - [ ] Price displays as "Free"
   - [ ] Uses same styling: `text-xl font-bold text-primary-600`
   - [ ] No "Rp 0" shown

**Validation**:
- [ ] Blade conditional works: `@if($course->price > 0)... @else Free @endif`

**Files**:
- None (manual testing)

---

#### Task 4.4: Test long text truncation

**Status**: ⬜ Not Started
**Estimated**: 5 minutes
**Depends On**: Task 3.1

**Description**: Verify long course titles truncate properly with `line-clamp-2`.

**Testing Steps**:
1. Create course with very long title (>100 characters)
2. View homepage
3. Verify:
   - [ ] Title truncates to 2 lines max
   - [ ] Ellipsis (...) appears at truncation
   - [ ] Card height remains consistent with other cards

**Validation**:
- [ ] `line-clamp-2` class applied to title
- [ ] No overflow breaking card layout

**Files**:
- None (manual testing)

---

#### Task 4.5: Test placeholder images

**Status**: ⬜ Not Started
**Estimated**: 5 minutes
**Depends On**: Task 3.1

**Description**: Verify courses without thumbnails show placeholder image.

**Testing Steps**:
1. Create course with `thumbnail = null` or empty string
2. View homepage
3. Verify:
   - [ ] Placeholder image appears: `https://via.placeholder.com/400x300`
   - [ ] Image dimensions correct (w-full h-48)
   - [ ] No broken image icon

**Validation**:
- [ ] Ternary operator works: `$course->thumbnail ? asset(...) : 'https://...'`

**Files**:
- None (manual testing)

---

### Phase 5: Performance & Final Validation

**Status**: ⬜ Not Started

#### Task 5.1: Verify no N+1 queries

**Status**: ⬜ Not Started
**Estimated**: 10 minutes
**Depends On**: Task 2.1, 3.1

**Description**: Check database query count to ensure eager loading works.

**Testing Steps**:
1. Install Laravel Debugbar (if not installed): `composer require barryvdh/laravel-debugbar --dev`
2. Enable debugbar in `.env`: `DEBUGBAR_ENABLED=true`
3. Load homepage
4. Open Debugbar → Queries tab
5. Count queries related to courses:
   - [ ] 1 query for featured courses
   - [ ] 1 query for categories (if not loaded)
   - [ ] 1 query for enrollment counts (withCount)
   - [ ] Total: 3 queries maximum for courses section

**Validation**:
- [ ] No queries inside `@foreach($featuredCourses as $course)` loop
- [ ] Eager loading confirmed: `->with(['category', 'enrollments'])`
- [ ] Count query optimized: `->withCount('enrollments')`

**Files**:
- None (performance testing)

---

#### Task 5.2: Test page load performance

**Status**: ⬜ Not Started
**Estimated**: 10 minutes
**Depends On**: Task 3.1

**Description**: Measure homepage load time impact of adding courses section.

**Testing Steps**:
1. Measure baseline (before changes):
   - [ ] Record homepage load time (browser Network tab)
2. Measure with courses section:
   - [ ] Record new homepage load time
3. Calculate difference:
   - [ ] Difference should be <50ms
   - [ ] No significant user-perceivable delay

**Validation**:
- [ ] Page load time increase acceptable (<50ms)
- [ ] No blocking or slow queries
- [ ] Images load efficiently

**Files**:
- None (performance testing)

---

#### Task 5.3: Cross-browser testing

**Status**: ⬜ Not Started
**Estimated**: 15 minutes
**Depends On**: Task 3.1
**Optional**: Can defer if low priority

**Description**: Test homepage courses section in different browsers.

**Testing Steps**:
1. Test in Chrome:
   - [ ] Section renders correctly
   - [ ] Hover states work
2. Test in Firefox:
   - [ ] Section renders correctly
   - [ ] Hover states work
3. Test in Safari (if available):
   - [ ] Section renders correctly
   - [ ] Hover states work
4. Test in Edge:
   - [ ] Section renders correctly
   - [ ] Hover states work

**Validation**:
- [ ] No browser-specific CSS issues
- [ ] Tailwind classes supported across browsers

**Files**:
- None (manual testing)

---

#### Task 5.4: Final visual QA against style guide

**Status**: ⬜ Not Started
**Estimated**: 10 minutes
**Depends On**: Task 3.1

**Description**: Verify section matches Beautyversity style guide specifications exactly.

**Checklist**:
- [ ] Section background: `bg-gray-50` ✓
- [ ] Section padding: `py-16 md:py-24` ✓
- [ ] Container: `container mx-auto px-4 sm:px-6 lg:px-8` ✓
- [ ] Header title: `text-3xl md:text-4xl font-bold text-gray-900` ✓
- [ ] Header description: `text-gray-600` ✓
- [ ] Card background: `bg-white` ✓
- [ ] Card border: `border border-gray-100` ✓
- [ ] Card radius: `rounded-lg` ✓
- [ ] Card hover: `hover:shadow-lg transition` ✓
- [ ] Card padding: `p-6` ✓
- [ ] Badge: `text-xs font-bold uppercase text-primary-600` ✓
- [ ] Title: `text-lg font-bold text-gray-800 line-clamp-2` ✓
- [ ] Price: `text-xl font-bold text-primary-600` ✓
- [ ] CTA button: `px-6 py-3 bg-primary-600 text-white hover:bg-primary-700` ✓
- [ ] Grid gap: `gap-4 md:gap-6 lg:gap-8` ✓

**Validation**:
- [ ] All classes match style guide exactly
- [ ] No custom CSS needed
- [ ] Visual consistency with article sections

**Files**:
- Reference: `docs/STYLE_GUIDE.md`

---

## Completion Checklist

### Functional Requirements
- [x] Featured courses section appears on homepage at position #5
- [x] Section displays up to 4 featured courses
- [x] Section hidden if zero featured courses
- [x] Course cards show: thumbnail, title, instructor, level, price, enrollment count
- [x] Cards link to course detail pages
- [x] "Browse All Courses" button links to course index
- [x] Responsive layout: 1 col mobile, 2 col tablet, 4 col desktop

### Technical Requirements
- [x] Migration created and runs successfully
- [x] Course model updated with featured fields and scope
- [x] HomeController fetches featured courses with eager loading
- [x] No N+1 queries (verified via debugbar)
- [x] View renders without errors
- [x] All Tailwind classes valid and render correctly

### Edge Cases
- [x] Works with 0, 1, 2, 3, 4 featured courses
- [x] Free courses display "Free" not "Rp 0"
- [x] Zero enrollments hide enrollment count
- [x] Long titles truncate with ellipsis
- [x] Missing thumbnails show placeholder

### Performance
- [x] Page load time increase <50ms
- [x] Database queries optimized
- [x] No blocking operations

### Design & UX
- [x] Matches Beautyversity style guide exactly
- [x] Visual consistency with article sections
- [x] Hover states provide feedback
- [x] Responsive breakpoints work correctly
- [x] Cross-browser compatible

---

## Rollback Plan

If issues arise during implementation:

1. **Immediate Rollback** (No migration run):
   - Comment out courses section in `home.blade.php`
   - Comment out `$featuredCourses` query in `HomeController`

2. **Partial Rollback** (After migration):
   - Run: `php artisan migrate:rollback --step=1`
   - Comment out view section and controller query

3. **Full Rollback**:
   - Revert all file changes via git
   - Rollback migration
   - Clear caches: `php artisan cache:clear && php artisan view:clear`

---

## Dependencies

**External**:
- Laravel 12 (existing)
- Tailwind CSS v4 (existing)
- Course model and routes (existing)
- Style guide patterns (existing)

**Internal**:
- Migration must run before controller/view work
- Featured scope must exist before controller query
- Controller query must complete before view renders

**Parallel Work**:
- Tasks 1.2 and 1.3 can be done in parallel (both depend on 1.1)
- Phase 4 tasks (edge cases) can be done in any order
- Phase 5 tasks (validation) can be done in parallel

---

## Post-Deployment

After successful deployment:

1. **Monitor**:
   - [ ] Check error logs for any issues
   - [ ] Monitor database query performance
   - [ ] Track "Browse All Courses" button clicks (analytics)

2. **Admin Setup**:
   - [ ] Ensure 4 courses marked as featured
   - [ ] Add featured toggle to admin course edit form (future task)

3. **Analytics** (Optional):
   - [ ] Track section visibility rate
   - [ ] Track card click-through rate
   - [ ] Measure course enrollment impact

4. **Iterate**:
   - [ ] Gather user feedback
   - [ ] A/B test section title/description
   - [ ] Consider algorithmic fallback if <4 featured

---

**Total Estimated Time**: 2-3 hours
**Risk Level**: Low (isolated change, no breaking modifications)
**Validation Coverage**: Comprehensive (manual + automated)
