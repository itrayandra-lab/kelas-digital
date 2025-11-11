# course-homepage-showcase Specification

## Purpose
TBD - created by archiving change add-homepage-courses-section. Update Purpose after archive.
## Requirements
### Requirement: Featured courses section on homepage

The homepage MUST display a dedicated section showcasing featured courses between the Recommendations section and Trending section (position #5 in content flow).

**Purpose**: Provide visibility for course offerings, convert article readers into course enrollees, and establish platform as hybrid content + education destination.

**Acceptance Criteria**:
- Section appears after "Recommendation for You" and before "Trending"
- Section uses light gray background (`bg-gray-50`) consistent with other sections
- Section includes header with title, description, and "Browse All Courses" CTA button
- Section displays up to 4 featured courses in responsive grid layout
- Section is hidden if zero featured courses available (graceful degradation)

#### Scenario: User views homepage with featured courses

**Given** the database contains 4 courses marked as featured (`is_featured = true`)

**When** a user visits the homepage (`/`)

**Then** the page displays a "Featured Courses" section between Recommendations and Trending

**And** the section shows:
- Section title: "Featured Courses" (text-3xl md:text-4xl font-bold text-gray-900)
- Section description: "Structured learning programs by expert instructors" (text-gray-600)
- "Browse All Courses" button (desktop only, links to `/courses`)
- 4 course cards in responsive grid (1 col mobile, 2 col tablet, 4 col desktop)

**And** the section uses styling:
- Background: `bg-gray-50`
- Padding: `py-16 md:py-24`
- Container: `container mx-auto px-4 sm:px-6 lg:px-8`
- Grid gap: `gap-4 md:gap-6 lg:gap-8`

#### Scenario: Homepage with fewer than 4 featured courses

**Given** the database contains only 2 courses marked as featured

**When** a user visits the homepage

**Then** the "Featured Courses" section displays exactly 2 course cards

**And** the grid layout still renders correctly (no broken layout)

**And** the section remains visible (not hidden)

#### Scenario: Homepage with zero featured courses

**Given** the database contains zero courses marked as featured

**When** a user visits the homepage

**Then** the "Featured Courses" section does not appear on the page

**And** no empty state or placeholder is shown

**And** the Trending section appears immediately after Recommendations

---

### Requirement: Featured course card display

Each featured course MUST be displayed as a card with consistent styling matching article cards but with course-specific metadata.

**Purpose**: Provide clear visual differentiation while maintaining design consistency, communicate course value proposition (instructor, level, price).

**Acceptance Criteria**:
- Card structure matches article cards (white background, rounded corners, border, hover shadow)
- Card displays course-specific metadata (not article metadata)
- Price is prominently displayed
- Enrollment count shown conditionally
- Card is clickable and links to course detail page
- Follows Beautyversity style guide specifications

#### Scenario: Displaying a course card with enrollments

**Given** a featured course exists with:
- title: "Advanced Skincare Chemistry"
- instructor: "Dr. Sarah Johnson"
- level: "Advanced"
- price: 299000
- thumbnail: "skincare-course.jpg"
- enrollments_count: 15
- slug: "advanced-skincare-chemistry"

**When** the course card renders on the homepage

**Then** the card displays:
- Thumbnail image (w-full h-48 object-cover)
- Badge: "COURSE" (text-xs font-bold uppercase text-primary-600)
- Title: "Advanced Skincare Chemistry" (text-lg font-bold text-gray-800, line-clamp-2)
- Instructor: "Dr. Sarah Johnson" (text-sm text-gray-600)
- Level: "Advanced" (text-sm text-gray-600, separated by bullet)
- Price: "Rp 299.000" (text-xl font-bold text-primary-600)
- Enrollment count: "15 enrolled" (text-xs text-gray-500)

**And** the card uses styling:
- Container: `bg-white rounded-lg overflow-hidden border border-gray-100`
- Hover effect: `hover:shadow-lg transition`
- Padding: `p-6`

**And** clicking the card navigates to `/courses/advanced-skincare-chemistry`

#### Scenario: Displaying a course card with zero enrollments

**Given** a featured course exists with:
- title: "Beginner Makeup Basics"
- enrollments_count: 0
- (other fields similar to previous scenario)

**When** the course card renders

**Then** the enrollment count text does not appear

**And** the card layout remains consistent (no broken spacing)

**And** all other card elements display normally

#### Scenario: Displaying a free course

**Given** a featured course exists with:
- title: "Introduction to Beauty Science"
- price: 0

**When** the course card renders

**Then** the price displays as "Free" (not "Rp 0")

**And** uses the same styling: `text-xl font-bold text-primary-600`

#### Scenario: Course card hover interaction

**Given** a featured course card is displayed

**When** a user hovers over the card

**Then** the card shadow increases from default to `shadow-lg`

**And** the transition is smooth (150ms default Tailwind transition)

**And** the title color changes to `text-primary-600` (hover state)

---

### Requirement: Featured courses data fetching

The HomeController MUST fetch featured courses efficiently with proper eager loading to prevent N+1 query issues.

**Purpose**: Ensure performant data retrieval, avoid database query overhead, maintain consistent homepage load times.

**Acceptance Criteria**:
- Featured courses fetched using `featured()` scope on Course model
- Relationships eager loaded (category, enrollments)
- Enrollment count calculated via `withCount()` for efficiency
- Query limited to 4 courses maximum
- Results ordered by `featured_at` descending (most recently featured first)

#### Scenario: Fetching featured courses for homepage

**Given** the `Course` model has a `featured()` scope defined

**And** 6 courses exist in database, 4 marked as featured

**When** the `HomeController::index()` method executes

**Then** the controller queries:
```php
$featuredCourses = Course::featured()
    ->with(['category', 'enrollments'])
    ->withCount('enrollments')
    ->limit(4)
    ->get();
```

**And** exactly 4 courses are returned

**And** the query executes with proper eager loading (no N+1 queries)

**And** the result set is passed to view as `$featuredCourses`

#### Scenario: Verifying no N+1 queries

**Given** 4 featured courses exist with relationships (category, enrollments)

**When** the homepage renders featured courses section

**Then** the total database query count for courses section is:
- 1 query for featured courses
- 1 query for eager-loaded categories (if not already loaded)
- 1 query for enrollment counts (via withCount)
- Total: 3 queries maximum

**And** no additional queries execute when rendering individual course cards

---

### Requirement: Course model featured scope

The Course model MUST provide a `featured()` scope to filter courses marked for homepage display.

**Purpose**: Encapsulate featured course query logic, provide reusable filtering method, maintain clean controller code.

**Acceptance Criteria**:
- Scope filters courses where `is_featured = true`
- Scope orders by `featured_at` descending
- Scope returns query builder instance (chainable)
- Scope name follows Laravel conventions (scopeFeatured)

#### Scenario: Using featured scope

**Given** the `Course` model has the following scope defined:
```php
public function scopeFeatured($query)
{
    return $query->where('is_featured', true)
                 ->orderBy('featured_at', 'desc');
}
```

**When** calling `Course::featured()->get()`

**Then** the query filters for `is_featured = true`

**And** results are ordered by `featured_at` descending

**And** the scope can be chained with other query methods:
```php
Course::featured()->limit(4)->get()
```

#### Scenario: Featured scope with no featured courses

**Given** all courses in database have `is_featured = false`

**When** calling `Course::featured()->get()`

**Then** an empty collection is returned

**And** no exception is thrown

---

### Requirement: Course featured fields in database

The `courses` table MUST include fields to support manual curation of featured courses.

**Purpose**: Enable admin control over homepage course selection, track when courses were featured, provide indexing for query performance.

**Acceptance Criteria**:
- `is_featured` boolean column with default false
- `featured_at` timestamp column (nullable)
- Index on `is_featured` column for query optimization
- Fields added after `level` column for schema organization
- Migration reversible (down method drops fields and index)

#### Scenario: Adding featured fields via migration

**Given** a migration file exists: `2025_11_11_000001_add_featured_fields_to_courses_table.php`

**When** running `php artisan migrate`

**Then** the `courses` table schema includes:
- Column: `is_featured` (boolean, default false, after `level`)
- Column: `featured_at` (timestamp, nullable, after `is_featured`)
- Index: `courses_is_featured_index` on `is_featured` column

**And** existing course records have `is_featured = false`

**And** the migration can be rolled back without data loss:
```php
php artisan migrate:rollback --step=1
```

#### Scenario: Setting a course as featured

**Given** a course exists with `is_featured = false`

**When** an admin updates the course:
```php
$course->update([
    'is_featured' => true,
    'featured_at' => now()
]);
```

**Then** the course appears in `Course::featured()` query results

**And** the `featured_at` timestamp reflects the feature time

#### Scenario: Unfeaturing a course

**Given** a course exists with `is_featured = true`

**When** an admin updates the course:
```php
$course->update(['is_featured' => false]);
```

**Then** the course no longer appears in `Course::featured()` results

**And** the `featured_at` timestamp remains unchanged (historical data)

---

### Requirement: Responsive course grid layout

The featured courses grid MUST adapt to different screen sizes following mobile-first responsive design principles.

**Purpose**: Ensure optimal viewing experience across devices, maintain readability on mobile, leverage desktop screen real estate.

**Acceptance Criteria**:
- Mobile (0-767px): 1 column, gap-4
- Tablet (768-1023px): 2 columns, gap-6
- Desktop (1024px+): 4 columns, gap-8
- Grid uses Tailwind responsive classes
- Layout does not break with 1-4 courses displayed

#### Scenario: Viewing courses section on mobile device

**Given** a user accesses homepage on a mobile device (375px width)

**When** the "Featured Courses" section renders

**Then** courses display in a single column (`grid-cols-1`)

**And** cards have 16px gap between them (`gap-4`)

**And** cards fill the full container width minus padding

**And** all 4 courses are visible (stacked vertically)

#### Scenario: Viewing courses section on tablet device

**Given** a user accesses homepage on a tablet device (768px width)

**When** the "Featured Courses" section renders

**Then** courses display in 2 columns (`md:grid-cols-2`)

**And** cards have 24px gap between them (`md:gap-6`)

**And** if 4 courses exist, they arrange in 2 rows of 2 courses each

#### Scenario: Viewing courses section on desktop

**Given** a user accesses homepage on a desktop device (1440px width)

**When** the "Featured Courses" section renders

**Then** courses display in 4 columns (`lg:grid-cols-4`)

**And** cards have 32px gap between them (`lg:gap-8`)

**And** all 4 courses are visible in a single row

**And** cards have balanced width distribution

#### Scenario: Responsive "Browse All" button

**Given** the "Featured Courses" section header includes a CTA button

**When** viewed on mobile devices (< 768px)

**Then** the "Browse All Courses" button is hidden (`hidden md:inline-block`)

**And** a mobile button appears below the grid in the section footer

**When** viewed on tablet/desktop (>= 768px)

**Then** the button appears in the section header (top-right position)

**And** uses styling: `px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition`

---

### Requirement: Course card clickability

Each course card MUST be fully clickable and navigate to the course detail page.

**Purpose**: Provide intuitive user interaction, follow standard card pattern, enable course discovery.

**Acceptance Criteria**:
- Entire card is clickable (not just title)
- Card links to `/courses/{slug}` route
- Link uses standard anchor tag (SEO-friendly, crawlable)
- Hover states provide visual feedback
- Link opens in same window (standard navigation)

#### Scenario: Clicking a course card

**Given** a course card is displayed for course with slug "skincare-101"

**When** a user clicks anywhere on the card

**Then** the browser navigates to `/courses/skincare-101`

**And** the page loads the course detail view

**And** navigation occurs in the same browser window/tab

#### Scenario: Course card link structure

**Given** a course card renders in the DOM

**Then** the card HTML structure is:
```html
<a href="/courses/{slug}" class="...card-classes...">
  <img src="..." alt="...">
  <div class="p-6">
    <!-- card content -->
  </div>
</a>
```

**And** the anchor tag wraps the entire card (not nested inside)

**And** the link has no `target="_blank"` attribute (same-window navigation)

---

