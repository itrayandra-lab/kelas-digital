# Performance Optimization Plan
## Article Discovery Features - Bottleneck Analysis & Solutions

**Date Created:** 2025-11-11
**Current Scale:** 78 articles, 367 tags, 535 article-tag relations
**Status:** Monitoring phase - optimizations planned for future scaling

---

## Executive Summary

The `enhance-article-discovery` implementation is currently performing well at current scale (78 articles). However, two potential bottlenecks have been identified that will require optimization as the platform scales:

1. **Related Articles Query** - GROUP BY aggregation with JOIN on pivot table
2. **Search Filter Query** - Multiple EXISTS subqueries with LIKE wildcards

This document provides a phased optimization roadmap based on article count milestones.

---

## Bottleneck Analysis

### 1. Related Articles Query Bottleneck

**Location:** `app/Models/Article.php:370-418`

**Current Implementation:**
```php
// Tag-based matching with aggregation
$relatedByTags = Article::published()
    ->where('articles.id', '!=', $this->id)
    ->with('categories', 'tags')
    ->selectRaw('articles.*, COUNT(DISTINCT article_tag.tag_id) as shared_tags_count')
    ->join('article_tag', 'articles.id', '=', 'article_tag.article_id')
    ->whereIn('article_tag.tag_id', $tagIds)
    ->groupBy('articles.id')
    ->orderByRaw('shared_tags_count DESC, articles.views_count DESC')
    ->limit($limit)
    ->get();
```

**Generated SQL:**
```sql
SELECT articles.*, COUNT(DISTINCT article_tag.tag_id) as shared_tags_count
FROM articles
JOIN article_tag ON articles.id = article_tag.article_id
WHERE articles.id != 123
  AND article_tag.tag_id IN (1, 2, 3, 4, 5)
  AND articles.status = 'published'
  AND (articles.scheduled_at IS NULL OR articles.scheduled_at <= NOW())
GROUP BY articles.id
ORDER BY shared_tags_count DESC, articles.views_count DESC
LIMIT 4
```

**Performance Issues:**

1. **GROUP BY on all articles columns** - MySQL must group all columns in `articles.*`
2. **COUNT(DISTINCT)** - Aggregate operation requiring full scan of each group
3. **JOIN + WHERE IN** - Must join with pivot table for each article with matching tags
4. **Sorting after aggregation** - Must calculate all shared_tags_count before sorting

**Estimated Performance:**

| Articles | Avg Tags/Article | Estimated Query Time | Status |
|----------|------------------|---------------------|--------|
| 100 | 7 | <50ms | ✅ Good |
| 500 | 7 | 100-200ms | ⚠️ Acceptable |
| 1,000 | 8 | 300-500ms | ⚠️ Slow |
| 2,000 | 8 | 500-1000ms | ❌ Problem |
| 5,000 | 10 | 1-3s | ❌ Critical |
| 10,000+ | 10 | 3-10s | ❌ Unusable |

---

### 2. Search Filter Query Bottleneck

**Location:** `app/Http/Controllers/SearchController.php:40-75`

**Current Implementation:**
```php
$articleQuery = Article::published()
    ->with('categories', 'tags')
    ->where(function ($query) use ($keyword) {
        $query->where('title', 'like', "%{$keyword}%")
            ->orWhere('excerpt', 'like', "%{$keyword}%")
            ->orWhere('content', 'like', "%{$keyword}%")
            ->orWhere('author', 'like', "%{$keyword}%");
    });

// Category filter
if ($categoryId) {
    $articleQuery->whereHas('categories', function ($query) use ($categoryId) {
        $query->where('article_categories.id', $categoryId);
    });
}

// Tag filter
if (!empty($tagIds)) {
    $articleQuery->whereHas('tags', function ($query) use ($tagIds) {
        $query->whereIn('tags.id', $tagIds);
    });
}
```

**Generated SQL:**
```sql
SELECT * FROM articles
WHERE (
  title LIKE '%skincare%' OR excerpt LIKE '%skincare%'
  OR content LIKE '%skincare%' OR author LIKE '%skincare%'
)
AND status = 'published'
AND (scheduled_at IS NULL OR scheduled_at <= NOW())
AND published_at >= '2025-10-01'
AND published_at <= '2025-11-30'
AND EXISTS (
  SELECT * FROM article_article_category
  JOIN article_categories ON article_article_category.article_category_id = article_categories.id
  WHERE article_article_category.article_id = articles.id
    AND article_categories.id = 3
)
AND EXISTS (
  SELECT * FROM article_tag
  JOIN tags ON article_tag.tag_id = tags.id
  WHERE article_tag.article_id = articles.id
    AND tags.id IN (1, 2, 5)
)
ORDER BY published_at DESC
LIMIT 12 OFFSET 0
```

