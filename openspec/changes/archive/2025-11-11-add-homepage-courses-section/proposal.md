# Proposal: Add Homepage Courses Section

**Change ID**: `add-homepage-courses-section`
**Status**: Implemented
**Created**: 2025-01-11
**Author**: AI Assistant (Claude)

## Problem Statement

The redesigned homepage currently focuses exclusively on articles, with 7 distinct sections showcasing various article collections (hero slider, latest, popular, recommendations, trending, featured category, and more articles). However, Beautyversity is a hybrid platform combining both content management (articles) and learning management (courses).

**Current Gap:**
- Courses, a core revenue-generating feature, have zero visibility on the homepage
- Users must navigate through menus to discover course offerings
- The homepage fails to communicate the platform's dual nature (articles + structured education)
- Missed opportunity to convert engaged article readers into course enrollees

**User Impact:**
- Casual visitors don't know courses exist
- No natural funnel from free content (articles) to paid education (courses)
- Platform appears to be blog-only, not an education platform

## Proposed Solution

Add a **"Featured Courses"** section to the homepage, positioned strategically after the Recommendation section and before Trending (position #5 in the content flow).

**Why This Position:**
1. **Content-First Funnel**: Users read articles first (hero, latest, popular, recommendations), then discover courses naturally
2. **Mid-Page Visibility**: Appears after initial scroll but before fatigue sets in
3. **Balanced Prominence**: Not too aggressive (top) or hidden (bottom)
4. **Pyramid Strategy**: Free content at top → curated content → premium courses → more content

**Design Principles:**
- Maintain visual consistency with existing article cards
- Use same Tailwind patterns from style guide (bg-white, rounded-lg, border, hover states)
- Show metadata relevant to courses: instructor, level, price, enrollment count
- 4-column grid on desktop (matching Recommendations and Terpopuler layout)
- Responsive: 1 column mobile, 2 tablet, 4 desktop

**Content Strategy:**
- Display 4 featured courses (manual curation via `is_featured` flag)
- Fallback to newest courses if <4 featured
- Show "Browse All Courses" CTA button
- Each card links to course detail page (`/courses/{slug}`)

## Scope

This change introduces one new capability:

1. **Course Homepage Showcase** - Display featured courses in a dedicated homepage section

### Related Specs

This change will **ADD** a new spec:
- `openspec/specs/course-homepage-showcase/spec.md` - New capability

This change will **MODIFY** existing spec:
- `openspec/specs/homepage-sections/spec.md` - Update section count and ordering

## Success Criteria

### Functional
- [x] Featured courses section appears on homepage at position #5
- [x] Section displays 4 courses with proper metadata (title, instructor, level, price, thumbnail)
- [x] Cards use consistent styling matching article cards
- [x] Responsive layout works across all breakpoints
- [x] "Browse All Courses" button links to course index
- [x] Course cards link to individual course detail pages

### Technical
- [x] No N+1 queries (use eager loading with `->with('category', 'enrollments')`)
- [x] Featured courses scope added to Course model
- [x] HomeController updated with courses query
- [x] View rendering tested on mobile, tablet, desktop
- [x] No breaking changes to existing sections

### Design
- [x] Follows Beautyversity style guide (STYLE_GUIDE.md)
- [x] Uses dusty rose primary colors (primary-600, primary-700)
- [x] Matches spacing (p-6, gap-8, py-16 md:py-24)
- [x] Card shadows and transitions consistent with article cards
- [x] Typography hierarchy maintained (text-lg for titles, text-sm for metadata)

### User Experience
- [x] Section visually distinct from article sections (badge shows "COURSE" instead of category)
- [x] Clear value proposition (price visible, level shown)
- [x] Hover states provide feedback
- [x] CTA button prominent and clear

## Implementation Strategy

### Phase 1: Database & Model (Low Risk)
- Add `is_featured` boolean column to courses table
- Add `featured()` scope to Course model
- Seed 2-4 courses as featured for testing

### Phase 2: Controller & Data (Medium Risk)
- Update HomeController to fetch 4 featured courses
- Eager load relationships (category, enrollments for count)
- Pass data to view

### Phase 3: View & Styling (Medium Risk)
- Add courses section to home.blade.php after recommendations
- Create card component matching style guide patterns
- Test responsive layout

### Phase 4: Validation (Low Risk)
- Manual testing across breakpoints
- Verify no N+1 queries
- Check performance impact

**Estimated Effort:** 2-3 hours
**Risk Level:** Low (isolated change, no breaking modifications)

## Design Decisions

See `design.md` for detailed architectural rationale.

## Open Questions

1. **Featured Selection**: Should we use `is_featured` flag (manual curation) or algorithmic selection (newest, popular)?
   - **Decision**: Manual curation via `is_featured` flag (consistent with article recommendations)

2. **Enrollment Display**: Show actual enrollment count or generic "X+ students enrolled"?
   - **Decision**: Show actual count if >0, otherwise hide to avoid signaling low enrollment

3. **Price Display**: Show exact price or "Starting from RP X"?
   - **Decision**: Show exact price for transparency

4. **Course Limit**: Always show 4, or flexible based on available featured courses?
   - **Decision**: Target 4, but show 2-4 based on availability (better than empty section)

## References

- Style Guide: `docs/STYLE_GUIDE.md`
- Current Homepage: `resources/views/home.blade.php`
- Course Model: `app/Models/Course.php`
- Home Controller: `app/Http/Controllers/HomeController.php`
- Related Spec: `openspec/specs/homepage-sections/spec.md`
