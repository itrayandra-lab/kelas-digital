# hero-slider-management Specification

## Purpose

Enable content managers to manually curate and order articles displayed in the homepage hero slider, while maintaining automatic fallback when manual curation is inactive. This capability provides editorial control over the most prominent content placement on the platform.

## ADDED Requirements

### Requirement: Hero Slider Manual Ordering

The system SHALL allow content managers to manually select and order up to 5 articles for the homepage hero slider.

#### Scenario: Add article to hero slider with order

- **WHEN** admin creates or edits an article
- **THEN** admin can check "Include in Hero Slider" checkbox
- **AND** admin specifies order position (1-5) via number input
- **AND** system sets `hero_slider_order = N` where N is the specified order

#### Scenario: Remove article from hero slider

- **WHEN** admin unchecks "Include in Hero Slider" checkbox
- **THEN** system sets `hero_slider_order = null`
- **AND** article no longer appears in hero slider queries

#### Scenario: Prevent duplicate order positions

- **WHEN** admin attempts to set `hero_slider_order` to a value already used by another article
- **THEN** validation fails with error message: "This slider position is already taken. Please choose a different order (1-5)."
- **AND** form redisplays with error
- **AND** database unique constraint prevents save if validation bypassed

#### Scenario: Order position range validation

- **WHEN** admin enters `hero_slider_order` value
- **THEN** system validates value is integer between 1 and 5 (inclusive)
- **AND** rejects values outside this range with validation error

### Requirement: Hero Slider Query with Fallback

The system SHALL query hero slider articles with automatic fallback to latest published articles when manual curation provides fewer than 5 articles.

#### Scenario: Query manual hero slider articles

- **WHEN** system queries for homepage hero slider
- **THEN** system fetches published articles where `hero_slider_order IS NOT NULL`
- **AND** orders by `hero_slider_order` ascending
- **AND** limits to 5 articles
- **AND** eager loads categories and tags relationships

#### Scenario: Fallback when manual articles insufficient

- **WHEN** manual hero slider articles count < 5
- **THEN** system calculates needed articles = 5 - manual count
- **AND** fetches published articles where `hero_slider_order IS NULL`
- **AND** excludes IDs already in manual set
- **AND** orders by `published_at` descending
- **AND** limits to needed count
- **AND** merges fallback articles after manual articles

#### Scenario: All 5 slots manually filled

- **WHEN** 5 articles have non-null `hero_slider_order` values (1-5)
- **THEN** fallback query does not execute
- **AND** hero slider shows exactly the 5 manually curated articles in specified order

#### Scenario: No manual curation (all null)

- **WHEN** all articles have `hero_slider_order = null`
- **THEN** fallback query executes for 5 articles
- **AND** behavior identical to original automatic system (5 latest published)

### Requirement: Automatic Cleanup on Status Change

The system SHALL automatically remove articles from hero slider when they become unpublished.

#### Scenario: Unpublish article in hero slider

- **WHEN** article with non-null `hero_slider_order` status changes to `draft` or `scheduled`
- **THEN** system automatically sets `hero_slider_order = null`
- **AND** article removed from hero slider on next homepage load
- **AND** no admin intervention required

#### Scenario: Republish previously removed article

- **WHEN** article status changes back to `published`
- **THEN** `hero_slider_order` remains null (not auto-restored)
- **AND** admin must manually re-add to slider if desired

#### Scenario: Delete article in hero slider

- **WHEN** article with non-null `hero_slider_order` is deleted
- **THEN** database cascade removes article
- **AND** unique constraint on `hero_slider_order` frees up that position
- **AND** other articles' orders remain unchanged

### Requirement: Article Model Scope for Hero Slider

The Article model SHALL provide a query scope for retrieving hero slider articles.

#### Scenario: InHeroSlider scope

- **WHEN** developer calls `Article::inHeroSlider()`
- **THEN** scope applies `whereNotNull('hero_slider_order')` filter
- **AND** orders by `hero_slider_order` ascending
- **AND** returns query builder for further chaining

#### Scenario: Chain inHeroSlider with published scope

- **WHEN** developer calls `Article::published()->inHeroSlider()`
- **THEN** both scopes apply (published status AND non-null order)
- **AND** results sorted by `hero_slider_order` ascending

### Requirement: Hero Slider Admin Management Page