**Performance Issues:**

1. **LIKE '%keyword%'** - Full table scan (cannot use index due to leading wildcard)
2. **Multiple OR conditions** - Cannot optimize with standard indexes
3. **Multiple EXISTS subqueries** - Each executed per row in result set
4. **Nested JOINs in EXISTS** - Each subquery joins pivot + entity table

**Estimated Performance:**

| Articles | With Filters | Estimated Query Time | Status |
|----------|--------------|---------------------|--------|
| 100 | None | <50ms | ✅ Good |
| 500 | Category only | 100-200ms | ⚠️ Acceptable |
| 1,000 | Category + Tags | 500ms-1s | ⚠️ Slow |
| 2,000 | All filters | 1-2s | ❌ Problem |
| 5,000 | All filters | 3-5s | ❌ Critical |
| 10,000+ | All filters | 10s+ | ❌ Unusable |

---

## Optimization Roadmap

### Phase 1: Immediate (0-500 articles)
**Current Status:** Safe zone
**Timeline:** When articles reach ~300
**Effort:** 1-2 hours
**Priority:** Low

#### Actions:

**1. Add Composite Indexes**

Create migration: `add_performance_indexes_to_articles_system`

```php
// database/migrations/YYYY_MM_DD_add_performance_indexes.php
public function up()
{
    Schema::table('article_tag', function (Blueprint $table) {
        $table->index(['article_id', 'tag_id'], 'idx_article_tag_composite');
        $table->index(['tag_id', 'article_id'], 'idx_tag_article_reverse');
    });

    Schema::table('articles', function (Blueprint $table) {
        $table->index(['status', 'scheduled_at', 'views_count'], 'idx_status_scheduled_views');
        $table->index(['status', 'published_at'], 'idx_status_published');
    });

    Schema::table('article_article_category', function (Blueprint $table) {
        $table->index(['article_id', 'article_category_id'], 'idx_article_category_composite');
    });
}
```

**Expected Impact:**
- Related articles query: 20-30% faster
- Search query: 15-25% faster
- All queries benefit from better indexes

**Risk:** Very low (indexes only improve performance)

---

### Phase 2: Short-term (500-1,000 articles)
**Timeline:** When articles reach ~500
**Effort:** 4-8 hours
**Priority:** Medium

#### Solution A: Redis Caching for Related Articles

**Installation:**
```bash
composer require predis/predis
```

**Implementation:**
```php
// app/Models/Article.php
public function getRelatedArticles($limit = 4)
{
    $cacheKey = "article_related_{$this->id}_{$limit}";
    $cacheTTL = now()->addHours(24);

    return Cache::remember($cacheKey, $cacheTTL, function() use ($limit) {
        // Existing query logic here
        $tagIds = $this->tags()->pluck('tags.id')->toArray();
        $categoryIds = $this->categories()->pluck('article_categories.id')->toArray();

        // ... rest of existing implementation
    });
}

// Add cache invalidation
protected static function booted()
{
    static::saved(function ($article) {
        // Clear cache for this article
        Cache::forget("article_related_{$article->id}_4");

        // Clear cache for related articles (articles with shared tags/categories)
        $relatedIds = $article->tags()->with('articles')->get()
            ->pluck('articles')->flatten()->pluck('id')->unique();

        foreach ($relatedIds as $id) {
            Cache::forget("article_related_{$id}_4");
        }
    });
}
```

**Expected Impact:**
- First request: Same as before
- Cached requests: 50-100x faster (<10ms)
- 95% of requests will be cached

**Pros:**
- ✅ Massive performance improvement
- ✅ Easy to implement
- ✅ Easy to rollback
- ✅ No schema changes

**Cons:**
- ❌ Requires Redis server
- ❌ Cache invalidation complexity
- ❌ Cold cache still slow

---

#### Solution B: MySQL FULLTEXT Index for Search

**Migration:**
```php
// database/migrations/YYYY_MM_DD_add_fulltext_index_to_articles.php
public function up()
{
    DB::statement('ALTER TABLE articles ADD FULLTEXT INDEX articles_fulltext(title, excerpt, content)');
}

public function down()
{
    Schema::table('articles', function (Blueprint $table) {
        $table->dropIndex('articles_fulltext');
    });
}
```

