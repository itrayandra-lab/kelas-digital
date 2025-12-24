# Article View Count Enhancement

**Date:** 2025-12-24
**Status:** Approved

## Overview

Meningkatkan akurasi view count dengan spam protection dan menampilkan view count di UI untuk memberikan social proof kepada pembaca.

## Goals

1. **Akurasi** - Mencegah spam/inflasi view count dengan IP-based deduplication
2. **Visibility** - Menampilkan view count di UI (detail page, cards, admin panel)

## Design Decisions

| Aspek | Keputusan |
|-------|-----------|
| Spam Protection | IP-based dengan 30 menit cooldown |
| Storage | Laravel Cache (default driver) |
| Display Locations | Detail page, cards, admin panel |
| Format | Hybrid (exact <1k, abbreviated ≥1k) |
| Visual | Icon eye + angka |
| Admin Format | Exact number untuk monitoring |

## Architecture

### Current State

```
User visit → incrementViews() → views_count++
(No protection, setiap visit = +1)
```

### New State

```
User visit → Check cache key → Key exists?
                                   │
                         ┌────────┴────────┐
                         ↓                 ↓
                      NO: increment     YES: skip
                      + set cache key
                      (TTL 30 min)
```

## Implementation Details

### 1. Spam Protection (Article Model)

```php
// app/Models/Article.php

public function incrementViewsWithProtection(string $ipAddress): bool
{
    $cacheKey = "article_view:{$this->id}:" . md5($ipAddress);

    // Sudah view dalam 30 menit terakhir? Skip.
    if (Cache::has($cacheKey)) {
        return false;
    }

    // Increment view count
    $this->incrementViews();

    // Set cooldown 30 menit
    Cache::put($cacheKey, true, now()->addMinutes(30));

    return true;
}
```

### 2. View Count Formatting

```php
// Helper function atau accessor di model

function formatViewCount(int $count): string
{
    if ($count < 1000) {
        return (string) $count;
    }

    if ($count < 1000000) {
        $formatted = $count / 1000;
        return rtrim(rtrim(number_format($formatted, 1), '0'), '.') . 'k';
    }

    $formatted = $count / 1000000;
    return rtrim(rtrim(number_format($formatted, 1), '0'), '.') . 'M';
}
```

**Format Examples:**

| Input | Output |
|-------|--------|
| 0 | "0" |
| 823 | "823" |
| 1000 | "1k" |
| 1234 | "1.2k" |
| 10500 | "10.5k" |
| 56700 | "56.7k" |
| 1234567 | "1.2M" |

### 3. Model Accessor

```php
// app/Models/Article.php

public function getFormattedViewsAttribute(): string
{
    return formatViewCount($this->views_count);
}
```

### 4. Controller Update

```php
// app/Http/Controllers/HomeController.php - showArticle()

// BEFORE:
$article->incrementViews();

// AFTER:
$article->incrementViewsWithProtection($request->ip());
```

### 5. UI Components

**Article Detail Page:**
```html
<div class="flex items-center gap-4 text-gray-500 text-sm">
    <span>{{ $article->author }}</span>
    <span>•</span>
    <span>{{ $article->published_at->format('d M Y') }}</span>
    <span>•</span>
    <span class="flex items-center gap-1">
        <svg class="w-4 h-4"><!-- eye icon --></svg>
        {{ $article->formatted_views }}
    </span>
</div>
```

**Article Card:**
```html
<div class="flex items-center gap-1 text-gray-400 text-xs">
    <svg class="w-3.5 h-3.5"><!-- eye icon --></svg>
    <span>{{ $article->formatted_views }}</span>
</div>
```

**Admin Panel (exact numbers):**
```html
<th>Views</th>
...
<td class="text-center">{{ number_format($article->views_count) }}</td>
```

## Files to Modify

| File | Changes |
|------|---------|
| `app/Models/Article.php` | + `incrementViewsWithProtection()`, + `getFormattedViewsAttribute()` |
| `app/Http/Controllers/HomeController.php` | Update to use new method |
| `resources/views/articles/show.blade.php` | + view count in meta |
| `resources/views/components/article-card.blade.php` | + view count display |
| `resources/views/admin/articles/index.blade.php` | + views column |

## Not Required

- No new migration (column `views_count` already exists)
- No new database table (using cache, not database)
- No additional packages
