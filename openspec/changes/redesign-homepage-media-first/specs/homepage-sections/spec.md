# Homepage Sections Specification

## ADDED Requirements

### Requirement: Hero Slider Section

The homepage SHALL display a hero slider section using Splide.js that showcases featured articles with visual prominence and interactive navigation.

#### Scenario: Hero slider displays featured articles

- **WHEN** user visits homepage
- **THEN** hero slider displays up to 5 latest published articles
- **AND** each slide shows article thumbnail, title, excerpt, author, date, and views count
- **AND** slider has left/right arrow navigation controls
- **AND** slider has dot indicators for slide position

#### Scenario: Hero slider autoplay

- **WHEN** user visits homepage
- **THEN** slider automatically transitions to next slide every 5 seconds
- **AND** autoplay pauses when user hovers over slider
- **AND** autoplay resumes when hover ends

#### Scenario: Manual navigation

- **WHEN** user clicks left/right arrows
- **THEN** slider transitions to previous/next slide
- **AND** autoplay resets timer after manual navigation

### Requirement: Latest Article Section

The homepage SHALL display a "Latest Article" section with 6 most recent published articles in a two-column asymmetric layout.

#### Scenario: Latest articles two-column layout

- **WHEN** user views Latest Article section
- **THEN** left column displays 3 articles in horizontal card format (thumbnail left, content right)
- **AND** right column displays 3 articles in vertical/compact card format (thumbnail top, content bottom)
- **AND** all 6 articles are ordered by `published_at` descending

#### Scenario: Latest article card content

- **WHEN** article card is displayed
- **THEN** card shows thumbnail image, title, category badges (max 2), excerpt (120 chars limit), published date, and views count
- **AND** category badges are clickable links to category pages

### Requirement: Terpopuler Section

The homepage SHALL display a "Terpopuler" section with 4 most-viewed articles of all time on a dark background.

#### Scenario: Terpopuler displays top articles

- **WHEN** user views Terpopuler section
- **THEN** section displays 4 articles ordered by `views_count` descending
- **AND** section has dark gray background (bg-gray-800 or similar)
- **AND** each card shows thumbnail, title, and views count

#### Scenario: Terpopuler grid layout

- **WHEN** screen width is desktop
- **THEN** articles display in 4-column grid
- **WHEN** screen width is tablet
- **THEN** articles display in 2-column grid
- **WHEN** screen width is mobile
- **THEN** articles display in single column

### Requirement: Recommendation Section

The homepage SHALL display a "Recommendation for You" section with 4 manually curated articles and a link to view all recommendations.

#### Scenario: Recommended articles display

- **WHEN** user views Recommendation section
- **THEN** section displays 4 articles where `is_recommended = true`
- **AND** articles are ordered by `recommended_at` descending
- **AND** section has light background (white or bg-gray-50)

#### Scenario: View all recommendations link

- **WHEN** user clicks "Lihat Lainnya" button
- **THEN** user is redirected to `/recommendations` page
- **AND** page displays all recommended articles with pagination

### Requirement: Trending Section

The homepage SHALL display a "Trending" section with 6 articles that are popular in the last 30 days, using the same two-column layout as Latest Article section.

#### Scenario: Trending articles algorithm

- **WHEN** user views Trending section
- **THEN** section displays 6 articles where `published_at >= now()->subDays(30)`
- **AND** articles are ordered by `views_count` descending
- **AND** layout matches Latest Article section (3 horizontal left + 3 vertical right)

#### Scenario: Trending with insufficient articles

- **WHEN** fewer than 6 articles exist in the last 30 days
- **THEN** section displays all available articles within the time window
- **AND** does not backfill with older articles

### Requirement: Featured Category Section

The homepage SHALL display a featured category section highlighting 3 articles from a manually selected category with dynamic color theming.

#### Scenario: Featured category selection

- **WHEN** admin sets a category's `is_featured_section = true`
- **THEN** homepage displays that category's name as section title
- **AND** section displays 3 most-viewed articles from that category
- **AND** section background uses category's theme color

#### Scenario: Category theme colors

- **WHEN** category is SKINCARE
- **THEN** section uses pink-600 background
- **WHEN** category is MYTHBUSTER
- **THEN** section uses purple-600 background
- **WHEN** category is HAIRCARE
- **THEN** section uses blue-600 background
- **WHEN** category is DECORATIVE
- **THEN** section uses rose-600 background
- **WHEN** category is BAHANAKTIF
- **THEN** section uses green-600 background
- **WHEN** category is any other
- **THEN** section uses gray-800 background

#### Scenario: Featured category full-width cards

- **WHEN** user views Featured Category section
- **THEN** 3 articles display in full-width horizontal card format
- **AND** each card shows large thumbnail with text overlay (title + category)

### Requirement: More Articles Section

The homepage SHALL display a "More Articles" section with 3 additional articles that do not appear in any previous homepage section.

#### Scenario: Smart article exclusion

- **WHEN** More Articles section loads
- **THEN** system collects all article IDs from hero, latest, popular, trending, recommended, and featured category sections
- **AND** queries 3 latest published articles excluding collected IDs
- **AND** displays in horizontal card format

#### Scenario: See More button

- **WHEN** user clicks "See More" button
- **THEN** user is redirected to `/articles` page
- **AND** page displays all published articles with pagination

### Requirement: Section Order

The homepage sections SHALL be displayed in a specific order to optimize user engagement and content discovery.

#### Scenario: Homepage section sequence

- **WHEN** user visits homepage
- **THEN** sections appear in this order from top to bottom:
  1. Top Bar (location + social icons)
  2. Main Header (logo + search + navigation)
  3. Hero Slider
  4. Latest Article
  5. Terpopuler
  6. Recommendation
  7. Trending
  8. Featured Category
  9. More Articles
  10. Footer

### Requirement: Responsive Design

All homepage sections SHALL be responsive and adapt to mobile, tablet, and desktop screen sizes.

#### Scenario: Mobile layout

- **WHEN** screen width is mobile (<768px)
- **THEN** all multi-column sections collapse to single column
- **AND** horizontal cards stack vertically
- **AND** hero slider remains full-width

#### Scenario: Tablet layout

- **WHEN** screen width is tablet (768px-1024px)
- **THEN** 4-column grids become 2-column
- **AND** 3-column grids become 2-column
- **AND** two-column asymmetric layouts remain 2-column

#### Scenario: Desktop layout

- **WHEN** screen width is desktop (>1024px)
- **THEN** all sections display in full multi-column layouts as designed