**Update SearchController:**
```php
// app/Http/Controllers/SearchController.php
if ($keyword !== '') {
    // Replace LIKE queries with FULLTEXT search
    $articleQuery = Article::published()
        ->with('categories', 'tags')
        ->whereRaw('MATCH(title, excerpt, content) AGAINST(? IN BOOLEAN MODE)', [$keyword]);

    // Apply filters (same as before)
    if ($categoryId) {
        $articleQuery->whereHas('categories', function ($query) use ($categoryId) {
            $query->where('article_categories.id', $categoryId);
        });
    }

    // ... rest remains same
}
```

**Expected Impact:**
- Keyword search: 10-50x faster
- Overall search: 5-10x faster
- Still has EXISTS bottleneck but much improved

**Pros:**
- ✅ Built into MySQL (no extra service)
- ✅ Relevance scoring
- ✅ Supports boolean operators (+, -, *)
- ✅ Easy to implement

**Cons:**
- ❌ Minimum word length (default 4 chars)
- ❌ Doesn't solve EXISTS bottleneck
- ❌ Limited compared to dedicated search engines

---

### Phase 3: Medium-term (1,000-2,000 articles)
**Timeline:** When articles reach ~1,000
**Effort:** 1-2 days
**Priority:** High (if bottleneck confirmed)

#### Solution: Pre-computed Recommendations Table

**Migration:**
```php
// database/migrations/YYYY_MM_DD_create_article_recommendations_table.php
public function up()
{
    Schema::create('article_recommendations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('article_id')->constrained()->onDelete('cascade');
        $table->foreignId('related_article_id')->constrained('articles')->onDelete('cascade');
        $table->float('similarity_score');
        $table->enum('match_type', ['tag', 'category', 'hybrid']);
        $table->integer('shared_tags_count')->default(0);
        $table->timestamps();

        $table->index(['article_id', 'similarity_score']);
        $table->unique(['article_id', 'related_article_id']);
    });
}
```

**Implementation:**
```php
// app/Models/Article.php

// Keep old method as protected for computing
protected function computeRelatedArticlesQuery($limit = 10)
{
    // Existing getRelatedArticles logic, but get top 10
    // ... (same as current implementation)
}

// New public method uses pre-computed table
public function getRelatedArticles($limit = 4)
{
    return Article::select('articles.*')
        ->join('article_recommendations', 'articles.id', '=', 'article_recommendations.related_article_id')
        ->where('article_recommendations.article_id', $this->id)
        ->with('categories', 'tags')
        ->orderByDesc('article_recommendations.similarity_score')
        ->limit($limit)
        ->get();
}

// Compute recommendations when article saved
public function updateRelatedArticles()
{
    $related = $this->computeRelatedArticlesQuery(10);

    // Clear existing
    DB::table('article_recommendations')
        ->where('article_id', $this->id)
        ->delete();

    // Insert new
    $score = 10.0;
    foreach ($related as $article) {
        DB::table('article_recommendations')->insert([
            'article_id' => $this->id,
            'related_article_id' => $article->id,
            'similarity_score' => $score,
            'match_type' => 'hybrid',
            'shared_tags_count' => $article->shared_tags_count ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $score -= 0.5;
    }
}

// Hook into model events
protected static function booted()
{
    static::saved(function ($article) {
        // Dispatch job to avoid blocking
        \App\Jobs\UpdateArticleRecommendations::dispatch($article);
    });
}
```

**Create Job:**
```php
// app/Jobs/UpdateArticleRecommendations.php
<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateArticleRecommendations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Article $article
    ) {}

    public function handle(): void
    {
        $this->article->updateRelatedArticles();
    }
}
```

**Scheduled Command (nightly re-compute):**
```php
// app/Console/Commands/RecomputeArticleRecommendations.php
<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class RecomputeArticleRecommendations extends Command
{
    protected $signature = 'articles:recompute-recommendations';
    protected $description = 'Recompute all article recommendations';

    public function handle()
    {
        $articles = Article::published()->get();
        $bar = $this->output->createProgressBar($articles->count());

        foreach ($articles as $article) {
            $article->updateRelatedArticles();
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nRecomputed recommendations for {$articles->count()} articles");
    }
}
```

