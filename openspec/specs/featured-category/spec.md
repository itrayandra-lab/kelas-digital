# featured-category Specification

## Purpose
TBD - created by archiving change redesign-homepage-media-first. Update Purpose after archive.
## Requirements
### Requirement: Featured Category Selection

The system SHALL allow admins to designate one article category as featured on the homepage with exclusive selection enforcement.

#### Scenario: Mark category as featured

- **WHEN** admin selects a category to feature
- **THEN** system sets category's `is_featured_section = true` and `featured_at = now()`
- **AND** system automatically sets `is_featured_section = false` for all other categories

#### Scenario: Only one featured category allowed

- **WHEN** admin attempts to feature a category
- **AND** another category is already featured
- **THEN** system unfeatured the previous category automatically
- **AND** sets new category as featured

#### Scenario: Unfeature category

- **WHEN** admin unfeatures a category
- **THEN** system sets `is_featured_section = false` and `featured_at = null`
- **AND** homepage Featured Category section is hidden

### Requirement: Featured Category Query

The system SHALL query articles from the featured category for homepage display with proper ordering.

#### Scenario: Fetch featured category articles

- **WHEN** system loads homepage Featured Category section
- **THEN** system finds category where `is_featured_section = true`
- **AND** fetches 3 published articles from that category
- **AND** orders articles by `views_count` descending

#### Scenario: No featured category

- **WHEN** no category has `is_featured_section = true`
- **THEN** Featured Category section is hidden on homepage
- **AND** other sections remain unaffected

#### Scenario: Featured category with insufficient articles

- **WHEN** featured category has fewer than 3 published articles
- **THEN** section displays all available articles from that category
- **AND** does not backfill with articles from other categories

### Requirement: Category Theme Colors

Each article category SHALL have a predefined theme color used for visual distinction in the Featured Category section.

#### Scenario: Theme color accessor

- **WHEN** developer accesses `$category->theme_color`
- **THEN** system returns Tailwind CSS class based on category slug:
  - SKINCARE → `bg-pink-600`
  - MYTHBUSTER → `bg-purple-600`
  - HAIRCARE → `bg-blue-600`
  - DECORATIVE → `bg-rose-600`
  - BAHANAKTIF → `bg-green-600`
  - MENZONE → `bg-indigo-600`
  - PERSONALCARE → `bg-teal-600`
  - BEAUTYLIFE → `bg-amber-600`
  - default → `bg-gray-800`

#### Scenario: Theme color in view

- **WHEN** Featured Category section renders
- **THEN** section container uses featured category's theme color as background
- **AND** text color adjusts to white for contrast

### Requirement: Admin UI for Featured Category

The admin panel SHALL provide radio button selection for featuring categories with clear visual indicators.

#### Scenario: Featured category radio in category list

- **WHEN** admin views article categories list
- **THEN** each category row shows a radio button labeled "Feature on Homepage"
- **AND** only one radio can be selected at a time
- **AND** currently featured category has radio pre-selected

#### Scenario: Featured category indicator

- **WHEN** admin views categories list
- **THEN** featured category row shows "FEATURED" badge
- **AND** badge displays in category's theme color

#### Scenario: Update featured category via radio

- **WHEN** admin selects different category radio button
- **THEN** system submits form and updates featured category
- **AND** page refreshes showing new featured category badge
- **AND** previous category's badge is removed

### Requirement: Featured Category Persistence

Featured category selection SHALL persist across deploys and cache clears.

#### Scenario: Featured category after cache clear

- **WHEN** admin clears application cache
- **THEN** featured category selection remains unchanged
- **AND** homepage loads correct featured category from database

#### Scenario: Featured category database columns

- **WHEN** article_categories table is queried
- **THEN** table has `is_featured_section` boolean column (default false)
- **AND** table has `featured_at` timestamp column (nullable)
- **AND** table has `theme_color` varchar column (nullable, for future custom colors)

### Requirement: Featured Category Section Layout

The Featured Category section SHALL display articles in full-width card format with overlay text and category color theming.

#### Scenario: Full-width card display

- **WHEN** Featured Category section renders
- **THEN** 3 articles display in stacked full-width cards
- **AND** each card shows large thumbnail image as background
- **AND** title and category badge overlay on image with dark gradient for readability

#### Scenario: Responsive layout

- **WHEN** screen width is mobile
- **THEN** cards remain full-width but stack vertically with reduced height
- **WHEN** screen width is desktop
- **THEN** cards display with larger height (h-64 or similar)

#### Scenario: Category name in section header

- **WHEN** Featured Category section renders
- **THEN** section title displays featured category name (e.g., "MYTHBUSTER", "SKINCARE")
- **AND** title uses featured category's theme color for accent

