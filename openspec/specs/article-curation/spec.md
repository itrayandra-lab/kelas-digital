# article-curation Specification

## Purpose
TBD - created by archiving change redesign-homepage-media-first. Update Purpose after archive.
## Requirements
### Requirement: Article Recommendation System

The system SHALL allow content managers to manually curate articles for the homepage Recommendation section using a recommendation flag.

#### Scenario: Mark article as recommended

- **WHEN** admin creates or edits an article
- **THEN** admin can check "Recommend this article" checkbox
- **AND** system sets `is_recommended = true` and `recommended_at = now()`

#### Scenario: Unmark article as recommended

- **WHEN** admin unchecks "Recommend this article" checkbox
- **THEN** system sets `is_recommended = false` and `recommended_at = null`

#### Scenario: Recommended articles query

- **WHEN** system queries recommended articles for homepage
- **THEN** system fetches articles where `is_recommended = true`
- **AND** orders by `recommended_at` descending
- **AND** limits to 4 for homepage preview
- **AND** takes all for `/recommendations` dedicated page

#### Scenario: Recommendation page pagination

- **WHEN** user visits `/recommendations` page
- **THEN** page displays all recommended articles with pagination (12 per page)
- **AND** articles maintain `recommended_at` descending order

### Requirement: Article View Tracking

The system SHALL track article view counts to power popularity-based sections (Terpopuler and Trending).

#### Scenario: Increment views on article visit

- **WHEN** user visits an article detail page
- **THEN** system increments article's `views_count` by 1
- **AND** update does not modify `updated_at` timestamp

#### Scenario: Query popular articles

- **WHEN** system queries for Terpopuler section
- **THEN** system fetches published articles ordered by `views_count` descending
- **AND** limits to 4 articles

#### Scenario: Query trending articles

- **WHEN** system queries for Trending section
- **THEN** system fetches published articles where `published_at >= now()->subDays(30)`
- **AND** orders by `views_count` descending
- **AND** limits to 6 articles

#### Scenario: Default views count for new articles

- **WHEN** new article is created
- **THEN** `views_count` defaults to 0

### Requirement: Article Scopes

The Article model SHALL provide query scopes for filtering articles by popularity and timeframe.

#### Scenario: Trending scope

- **WHEN** developer calls `Article::trending()`
- **THEN** scope applies `published_at >= now()->subDays(30)` filter
- **AND** orders by `views_count` descending

#### Scenario: Popular scope

- **WHEN** developer calls `Article::popular()`
- **THEN** scope orders by `views_count` descending
- **AND** does not apply any date filters

#### Scenario: Recommended scope

- **WHEN** developer calls `Article::recommended()`
- **THEN** scope filters where `is_recommended = true`
- **AND** orders by `recommended_at` descending

### Requirement: Admin UI for Curation

The admin panel SHALL provide intuitive controls for content curation with clear indicators of recommendation status.

#### Scenario: Recommendation checkbox in article form

- **WHEN** admin views article create/edit form
- **THEN** form displays "Recommend this article" checkbox
- **AND** checkbox is checked if `is_recommended = true`
- **AND** checkbox label explains it will appear in Recommendation section

#### Scenario: Recommendation indicator in article list

- **WHEN** admin views articles list
- **THEN** recommended articles show a "LIVE" badge if in top 4 by `recommended_at`
- **AND** show "Queued" badge if recommended but not in top 4
- **AND** non-recommended articles show no badge

#### Scenario: Recommendation counter

- **WHEN** admin views articles list with recommended filter
- **THEN** page header shows "X articles currently showing, Y queued"
- **WHERE** X = count of top 4 recommended, Y = count of remaining recommended

### Requirement: Validation Rules

The system SHALL validate recommendation data to ensure data integrity.

#### Scenario: Recommended timestamp validation

- **WHEN** article `is_recommended` changes from false to true
- **THEN** system sets `recommended_at` to current timestamp
- **WHEN** article `is_recommended` changes from true to false
- **THEN** system sets `recommended_at` to null

#### Scenario: Published articles only in sections

- **WHEN** system queries articles for any homepage section
- **THEN** query applies `status = 'published'` filter
- **AND** excludes draft and scheduled articles