**Add to scheduler:**
```php
// routes/console.php
Schedule::command('articles:recompute-recommendations')
    ->dailyAt('03:00')
    ->withoutOverlapping();
```

**Expected Impact:**
- Query time: 500-1000ms → 10-20ms (50-100x faster)
- Predictable performance regardless of article count
- Can track analytics (why articles are related)

**Pros:**
- ✅ Super fast queries (simple JOIN + ORDER)
- ✅ Scalable to 100K+ articles
- ✅ Can improve algorithm without changing query
- ✅ Analytics-friendly

**Cons:**
- ❌ Extra table + storage (~100 bytes × articles × 10)
- ❌ Eventual consistency (recommendations may be stale)
- ❌ More complex deployment
- ❌ Need queue worker

---

### Phase 4: Long-term (2,000+ articles)
**Timeline:** When articles reach ~2,000
**Effort:** 2-3 days
**Priority:** Critical (if search becomes bottleneck)

#### Solution: Meilisearch Integration

**Installation:**
```bash
# Docker Compose
services:
  meilisearch:
    image: getmeili/meilisearch:latest
    ports:
      - "7700:7700"
    environment:
      MEILI_MASTER_KEY: ${MEILI_MASTER_KEY}
    volumes:
      - meilisearch_data:/meili_data

# Laravel
composer require laravel/scout meilisearch/meilisearch-php
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

**Environment:**
```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://localhost:7700
MEILISEARCH_KEY=your-master-key
```

**Update Article Model:**
```php
// app/Models/Article.php
use Laravel\Scout\Searchable;

class Article extends Model
{
    use Searchable;

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => strip_tags($this->content ?? ''),
            'author' => $this->author,
            'status' => $this->status,
            'published_at' => $this->published_at?->timestamp,
            'views_count' => $this->views_count,

            // Filterable fields
            'category_ids' => $this->categories->pluck('id')->toArray(),
            'category_names' => $this->categories->pluck('name')->toArray(),
            'tag_ids' => $this->tags->pluck('id')->toArray(),
            'tag_names' => $this->tags->pluck('name')->toArray(),
        ];
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable()
    {
        return $this->status === 'published';
    }
}
```

**Configure Meilisearch:**
```php
// app/Providers/AppServiceProvider.php
use Laravel\Scout\Scout;

public function boot()
{
    Scout::makeSearchableUsing(function ($models) {
        // Configure Meilisearch settings
        $models->first()->searchableUsing()->updateSettings([
            'filterableAttributes' => [
                'status',
                'category_ids',
                'tag_ids',
                'published_at',
            ],
            'sortableAttributes' => [
                'published_at',
                'views_count',
            ],
            'rankingRules' => [
                'words',
                'typo',
                'proximity',
                'attribute',
                'sort',
                'exactness',
            ],
        ]);
    });
}
```

**Update SearchController:**
```php
// app/Http/Controllers/SearchController.php
public function index(Request $request)
{
    $keyword = trim((string) $request->input('q', ''));
    $categoryId = $request->input('category_id');
    $tagIds = $request->input('tag_id', []);
    $dateFrom = $request->input('date_from');
    $dateTo = $request->input('date_to');

    if ($keyword !== '') {
        // Build Meilisearch query
        $query = Article::search($keyword);

        // Add filters
        $filters = [];

        if ($categoryId) {
            $filters[] = "category_ids = {$categoryId}";
        }

        if (!empty($tagIds)) {
            $tagIds = is_array($tagIds) ? $tagIds : [$tagIds];
            $tagFilters = collect($tagIds)->map(fn($id) => "tag_ids = {$id}")->join(' OR ');
            $filters[] = "({$tagFilters})";
        }

        if ($dateFrom) {
            $filters[] = "published_at >= " . strtotime($dateFrom);
        }

        if ($dateTo) {
            $filters[] = "published_at <= " . strtotime($dateTo);
        }

        if (!empty($filters)) {
            $query->whereRaw(implode(' AND ', $filters));
        }

        // Paginate
        $articles = $query->paginate(12);

        // For AJAX, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'articles' => $articles->items(),
                'has_more_pages' => $articles->hasMorePages(),
                'current_page' => $articles->currentPage(),
                'total' => $articles->total(),
            ]);
        }
    } else {
        $articles = collect();
    }

    // ... rest of the method (filter options, etc.)
}
```

**Initial Indexing:**
```bash
php artisan scout:import "App\Models\Article"
```

**Expected Impact:**
- Search speed: 1-5s → 10-50ms (100-500x faster)
- Scales to millions of articles
- Typo tolerance, relevance scoring
- Faceted search (category counts)
- Real-time updates

**Pros:**
- ✅ Extremely fast (<50ms for complex queries)
- ✅ Typo tolerance and relevance scoring
- ✅ Scalable to millions of documents
- ✅ Faceted search (show counts per category/tag)
- ✅ Highlight matching text
- ✅ Great developer experience

**Cons:**
- ❌ Extra service to maintain
- ❌ Additional storage (search index)
- ❌ Sync complexity (DB → Index)
- ❌ Learning curve
- ❌ Infrastructure cost

---

## Alternative: Denormalization (Aggressive Optimization)

**Only if desperate** - This violates normalization but can work for read-heavy workloads.

**Migration:**
```php
Schema::table('articles', function (Blueprint $table) {
    $table->json('category_ids')->nullable();
    $table->json('tag_ids')->nullable();
    $table->fullText(['title', 'excerpt', 'content'], 'articles_fulltext');
});
```

**Sync on save:**
```php
protected static function booted()
{
    static::saved(function ($article) {
        $article->withoutEvents(function () use ($article) {
            $article->update([
                'category_ids' => $article->categories->pluck('id'),
                'tag_ids' => $article->tags->pluck('id'),
            ]);
        });
    });
}
```

**Search query becomes:**
```php
$articleQuery = Article::published()
    ->whereRaw('MATCH(title, excerpt, content) AGAINST(? IN BOOLEAN MODE)', [$keyword])
    ->when($categoryId, fn($q) => $q->whereJsonContains('category_ids', $categoryId))
    ->when($tagIds, fn($q) => $q->where(function($query) use ($tagIds) {
        foreach ($tagIds as $tagId) {
            $query->orWhereJsonContains('tag_ids', $tagId);
        }
    }));