The system SHALL provide a dedicated admin page for managing hero slider content.

#### Scenario: View hero slider management page

- **WHEN** content-manager or admin visits `/admin/hero-slider`
- **THEN** page displays current hero slider articles (0-5)
- **AND** shows order position badge (1-5) for each article
- **AND** displays article thumbnail, title, category, and published date
- **AND** provides "Remove" button for each article

#### Scenario: Display article count indicator

- **WHEN** viewing hero slider management page
- **THEN** page header shows "Current Hero Slider Articles (N/5)" where N = count
- **AND** if N = 0, shows message: "No articles manually added. Slider will show 5 latest published articles."

#### Scenario: Remove article via management page

- **WHEN** admin clicks "Remove" button for a hero slider article
- **THEN** system sets that article's `hero_slider_order = null`
- **AND** redirects back to management page
- **AND** shows success message: "Article removed from hero slider"

#### Scenario: Unauthorized access to hero slider page

- **WHEN** user without `manage articles` permission visits `/admin/hero-slider`
- **THEN** system returns 403 Forbidden error
- **AND** redirects to unauthorized page or login

### Requirement: Stale Content Warning

The system SHALL warn content managers when hero slider content has not been updated recently.

#### Scenario: Display stale content warning

- **WHEN** viewing hero slider management page
- **AND** last updated article in slider was modified > 30 days ago
- **THEN** page displays yellow warning box with message: "⚠️ Stale content warning: Hero slider last updated {N} days ago. Consider refreshing with new content."

#### Scenario: No warning for recent updates

- **WHEN** last updated article in slider was modified ≤ 30 days ago
- **THEN** no stale content warning displayed

#### Scenario: Calculate last updated date

- **WHEN** system calculates last update date
- **THEN** uses `MAX(updated_at)` from articles where `hero_slider_order IS NOT NULL`
- **AND** if no manual articles, last updated = null (no warning)

### Requirement: Permission Control

Hero slider management SHALL respect existing role-based access control.

#### Scenario: Content-manager role access

- **WHEN** user with `content-manager` role (has `manage articles` permission)
- **THEN** user can view and modify hero slider settings
- **AND** user can add/remove articles via article edit form
- **AND** user can access `/admin/hero-slider` management page

#### Scenario: Admin and super-admin role access

- **WHEN** user with `admin` or `super-admin` role
- **THEN** user has full access to hero slider management
- **AND** inherits `manage articles` permission

#### Scenario: Student role denied access

- **WHEN** user with `student` role attempts hero slider operations
- **THEN** all admin actions return 403 Forbidden
- **AND** hero slider management page inaccessible

### Requirement: Database Schema

The articles table SHALL include hero slider ordering column.

#### Scenario: Hero slider order column structure

- **WHEN** migration runs
- **THEN** articles table gains `hero_slider_order` column
- **AND** column type is `integer`, nullable
- **AND** unique constraint prevents duplicate order values
- **AND** index added for query performance
- **AND** positioned after `recommended_at` column

#### Scenario: Default value for existing articles

- **WHEN** migration runs on existing database
- **THEN** all existing articles have `hero_slider_order = null`
- **AND** homepage behavior unchanged (automatic mode)

## MODIFIED Requirements

### Requirement: Homepage Hero Slider Content Source

The homepage hero slider SHALL display articles based on manual curation with automatic fallback.

**Previous behavior:**
- Hero slider showed 5 most recent published articles unconditionally
- Query: `Article::published()->orderBy('published_at', 'desc')->limit(5)`

**New behavior:**
- Hero slider prioritizes manually curated articles (`hero_slider_order NOT NULL`)
- Falls back to latest published articles when manual count < 5
- Maintains 5 total articles in slider

#### Scenario: Hybrid query execution (replaces "Query latest 5 articles")

- **WHEN** HomeController fetches hero slider articles
- **THEN** first query fetches manual articles (non-null `hero_slider_order`, ordered by order ascending)
- **AND** if count < 5, second query fetches fallback articles (null `hero_slider_order`, ordered by `published_at` descending)
- **AND** final collection merges manual + fallback articles
- **AND** total count = 5 (or fewer if insufficient published articles exist)

## Related Capabilities

- **article-curation**: Hero slider management extends curation capabilities alongside recommendation and featured systems
- **homepage-sections**: Hero slider is the first section of the homepage architecture