```

**Pros:**
- ✅ No EXISTS overhead
- ✅ Single table scan
- ✅ Much faster than multiple whereHas

**Cons:**
- ❌ Data duplication
- ❌ Sync complexity (must update 2 places)
- ❌ JSON search slower than integer index
- ❌ Not standard Laravel pattern
- ❌ Risk of data inconsistency

**Use case:** Only if Meilisearch not an option and >5000 articles

---

## Monitoring & Triggers

### Set up Performance Monitoring

**Add Query Logging:**
```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    if (config('app.env') === 'production') {
        DB::listen(function ($query) {
            // Log slow queries (>1s)
            if ($query->time > 1000) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                ]);
            }
        });
    }
}
```

**Add Metrics Dashboard:**
```php
// Monitor these metrics weekly:
// - Average article detail page load time
// - Average search page load time
// - Database query time percentiles (p50, p95, p99)
// - Cache hit ratio (if using Redis)
```

### Optimization Triggers

| Metric | Trigger | Action Required |
|--------|---------|-----------------|
| Article count reaches 300 | - | Phase 1: Add indexes |
| Article detail load >500ms (p95) | 500+ articles | Phase 2A: Add Redis cache |
| Search page load >1s (p95) | 500+ articles | Phase 2B: Add FULLTEXT |
| Article detail load >1s (p95) | 1000+ articles | Phase 3: Pre-compute table |
| Search page load >2s (p95) | 2000+ articles | Phase 4: Meilisearch |

---

## Cost Estimation

### Phase 1 (Indexes)
- **Development:** 1-2 hours
- **Infrastructure:** $0 (uses existing MySQL)
- **Total:** ~$50-100 (dev time)

### Phase 2 (Redis + FULLTEXT)
- **Development:** 4-8 hours
- **Infrastructure:** $10-20/month (Redis Cloud or ElastiCache)
- **Total:** ~$200-400 (dev) + $10-20/month

### Phase 3 (Pre-compute Table)
- **Development:** 1-2 days
- **Storage:** ~10MB extra for 1000 articles
- **Infrastructure:** $0 (uses existing DB + queue)
- **Total:** ~$800-1600 (dev)

### Phase 4 (Meilisearch)
- **Development:** 2-3 days
- **Infrastructure:** $20-50/month (cloud hosting)
- **Storage:** ~2x article data size
- **Total:** ~$1600-2400 (dev) + $20-50/month

---

## Testing Strategy

### Performance Benchmarks

Create benchmarks before each optimization:

```php
// tests/Performance/ArticlePerformanceTest.php
<?php

namespace Tests\Performance;

use App\Models\Article;
use Tests\TestCase;

class ArticlePerformanceTest extends TestCase
{
    /** @test */
    public function benchmark_related_articles_query()
    {
        $article = Article::published()->first();

        $start = microtime(true);
        $related = $article->getRelatedArticles();
        $duration = (microtime(true) - $start) * 1000; // ms

        $this->assertLessThan(500, $duration,
            "Related articles query took {$duration}ms (threshold: 500ms)");

        echo "\nRelated articles query: {$duration}ms\n";
    }

    /** @test */
    public function benchmark_search_with_filters()
    {
        $start = microtime(true);

        $results = Article::published()
            ->where(function ($q) {
                $q->where('title', 'like', '%beauty%')
                  ->orWhere('content', 'like', '%beauty%');
            })
            ->whereHas('categories', fn($q) => $q->where('id', 1))
            ->whereHas('tags', fn($q) => $q->whereIn('id', [1,2,3]))
            ->paginate(12);

        $duration = (microtime(true) - $start) * 1000;

        $this->assertLessThan(1000, $duration,
            "Search query took {$duration}ms (threshold: 1000ms)");

        echo "\nSearch with filters: {$duration}ms\n";
    }
}
```

**Run before/after optimization:**
```bash
php artisan test --filter=Performance
```

---

## Rollback Plans

### Phase 2A (Redis Cache)
```php
// To disable caching, change .env:
CACHE_DRIVER=array

// Or wrap in config check:
if (config('cache.related_articles_enabled')) {
    return Cache::remember(...);
}
return $this->computeRelatedArticlesQuery($limit);
```

### Phase 2B (FULLTEXT)
```php
// Keep old LIKE query as fallback:
if (config('search.use_fulltext')) {
    $query->whereRaw('MATCH(...) AGAINST(...)');
} else {
    $query->where(function($q) use ($keyword) {
        $q->where('title', 'like', "%{$keyword}%")...
    });
}
```

### Phase 3 (Pre-compute)
```php
// Add config flag to use old method:
public function getRelatedArticles($limit = 4)
{
    if (config('articles.use_precomputed_recommendations')) {
        return $this->getPrecomputedRelatedArticles($limit);
    }

    // Fall back to old algorithm
    return $this->computeRelatedArticlesQuery($limit);
}
```

### Phase 4 (Meilisearch)
```php
// Scout supports database fallback:
SCOUT_DRIVER=database  // Falls back to normal queries
```

---

## Decision Matrix

Use this to decide which optimization to implement:

| Consideration | Phase 1 (Indexes) | Phase 2 (Cache + FT) | Phase 3 (Pre-compute) | Phase 4 (Meilisearch) |
|---------------|-------------------|----------------------|----------------------|----------------------|
| **Articles Count** | 300+ | 500+ | 1,000+ | 2,000+ |
| **Development Time** | 1-2 hours | 4-8 hours | 1-2 days | 2-3 days |
| **Infrastructure Cost** | $0 | $10-20/mo | $0 | $20-50/mo |
| **Complexity** | Very Low | Low | Medium | High |
| **Performance Gain** | 20-30% | 5-10x | 50-100x | 100-500x |
| **Maintenance** | None | Low | Medium | Medium |
| **Scalability** | Up to 1K | Up to 2K | Up to 50K | Millions |
| **Rollback Difficulty** | Very Easy | Easy | Medium | Medium |

**Recommendation Path:**
1. Start with Phase 1 at 300 articles ✅
2. Add Phase 2 at 500 articles ✅
3. Evaluate at 1000 articles: if still slow, go to Phase 3 or 4
4. Choose Phase 3 if budget-conscious, Phase 4 for best UX

---

## Conclusion

Current implementation is **solid for current scale** (78 articles). This plan provides a clear roadmap for scaling:

- **Immediate:** No action needed (safe zone)
- **At 300 articles:** Add indexes (Phase 1)
- **At 500 articles:** Add caching + FULLTEXT (Phase 2)
- **At 1000+ articles:** Evaluate Phase 3 or 4 based on metrics

Monitor query performance weekly and trigger optimizations based on actual metrics, not just article count.

**Next Review:** When articles reach 250 (prepare Phase 1)

---

**Document Version:** 1.0
**Last Updated:** 2025-11-11
**Author:** Claude Code Review
**Status:** Living document - update as new bottlenecks discovered
