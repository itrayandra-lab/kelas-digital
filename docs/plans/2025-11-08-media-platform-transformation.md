# Media Platform Transformation Implementation Plan

**Goal:** Transform Beautyversity from basic LMS+CMS into a traffic-focused Educational Media Platform with advanced search, social sharing, comments, and optimized conversion funnels.

**Architecture:** Media-first homepage with article prominence, hybrid mega-menu navigation, contextual course CTAs within articles, Laravel Scout for advanced search, and social sharing infrastructure. Built on existing Laravel 12 MVC with Tailwind/Alpine frontend.

**Tech Stack:** Laravel 12, Laravel Scout (Meilisearch driver), Tailwind CSS v4, Alpine.js, existing packages (Spatie Permission, Trix, Laravel SEO)

**Strategy:**
- Priority: Traffic → Authority → Monetization → Community
- Content Pyramid: 60% light content, 30% educational, 10% course teasers
- Funnel: Casual readers → Educated consumers → Course buyers
- Mobile: Responsive-first (70-80% mobile traffic expected)

---

## Phase 1: Foundation & Database Schema

### Task 1: Create Comments System Migration

**Files:**
- Create: `database/migrations/2025_11_08_000001_create_article_comments_table.php`
- Test: `tests/Unit/Models/ArticleCommentTest.php`

**Step 1: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_has_many_comments()
    {
        $article = Article::factory()->create();
        $user = User::factory()->create();

        $comment = ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Great article!',
        ]);

        $this->assertCount(1, $article->comments);
        $this->assertEquals('Great article!', $article->comments->first()->content);
    }

    public function test_comment_belongs_to_user()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $article = Article::factory()->create();

        $comment = ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Test comment',
        ]);

        $this->assertEquals('John Doe', $comment->user->name);
    }

    public function test_comment_can_have_replies()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $parentComment = ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Parent comment',
        ]);

        $replyComment = ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Reply comment',
            'parent_id' => $parentComment->id,
        ]);

        $this->assertCount(1, $parentComment->replies);
        $this->assertEquals('Reply comment', $parentComment->replies->first()->content);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Unit/Models/ArticleCommentTest.php`

Expected: FAIL with "Class 'App\Models\ArticleComment' not found"

**Step 3: Create migration file**

Create migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('article_comments')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index(['article_id', 'created_at']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_comments');
    }
};
```

**Step 4: Create ArticleComment model**

Create: `app/Models/ArticleComment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'content',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ArticleComment::class, 'parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'asc');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
```

**Step 5: Update Article model with comments relationship**

Modify: `app/Models/Article.php` (add after existing relationships)

```php
// Add this import at top
use Illuminate\Database\Eloquent\Relations\HasMany;

// Add this method after existing relationship methods
public function comments(): HasMany
{
    return $this->hasMany(ArticleComment::class)
        ->whereNull('parent_id')
        ->where('is_approved', true)
        ->orderBy('created_at', 'desc')
        ->with('replies');
}

public function allComments(): HasMany
{
    return $this->hasMany(ArticleComment::class);
}
```

**Step 6: Run migration**

Run: `php artisan migrate`

Expected: Migration successful

**Step 7: Run test to verify it passes**

Run: `php artisan test tests/Unit/Models/ArticleCommentTest.php`

Expected: PASS (all 3 tests)

**Step 8: Commit**

```bash
git add database/migrations/2025_11_08_000001_create_article_comments_table.php
git add app/Models/ArticleComment.php
git add app/Models/Article.php
git add tests/Unit/Models/ArticleCommentTest.php
git commit -m "feat: add article comments system with nested replies"
```

---

### Task 2: Create Article View Tracking Migration

**Files:**
- Create: `database/migrations/2025_11_08_000002_add_views_count_to_articles_table.php`
- Modify: `app/Models/Article.php`
- Test: `tests/Feature/ArticleViewTrackingTest.php`

**Step 1: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleViewTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_view_count_increments()
    {
        $article = Article::factory()->create(['views_count' => 0]);

        $this->assertEquals(0, $article->views_count);

        $article->recordView();

        $this->assertEquals(1, $article->fresh()->views_count);
    }

    public function test_multiple_views_increment_correctly()
    {
        $article = Article::factory()->create(['views_count' => 5]);

        $article->recordView();
        $article->recordView();

        $this->assertEquals(7, $article->fresh()->views_count);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/ArticleViewTrackingTest.php`

Expected: FAIL with "Unknown column 'views_count'"

**Step 3: Create migration**

Create migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('views_count')->default(0)->after('published_at');
            $table->index('views_count');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('views_count');
        });
    }
};
```

**Step 4: Update Article model**

Modify: `app/Models/Article.php`

Add to $fillable array:
```php
'views_count',
```

Add method after existing methods:
```php
public function recordView(): void
{
    $this->increment('views_count');
}

public function scopePopular($query, $limit = 10)
{
    return $query->orderBy('views_count', 'desc')->limit($limit);
}
```

**Step 5: Run migration**

Run: `php artisan migrate`

Expected: Migration successful

**Step 6: Run test to verify it passes**

Run: `php artisan test tests/Feature/ArticleViewTrackingTest.php`

Expected: PASS (all 2 tests)

**Step 7: Commit**

```bash
git add database/migrations/2025_11_08_000002_add_views_count_to_articles_table.php
git add app/Models/Article.php
git add tests/Feature/ArticleViewTrackingTest.php
git commit -m "feat: add article view tracking"
```

---

### Task 3: Create Course Related Fields Migration

**Files:**
- Create: `database/migrations/2025_11_08_000003_add_media_fields_to_articles_and_courses.php`
- Test: `tests/Unit/Models/ArticleMediaTest.php`

**Step 1: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_can_have_featured_flag()
    {
        $article = Article::factory()->create(['is_featured' => true]);

        $this->assertTrue($article->is_featured);
    }

    public function test_article_can_have_content_type()
    {
        $article = Article::factory()->create(['content_type' => 'educational']);

        $this->assertEquals('educational', $article->content_type);
    }

    public function test_scope_featured_returns_only_featured_articles()
    {
        Article::factory()->create(['is_featured' => true]);
        Article::factory()->create(['is_featured' => false]);

        $featuredArticles = Article::featured()->get();

        $this->assertCount(1, $featuredArticles);
        $this->assertTrue($featuredArticles->first()->is_featured);
    }

    public function test_scope_by_content_type_filters_correctly()
    {
        Article::factory()->create(['content_type' => 'light']);
        Article::factory()->create(['content_type' => 'educational']);
        Article::factory()->create(['content_type' => 'light']);

        $lightArticles = Article::byContentType('light')->get();

        $this->assertCount(2, $lightArticles);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Unit/Models/ArticleMediaTest.php`

Expected: FAIL with "Unknown column 'is_featured'"

**Step 3: Create migration**

Create migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status');
            $table->enum('content_type', ['light', 'educational', 'course_preview'])
                ->default('light')
                ->after('content_format');

            $table->index('is_featured');
            $table->index('content_type');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('level');
            $table->unsignedBigInteger('views_count')->default(0)->after('is_featured');

            $table->index('is_featured');
            $table->index('views_count');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'content_type']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'views_count']);
        });
    }
};
```

**Step 4: Update Article model**

Modify: `app/Models/Article.php`

Add to $fillable:
```php
'is_featured',
'content_type',
```

Add to $casts:
```php
'is_featured' => 'boolean',
```

Add scopes:
```php
public function scopeFeatured($query)
{
    return $query->where('is_featured', true);
}

public function scopeByContentType($query, $type)
{
    return $query->where('content_type', $type);
}

public function scopeLightContent($query)
{
    return $query->where('content_type', 'light');
}

public function scopeEducationalContent($query)
{
    return $query->where('content_type', 'educational');
}

public function scopeCoursePreview($query)
{
    return $query->where('content_type', 'course_preview');
}
```

**Step 5: Update Course model**

Modify: `app/Models/Course.php`

Add to $fillable:
```php
'is_featured',
'views_count',
```

Add to $casts:
```php
'is_featured' => 'boolean',
```

Add methods:
```php
public function recordView(): void
{
    $this->increment('views_count');
}

public function scopeFeatured($query)
{
    return $query->where('is_featured', true);
}

public function scopePopular($query, $limit = 10)
{
    return $query->orderBy('views_count', 'desc')->limit($limit);
}

public function scopeTrending($query, $limit = 4)
{
    return $query->where('created_at', '>=', now()->subDays(30))
        ->orderBy('views_count', 'desc')
        ->limit($limit);
}
```

**Step 6: Run migration**

Run: `php artisan migrate`

Expected: Migration successful

**Step 7: Run test to verify it passes**

Run: `php artisan test tests/Unit/Models/ArticleMediaTest.php`

Expected: PASS (all 4 tests)

**Step 8: Commit**

```bash
git add database/migrations/2025_11_08_000003_add_media_fields_to_articles_and_courses.php
git add app/Models/Article.php
git add app/Models/Course.php
git add tests/Unit/Models/ArticleMediaTest.php
git commit -m "feat: add featured flags and content type classification"
```

---

## Phase 2: Search Infrastructure (Laravel Scout)

### Task 4: Install and Configure Laravel Scout with Meilisearch

**Files:**
- Modify: `composer.json`
- Create: `config/scout.php`
- Modify: `app/Models/Article.php`
- Modify: `app/Models/Course.php`
- Test: `tests/Feature/SearchFunctionalityTest.php`

**Step 1: Install Laravel Scout**

Run: `composer require laravel/scout`

Expected: Package installed successfully

**Step 2: Publish Scout configuration**

Run: `php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"`

Expected: Configuration file published to config/scout.php

**Step 3: Install Meilisearch driver**

Run: `composer require meilisearch/meilisearch-php`

Expected: Package installed successfully

**Step 4: Configure Scout in .env**

Add to `.env.example`:
```env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=
```

**Step 5: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_are_searchable()
    {
        $this->markTestSkipped('Requires Meilisearch running - run manually');

        $article = Article::factory()->create([
            'title' => 'Niacinamide Benefits for Skin',
            'status' => 'published',
        ]);

        $results = Article::search('niacinamide')->get();

        $this->assertCount(1, $results);
        $this->assertEquals($article->id, $results->first()->id);
    }

    public function test_courses_are_searchable()
    {
        $this->markTestSkipped('Requires Meilisearch running - run manually');

        $course = Course::factory()->create([
            'title' => 'Advanced Skincare Formulation',
        ]);

        $results = Course::search('skincare')->get();

        $this->assertCount(1, $results);
        $this->assertEquals($course->id, $results->first()->id);
    }
}
```

**Step 6: Update Article model with Searchable trait**

Modify: `app/Models/Article.php`

Add import:
```php
use Laravel\Scout\Searchable;
```

Add trait to class:
```php
use Searchable;
```

Add method:
```php
public function toSearchableArray()
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'excerpt' => $this->excerpt,
        'author' => $this->author,
        'content' => strip_tags($this->content),
        'status' => $this->status,
        'content_type' => $this->content_type,
        'published_at' => $this->published_at?->timestamp,
    ];
}

public function shouldBeSearchable()
{
    return $this->status === 'published';
}
```

**Step 7: Update Course model with Searchable trait**

Modify: `app/Models/Course.php`

Add import:
```php
use Laravel\Scout\Searchable;
```

Add trait:
```php
use Searchable;
```

Add method:
```php
public function toSearchableArray()
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'instructor' => $this->instructor,
        'level' => $this->level,
    ];
}
```

**Step 8: Create documentation for Meilisearch setup**

Create: `docs/MEILISEARCH_SETUP.md`

```markdown
# Meilisearch Setup

## Installation

### macOS (Homebrew)
```bash
brew install meilisearch
brew services start meilisearch
```

### Linux/WSL
```bash
curl -L https://install.meilisearch.com | sh
./meilisearch
```

### Docker
```bash
docker run -d -p 7700:7700 getmeili/meilisearch:latest
```

## Initial Index

After starting Meilisearch, index existing content:

```bash
php artisan scout:import "App\Models\Article"
php artisan scout:import "App\Models\Course"
```

## Testing

Check Meilisearch is running:
```bash
curl http://127.0.0.1:7700/health
```

## Production

Set MEILISEARCH_KEY in production environment for security.
```

**Step 9: Commit**

```bash
git add composer.json composer.lock
git add config/scout.php
git add app/Models/Article.php
git add app/Models/Course.php
git add tests/Feature/SearchFunctionalityTest.php
git add docs/MEILISEARCH_SETUP.md
git add .env.example
git commit -m "feat: integrate Laravel Scout with Meilisearch for advanced search"
```

---

### Task 5: Create Advanced Search Controller and Views

**Files:**
- Modify: `app/Http/Controllers/SearchController.php`
- Create: `resources/views/search/advanced.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/SearchControllerTest.php`

**Step 1: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_advanced_search_page_loads()
    {
        $response = $this->get('/search/advanced');

        $response->assertStatus(200);
        $response->assertViewIs('search.advanced');
    }

    public function test_search_filters_by_type()
    {
        Article::factory()->create(['title' => 'Test Article', 'status' => 'published']);
        Course::factory()->create(['title' => 'Test Course']);

        $response = $this->get('/search/advanced?q=test&type=articles');

        $response->assertStatus(200);
        $response->assertViewHas('results');
    }

    public function test_search_filters_by_category()
    {
        $category = ArticleCategory::factory()->create(['name' => 'Skincare']);
        $article = Article::factory()->create(['status' => 'published']);
        $article->categories()->attach($category);

        $response = $this->get('/search/advanced?q=&category=' . $category->id);

        $response->assertStatus(200);
    }

    public function test_search_requires_query_or_filters()
    {
        $response = $this->get('/search/advanced');

        $response->assertStatus(200);
        $response->assertViewHas('results', collect([]));
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/SearchControllerTest.php`

Expected: FAIL with route not found or method not found

**Step 3: Update SearchController**

Modify: `app/Http/Controllers/SearchController.php`

Replace entire file with:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Course;
use App\Models\ArticleCategory;
use App\Models\Tag;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return view('search.results', [
                'query' => '',
                'articles' => collect([]),
                'courses' => collect([]),
            ]);
        }

        $articles = Article::search($query)
            ->where('status', 'published')
            ->take(10)
            ->get();

        $courses = Course::search($query)
            ->take(5)
            ->get();

        return view('search.results', compact('query', 'articles', 'courses'));
    }

    public function advanced(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $categoryId = $request->input('category');
        $contentType = $request->input('content_type');
        $sortBy = $request->input('sort', 'relevance');

        $results = collect([]);
        $articles = collect([]);
        $courses = collect([]);

        // Only search if query or filters provided
        if ($query || $categoryId || $contentType) {
            if ($type === 'all' || $type === 'articles') {
                $articlesQuery = $query
                    ? Article::search($query)->where('status', 'published')
                    : Article::published();

                // Apply filters
                if ($categoryId) {
                    $articlesQuery = $articlesQuery instanceof \Laravel\Scout\Builder
                        ? Article::published()->whereHas('categories', fn($q) => $q->where('article_categories.id', $categoryId))
                        : $articlesQuery->whereHas('categories', fn($q) => $q->where('article_categories.id', $categoryId));
                }

                if ($contentType) {
                    $articlesQuery = $articlesQuery instanceof \Laravel\Scout\Builder
                        ? Article::published()->where('content_type', $contentType)
                        : $articlesQuery->where('content_type', $contentType);
                }

                // Apply sorting
                if ($sortBy === 'date' && !($articlesQuery instanceof \Laravel\Scout\Builder)) {
                    $articlesQuery = $articlesQuery->orderBy('published_at', 'desc');
                } elseif ($sortBy === 'popular' && !($articlesQuery instanceof \Laravel\Scout\Builder)) {
                    $articlesQuery = $articlesQuery->orderBy('views_count', 'desc');
                }

                $articles = $articlesQuery->paginate(12);
            }

            if ($type === 'all' || $type === 'courses') {
                $coursesQuery = $query
                    ? Course::search($query)
                    : Course::query();

                // Apply sorting
                if ($sortBy === 'date' && !($coursesQuery instanceof \Laravel\Scout\Builder)) {
                    $coursesQuery = $coursesQuery->orderBy('created_at', 'desc');
                } elseif ($sortBy === 'popular' && !($coursesQuery instanceof \Laravel\Scout\Builder)) {
                    $coursesQuery = $coursesQuery->orderBy('views_count', 'desc');
                }

                $courses = $coursesQuery->paginate(12);
            }
        }

        // Get filter options
        $categories = ArticleCategory::withCount('articles')
            ->having('articles_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('search.advanced', compact(
            'query',
            'type',
            'categoryId',
            'contentType',
            'sortBy',
            'articles',
            'courses',
            'categories'
        ));
    }
}
```

**Step 4: Create advanced search view**

Create: `resources/views/search/advanced.blade.php`

```blade
<x-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold mb-8">Advanced Search</h1>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('search.advanced') }}" class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Search Query --}}
                <div class="lg:col-span-3">
                    <label for="q" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input
                        type="text"
                        name="q"
                        id="q"
                        value="{{ $query ?? '' }}"
                        placeholder="Enter keywords..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    >
                </div>

                {{-- Content Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>All Content</option>
                        <option value="articles" {{ ($type ?? '') === 'articles' ? 'selected' : '' }}>Articles Only</option>
                        <option value="courses" {{ ($type ?? '') === 'courses' ? 'selected' : '' }}>Courses Only</option>
                    </select>
                </div>

                {{-- Category --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" id="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->articles_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Content Type Filter --}}
                <div>
                    <label for="content_type" class="block text-sm font-medium text-gray-700 mb-2">Content Type</label>
                    <select name="content_type" id="content_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Types</option>
                        <option value="light" {{ ($contentType ?? '') === 'light' ? 'selected' : '' }}>Light Content</option>
                        <option value="educational" {{ ($contentType ?? '') === 'educational' ? 'selected' : '' }}>Educational</option>
                        <option value="course_preview" {{ ($contentType ?? '') === 'course_preview' ? 'selected' : '' }}>Course Preview</option>
                    </select>
                </div>

                {{-- Sort --}}
                <div class="lg:col-span-3">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select name="sort" id="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="relevance" {{ ($sortBy ?? 'relevance') === 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="date" {{ ($sortBy ?? '') === 'date' ? 'selected' : '' }}>Latest</option>
                        <option value="popular" {{ ($sortBy ?? '') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                    Search
                </button>
                <a href="{{ route('search.advanced') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
            </div>
        </form>

        {{-- Results --}}
        @if($articles->count() > 0 || $courses->count() > 0)
            {{-- Articles Results --}}
            @if($articles->count() > 0)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold mb-6">Articles ({{ $articles->total() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($articles as $article)
                            <a href="{{ route('article.show', $article->slug) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition">
                                @if($article->thumbnail)
                                    <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover rounded-t-lg">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg mb-2">{{ $article->title }}</h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($article->excerpt, 100) }}</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <span>{{ $article->published_at?->format('M d, Y') }}</span>
                                        <span>{{ $article->views_count }} views</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $articles->links() }}
                    </div>
                </div>
            @endif

            {{-- Courses Results --}}
            @if($courses->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-6">Courses ({{ $courses->total() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($courses as $course)
                            <a href="{{ route('course.show', $course->slug) }}" class="bg-white rounded-lg shadow hover:shadow-lg transition">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover rounded-t-lg">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg mb-2">{{ $course->title }}</h3>
                                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($course->description, 100) }}</p>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-pink-600 font-semibold">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                        <span class="text-gray-500">{{ $course->level }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $courses->links() }}
                    </div>
                </div>
            @endif
        @elseif($query || $categoryId || $contentType)
            <div class="text-center py-12">
                <p class="text-gray-600 text-lg">No results found. Try different search terms or filters.</p>
            </div>
        @endif
    </div>
</x-layout>
```

**Step 5: Add routes**

Modify: `routes/web.php`

Add route:
```php
Route::get('/search/advanced', [SearchController::class, 'advanced'])->name('search.advanced');
```

**Step 6: Run test to verify it passes**

Run: `php artisan test tests/Feature/SearchControllerTest.php`

Expected: PASS (all 4 tests)

**Step 7: Commit**

```bash
git add app/Http/Controllers/SearchController.php
git add resources/views/search/advanced.blade.php
git add routes/web.php
git add tests/Feature/SearchControllerTest.php
git commit -m "feat: add advanced search with filters and sorting"
```

---

## Phase 3: Media-First Homepage Transformation

### Task 6: Create New Homepage Controller and View

**Files:**
- Modify: `app/Http/Controllers/HomeController.php`
- Create: `resources/views/home-media.blade.php`
- Create: `resources/views/components/hero-section.blade.php`
- Test: `tests/Feature/HomePageTest.php`

**Step 1: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_homepage_shows_featured_content()
    {
        $featuredArticle = Article::factory()->create([
            'is_featured' => true,
            'status' => 'published',
        ]);

        $response = $this->get('/');

        $response->assertSee($featuredArticle->title);
    }

    public function test_homepage_shows_latest_articles()
    {
        Article::factory()->count(15)->create(['status' => 'published']);

        $response = $this->get('/');

        $response->assertViewHas('latestArticles');
        $this->assertCount(12, $response->viewData('latestArticles'));
    }

    public function test_homepage_shows_trending_courses()
    {
        Course::factory()->count(5)->create(['views_count' => 100]);

        $response = $this->get('/');

        $response->assertViewHas('trendingCourses');
        $this->assertCount(4, $response->viewData('trendingCourses'));
    }

    public function test_homepage_shows_popular_articles()
    {
        Article::factory()->count(10)->create([
            'status' => 'published',
            'views_count' => 50,
        ]);

        $response = $this->get('/');

        $response->assertViewHas('popularArticles');
    }

    public function test_homepage_shows_article_categories()
    {
        $response = $this->get('/');

        $response->assertViewHas('categories');
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/HomePageTest.php`

Expected: FAIL with missing view data

**Step 3: Update HomeController**

Modify: `app/Http/Controllers/HomeController.php`

Replace with:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Course;
use App\Models\ArticleCategory;

class HomeController extends Controller
{
    public function index()
    {
        // Hero Section: Featured Article or Latest
        $heroContent = Article::published()
            ->featured()
            ->with('categories', 'tags')
            ->latest('published_at')
            ->first()
            ?? Article::published()
                ->with('categories', 'tags')
                ->latest('published_at')
                ->first();

        // Latest Articles (12 for grid)
        $latestArticles = Article::published()
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc')
            ->limit(12)
            ->get();

        // Popular Articles (sidebar/widget)
        $popularArticles = Article::published()
            ->popular(5)
            ->get();

        // Trending Courses (4 cards)
        $trendingCourses = Course::trending(4)
            ->with('category')
            ->get();

        // Featured Course
        $featuredCourse = Course::featured()
            ->with('category')
            ->first();

        // Categories with article counts
        $categories = ArticleCategory::withCount('articles')
            ->having('articles_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('home', compact(
            'heroContent',
            'latestArticles',
            'popularArticles',
            'trendingCourses',
            'featuredCourse',
            'categories'
        ));
    }

    public function showArticle(string $slug)
    {
        $article = Article::published()
            ->with('categories', 'tags', 'comments.user', 'comments.replies.user')
            ->withRichText('body')
            ->where('slug', $slug)
            ->firstOrFail();

        // Record view
        $article->recordView();

        // Related articles by category
        $relatedArticles = Article::published()
            ->whereHas('categories', function($query) use ($article) {
                $query->whereIn('article_categories.id', $article->categories->pluck('id'));
            })
            ->where('id', '!=', $article->id)
            ->limit(3)
            ->get();

        // Related courses by content type or category
        $relatedCourses = collect([]);
        if ($article->content_type === 'course_preview') {
            $relatedCourses = Course::limit(2)->get();
        }

        return view('article.show', compact('article', 'relatedArticles', 'relatedCourses'));
    }
}
```

**Step 4: Create Hero Section Component**

Create: `resources/views/components/hero-section.blade.php`

```blade
@props(['content'])

<div class="relative bg-gradient-to-r from-pink-500 to-purple-600 text-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Text Content --}}
            <div>
                @if($content instanceof \App\Models\Article)
                    <div class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-semibold mb-4">
                        Featured Article
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                        {{ $content->title }}
                    </h1>
                    <p class="text-lg lg:text-xl mb-6 text-white/90">
                        {{ $content->excerpt }}
                    </p>
                    <div class="flex flex-wrap gap-4 items-center mb-6">
                        <span class="text-sm">By {{ $content->author }}</span>
                        <span class="text-sm">{{ $content->published_at?->format('M d, Y') }}</span>
                        <span class="text-sm">{{ $content->views_count }} views</span>
                    </div>
                    <a href="{{ route('article.show', $content->slug) }}"
                       class="inline-block px-8 py-3 bg-white text-pink-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                        Read Article
                    </a>
                @elseif($content instanceof \App\Models\Course)
                    <div class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-semibold mb-4">
                        Featured Course
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                        {{ $content->title }}
                    </h1>
                    <p class="text-lg lg:text-xl mb-6 text-white/90">
                        {{ Str::limit($content->description, 150) }}
                    </p>
                    <div class="flex flex-wrap gap-4 items-center mb-6">
                        <span class="text-sm">{{ $content->level }}</span>
                        <span class="text-sm">By {{ $content->instructor }}</span>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('course.show', $content->slug) }}"
                           class="inline-block px-8 py-3 bg-white text-pink-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                            Enroll Now
                        </a>
                        <span class="inline-block px-8 py-3 bg-white/20 text-white font-semibold rounded-lg">
                            Rp {{ number_format($content->price, 0, ',', '.') }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Image --}}
            <div class="relative">
                @if($content && $content->thumbnail)
                    <img src="{{ asset('storage/' . $content->thumbnail) }}"
                         alt="{{ $content->title }}"
                         class="rounded-lg shadow-2xl w-full h-auto">
                @else
                    <div class="bg-white/20 rounded-lg h-96 flex items-center justify-center">
                        <span class="text-6xl">🌟</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Decorative Wave --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 80C1200 80 1320 70 1380 65L1440 60V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
        </svg>
    </div>
</div>
```

**Step 5: Update home view**

Modify: `resources/views/home.blade.php`

Replace with media-first layout:

```blade
<x-layout>
    {{-- Hero Section --}}
    @if($heroContent)
        <x-hero-section :content="$heroContent" />
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Latest Articles Grid --}}
        <section class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold">Latest Articles</h2>
                <a href="{{ route('article.index') }}" class="text-pink-600 hover:text-pink-700 font-semibold">
                    View All →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latestArticles as $article)
                    <article class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        @if($article->thumbnail)
                            <a href="{{ route('article.show', $article->slug) }}">
                                <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                     alt="{{ $article->title }}"
                                     class="w-full h-48 object-cover rounded-t-lg">
                            </a>
                        @endif
                        <div class="p-5">
                            <div class="flex gap-2 mb-3">
                                @foreach($article->categories->take(2) as $category)
                                    <span class="px-2 py-1 bg-pink-100 text-pink-600 text-xs rounded">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                            <h3 class="font-semibold text-lg mb-2 hover:text-pink-600">
                                <a href="{{ route('article.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4">
                                {{ Str::limit($article->excerpt, 120) }}
                            </p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>{{ $article->published_at?->format('M d, Y') }}</span>
                                <span>{{ $article->views_count }} views</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Category Grid --}}
        <section class="mb-16">
            <h2 class="text-3xl font-bold mb-8">Browse by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('article.index', ['category' => $category->slug]) }}"
                       class="bg-gradient-to-br from-pink-500 to-purple-600 text-white p-6 rounded-lg hover:shadow-lg transition text-center">
                        <div class="text-3xl mb-2">
                            @switch($category->name)
                                @case('SKINCARE') 🧴 @break
                                @case('MYTHBUSTER') 🔬 @break
                                @case('HAIRCARE') 💇 @break
                                @case('DECORATIVE') 💄 @break
                                @default ✨
                            @endswitch
                        </div>
                        <h3 class="font-semibold">{{ $category->name }}</h3>
                        <p class="text-sm text-white/80">{{ $category->articles_count }} articles</p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Trending Courses --}}
        @if($trendingCourses->count() > 0)
            <section class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold">Trending Courses</h2>
                    <a href="{{ route('course.index') }}" class="text-pink-600 hover:text-pink-700 font-semibold">
                        View All →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($trendingCourses as $course)
                        <article class="bg-white rounded-lg shadow hover:shadow-lg transition">
                            @if($course->thumbnail)
                                <a href="{{ route('course.show', $course->slug) }}">
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                         alt="{{ $course->title }}"
                                         class="w-full h-40 object-cover rounded-t-lg">
                                </a>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold mb-2 hover:text-pink-600">
                                    <a href="{{ route('course.show', $course->slug) }}">
                                        {{ $course->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">
                                    {{ Str::limit($course->description, 80) }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-pink-600 font-bold">
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $course->level }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Popular Articles (Sidebar style in grid) --}}
        @if($popularArticles->count() > 0)
            <section class="bg-gray-50 rounded-lg p-8">
                <h2 class="text-2xl font-bold mb-6">Most Popular</h2>
                <div class="space-y-4">
                    @foreach($popularArticles as $index => $article)
                        <div class="flex gap-4">
                            <span class="text-3xl font-bold text-pink-200">{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <h3 class="font-semibold hover:text-pink-600 mb-1">
                                    <a href="{{ route('article.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $article->views_count }} views
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layout>
```

**Step 6: Run test to verify it passes**

Run: `php artisan test tests/Feature/HomePageTest.php`

Expected: PASS (all 6 tests)

**Step 7: Commit**

```bash
git add app/Http/Controllers/HomeController.php
git add resources/views/home.blade.php
git add resources/views/components/hero-section.blade.php
git add tests/Feature/HomePageTest.php
git commit -m "feat: transform homepage to media-first layout with hero section"
```

---

## Phase 4: Navigation & Mega Menu

### Task 7: Create Mega Menu Component

**Files:**
- Create: `resources/views/components/mega-menu.blade.php`
- Modify: `resources/views/layouts/app.blade.php` (or main layout)
- Create: `app/View/Components/MegaMenu.php`
- Test: Manual testing (component rendering)

**Step 1: Create MegaMenu Component Class**

Create: `app/View/Components/MegaMenu.php`

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\ArticleCategory;
use App\Models\CourseCategory;
use Illuminate\View\View;

class MegaMenu extends Component
{
    public $articleCategories;
    public $courseCategories;

    public function __construct()
    {
        $this->articleCategories = ArticleCategory::withCount('articles')
            ->having('articles_count', '>', 0)
            ->orderBy('name')
            ->limit(8)
            ->get();

        $this->courseCategories = CourseCategory::withCount('courses')
            ->having('courses_count', '>', 0)
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        return view('components.mega-menu');
    }
}
```

**Step 2: Create Mega Menu Blade Component**

Create: `resources/views/components/mega-menu.blade.php`

```blade
<nav class="bg-white shadow-md sticky top-0 z-50" x-data="{ articlesOpen: false, coursesOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="text-2xl font-bold text-pink-600">Beautyversity</span>
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center space-x-8">
                {{-- Browse Articles Dropdown --}}
                <div class="relative" @mouseenter="articlesOpen = true" @mouseleave="articlesOpen = false">
                    <button class="flex items-center text-gray-700 hover:text-pink-600 font-medium">
                        Browse Articles
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    {{-- Articles Mega Menu --}}
                    <div x-show="articlesOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 mt-2 w-96 bg-white rounded-lg shadow-xl py-4 px-6"
                         style="display: none;">

                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">By Category</h3>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            @foreach($articleCategories as $category)
                                <a href="{{ route('article.index', ['category' => $category->slug]) }}"
                                   class="flex items-center justify-between p-2 rounded hover:bg-pink-50 transition">
                                    <span class="text-gray-700">{{ $category->name }}</span>
                                    <span class="text-xs text-gray-500">({{ $category->articles_count }})</span>
                                </a>
                            @endforeach
                        </div>

                        <div class="border-t pt-3">
                            <a href="{{ route('article.index') }}"
                               class="text-pink-600 hover:text-pink-700 font-semibold text-sm">
                                View All Articles →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Our Courses Dropdown --}}
                <div class="relative" @mouseenter="coursesOpen = true" @mouseleave="coursesOpen = false">
                    <button class="flex items-center text-gray-700 hover:text-pink-600 font-medium">
                        Our Courses
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    {{-- Courses Mega Menu --}}
                    <div x-show="coursesOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-xl py-4 px-6"
                         style="display: none;">

                        @if($courseCategories->count() > 0)
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">By Category</h3>
                            <div class="space-y-2 mb-4">
                                @foreach($courseCategories as $category)
                                    <a href="{{ route('course.index', ['category' => $category->slug]) }}"
                                       class="flex items-center justify-between p-2 rounded hover:bg-pink-50 transition">
                                        <span class="text-gray-700">{{ $category->name }}</span>
                                        <span class="text-xs text-gray-500">({{ $category->courses_count }})</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="border-t pt-3">
                            <a href="{{ route('course.index') }}"
                               class="text-pink-600 hover:text-pink-700 font-semibold text-sm">
                                View All Courses →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- About --}}
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-pink-600 font-medium">
                    About
                </a>

                {{-- Search Icon --}}
                <a href="{{ route('search.advanced') }}" class="text-gray-700 hover:text-pink-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </a>

                {{-- Auth Links --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-pink-600 font-medium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-600 font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                        Sign Up
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden">
                <button @click="mobileOpen = !mobileOpen" class="text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu (collapsed by default) --}}
    <div x-show="mobileOpen" class="md:hidden bg-white border-t" style="display: none;">
        <div class="px-4 py-3 space-y-3">
            <a href="{{ route('article.index') }}" class="block text-gray-700 hover:text-pink-600">Articles</a>
            <a href="{{ route('course.index') }}" class="block text-gray-700 hover:text-pink-600">Courses</a>
            <a href="{{ route('about') }}" class="block text-gray-700 hover:text-pink-600">About</a>
            <a href="{{ route('search.advanced') }}" class="block text-gray-700 hover:text-pink-600">Search</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:text-pink-600">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-pink-600">Login</a>
                <a href="{{ route('register') }}" class="block text-pink-600">Sign Up</a>
            @endauth
        </div>
    </div>
</nav>
```

**Step 3: Update main layout to use MegaMenu**

Find your main layout file (likely `resources/views/layouts/app.blade.php` or similar) and replace the navigation section with:

```blade
<x-mega-menu />
```

**Step 4: Register component (if needed)**

Laravel 12 auto-discovers components in `app/View/Components`, but verify in `app/Providers/AppServiceProvider.php` if needed.

**Step 5: Test manually**

Run: `php artisan serve` and navigate to homepage

Expected: Mega menu displays with hover dropdowns

**Step 6: Commit**

```bash
git add app/View/Components/MegaMenu.php
git add resources/views/components/mega-menu.blade.php
git add resources/views/layouts/app.blade.php
git commit -m "feat: add hybrid mega menu navigation with category dropdowns"
```

---

## Phase 5: Article Comments System

### Task 8: Create Comment Controller and Form

**Files:**
- Create: `app/Http/Controllers/CommentController.php`
- Create: `resources/views/components/comment-form.blade.php`
- Create: `resources/views/components/comment-list.blade.php`
- Modify: `resources/views/article/show.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/CommentControllerTest.php`

**Step 1: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use App\Models\ArticleComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_post_comment()
    {
        $article = Article::factory()->create(['status' => 'published']);

        $response = $this->post(route('comment.store', $article), [
            'content' => 'Great article!',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_post_comment()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['status' => 'published']);

        $response = $this->actingAs($user)->post(route('comment.store', $article), [
            'content' => 'Great article!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('article_comments', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Great article!',
        ]);
    }

    public function test_user_can_reply_to_comment()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['status' => 'published']);
        $parentComment = ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Parent comment',
        ]);

        $response = $this->actingAs($user)->post(route('comment.store', $article), [
            'content' => 'Reply to parent',
            'parent_id' => $parentComment->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('article_comments', [
            'parent_id' => $parentComment->id,
            'content' => 'Reply to parent',
        ]);
    }

    public function test_comment_requires_content()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['status' => 'published']);

        $response = $this->actingAs($user)->post(route('comment.store', $article), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/CommentControllerTest.php`

Expected: FAIL with route not found

**Step 3: Create CommentController**

Create: `app/Http/Controllers/CommentController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleComment;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:article_comments,id',
        ]);

        $comment = $article->allComments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => true, // Auto-approve for MVP, add moderation later
        ]);

        return back()->with('success', 'Comment posted successfully!');
    }

    public function destroy(ArticleComment $comment)
    {
        // Only allow user to delete their own comments
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}
```

**Step 4: Add routes**

Modify: `routes/web.php`

Add routes:
```php
Route::middleware('auth')->group(function () {
    Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
});
```

**Step 5: Create Comment Form Component**

Create: `resources/views/components/comment-form.blade.php`

```blade
@props(['article', 'parentId' => null])

<div class="bg-white rounded-lg p-6 shadow">
    @auth
        <form method="POST" action="{{ route('comment.store', $article) }}" class="space-y-4">
            @csrf

            @if($parentId)
                <input type="hidden" name="parent_id" value="{{ $parentId }}">
                <p class="text-sm text-gray-600">Replying to comment...</p>
            @endif

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $parentId ? 'Your Reply' : 'Leave a Comment' }}
                </label>
                <textarea
                    name="content"
                    id="content"
                    rows="4"
                    required
                    maxlength="1000"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                    placeholder="Share your thoughts...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    Posting as <span class="font-semibold">{{ auth()->user()->name }}</span>
                </p>
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                    Post Comment
                </button>
            </div>
        </form>
    @else
        <div class="text-center py-8">
            <p class="text-gray-600 mb-4">Please login to leave a comment</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                Login
            </a>
        </div>
    @endauth
</div>
```

**Step 6: Create Comment List Component**

Create: `resources/views/components/comment-list.blade.php`

```blade
@props(['comments'])

<div class="space-y-6">
    @forelse($comments as $comment)
        <div class="bg-white rounded-lg p-6 shadow" id="comment-{{ $comment->id }}">
            {{-- Comment Header --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr($comment->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $comment->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                @if(auth()->id() === $comment->user_id)
                    <form method="POST" action="{{ route('comment.destroy', $comment) }}"
                          onsubmit="return confirm('Are you sure you want to delete this comment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                            Delete
                        </button>
                    </form>
                @endif
            </div>

            {{-- Comment Content --}}
            <div class="prose prose-sm max-w-none mb-4">
                <p class="text-gray-700">{{ $comment->content }}</p>
            </div>

            {{-- Reply Button --}}
            <div x-data="{ replyOpen: false }">
                <button @click="replyOpen = !replyOpen" class="text-pink-600 hover:text-pink-700 text-sm font-semibold">
                    Reply
                </button>

                {{-- Reply Form --}}
                <div x-show="replyOpen" x-transition class="mt-4" style="display: none;">
                    <x-comment-form :article="$comment->article" :parentId="$comment->id" />
                </div>
            </div>

            {{-- Nested Replies --}}
            @if($comment->replies->count() > 0)
                <div class="ml-12 mt-6 space-y-4 border-l-2 border-pink-200 pl-6">
                    @foreach($comment->replies as $reply)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-pink-400 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr($reply->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-900">{{ $reply->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                @if(auth()->id() === $reply->user_id)
                                    <form method="POST" action="{{ route('comment.destroy', $reply) }}"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="text-gray-700 text-sm">{{ $reply->content }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500">No comments yet. Be the first to comment!</p>
        </div>
    @endforelse
</div>
```

**Step 7: Update Article Show View**

Modify: `resources/views/article/show.blade.php`

Add comments section before closing tag:

```blade
{{-- Comments Section --}}
<section class="mt-12">
    <h2 class="text-2xl font-bold mb-6">
        Comments ({{ $article->comments->count() }})
    </h2>

    {{-- Comment Form --}}
    <div class="mb-8">
        <x-comment-form :article="$article" />
    </div>

    {{-- Comment List --}}
    <x-comment-list :comments="$article->comments" />
</section>
```

**Step 8: Run test to verify it passes**

Run: `php artisan test tests/Feature/CommentControllerTest.php`

Expected: PASS (all 4 tests)

**Step 9: Commit**

```bash
git add app/Http/Controllers/CommentController.php
git add resources/views/components/comment-form.blade.php
git add resources/views/components/comment-list.blade.php
git add resources/views/article/show.blade.php
git add routes/web.php
git add tests/Feature/CommentControllerTest.php
git commit -m "feat: add article comments system with nested replies"
```

---

## Phase 6: Social Sharing & SEO

### Task 9: Add Social Sharing Buttons Component

**Files:**
- Create: `resources/views/components/social-share.blade.php`
- Modify: `resources/views/article/show.blade.php`
- Test: Manual testing

**Step 1: Create Social Share Component**

Create: `resources/views/components/social-share.blade.php`

```blade
@props(['title', 'url'])

@php
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($title);
@endphp

<div class="flex items-center gap-3" {{ $attributes }}>
    <span class="text-sm font-semibold text-gray-700">Share:</span>

    {{-- Facebook --}}
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition"
       aria-label="Share on Facebook">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
    </a>

    {{-- Twitter/X --}}
    <a href="https://twitter.com/intent/tweet?text={{ $encodedTitle }}&url={{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center justify-center w-10 h-10 bg-black text-white rounded-full hover:bg-gray-800 transition"
       aria-label="Share on Twitter">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
    </a>

    {{-- WhatsApp --}}
    <a href="https://api.whatsapp.com/send?text={{ $encodedTitle }}%20{{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center justify-center w-10 h-10 bg-green-500 text-white rounded-full hover:bg-green-600 transition"
       aria-label="Share on WhatsApp">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    {{-- LinkedIn --}}
    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center justify-center w-10 h-10 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition"
       aria-label="Share on LinkedIn">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
        </svg>
    </a>

    {{-- Copy Link --}}
    <button
        @click="
            navigator.clipboard.writeText('{{ $url }}');
            $el.querySelector('span').innerText = 'Copied!';
            setTimeout(() => $el.querySelector('span').innerText = 'Copy', 2000);
        "
        class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <span class="text-sm font-semibold">Copy</span>
    </button>
</div>
```

**Step 2: Update Article Show View**

Modify: `resources/views/article/show.blade.php`

Add after article content and before comments:

```blade
{{-- Social Sharing --}}
<div class="border-t border-b py-6 my-8">
    <x-social-share
        :title="$article->title"
        :url="route('article.show', $article->slug)"
    />
</div>
```

**Step 3: Enhance SEO Meta Tags**

The Article model already uses `HasSEO` trait. Verify `getDynamicSEOData()` includes social meta tags.

Modify: `app/Models/Article.php`

Ensure method exists or add:

```php
public function getDynamicSEOData(): SEOData
{
    return new SEOData(
        title: $this->title,
        description: $this->excerpt ?: Str::limit(strip_tags($this->content), 160),
        author: $this->author,
        image: $this->thumbnail ? asset('storage/' . $this->thumbnail) : asset('logo.webp'),
        url: route('article.show', $this->slug),
        published_time: $this->published_at,
        modified_time: $this->updated_at,
        section: $this->categories->first()?->name,
        tags: $this->tags->pluck('name')->toArray(),
        type: 'article',
    );
}
```

**Step 4: Test manually**

Run: `php artisan serve` and navigate to an article

Expected: Social share buttons render and work correctly

**Step 5: Commit**

```bash
git add resources/views/components/social-share.blade.php
git add resources/views/article/show.blade.php
git add app/Models/Article.php
git commit -m "feat: add social sharing buttons with Facebook, Twitter, WhatsApp, LinkedIn"
```

---

## Phase 7: Article-to-Course Conversion

### Task 10: Add Related Courses Widget and Contextual CTAs

**Files:**
- Create: `resources/views/components/related-courses.blade.php`
- Create: `resources/views/components/course-cta.blade.php`
- Modify: `resources/views/article/show.blade.php`
- Test: Manual testing

**Step 1: Create Related Courses Component**

Create: `resources/views/components/related-courses.blade.php`

```blade
@props(['courses'])

@if($courses->count() > 0)
    <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4">Related Courses You Might Like</h3>
        <p class="text-gray-600 text-sm mb-6">
            Take your knowledge to the next level with our professional courses
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($courses as $course)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}"
                             alt="{{ $course->title }}"
                             class="w-full h-32 object-cover">
                    @endif
                    <div class="p-4">
                        <h4 class="font-semibold mb-2 hover:text-pink-600">
                            <a href="{{ route('course.show', $course->slug) }}">
                                {{ $course->title }}
                            </a>
                        </h4>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ Str::limit($course->description, 100) }}
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-pink-600 font-bold text-lg">
                                Rp {{ number_format($course->price, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('course.show', $course->slug) }}"
                               class="px-4 py-2 bg-pink-600 text-white text-sm rounded-lg hover:bg-pink-700">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
```

**Step 2: Create Contextual CTA Component**

Create: `resources/views/components/course-cta.blade.php`

```blade
@props(['course', 'context' => 'default'])

<div class="my-8 p-6 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg">
    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex-1">
            @if($context === 'educational')
                <p class="text-sm font-semibold mb-2">🎓 DEEPEN YOUR KNOWLEDGE</p>
                <h3 class="text-2xl font-bold mb-2">Ready to become an expert?</h3>
                <p class="text-white/90">
                    Learn professional formulation techniques in our comprehensive course
                </p>
            @elseif($context === 'mythbuster')
                <p class="text-sm font-semibold mb-2">🔬 SCIENCE-BASED LEARNING</p>
                <h3 class="text-2xl font-bold mb-2">Want the complete truth?</h3>
                <p class="text-white/90">
                    Understand the science behind beauty claims with our expert-led course
                </p>
            @else
                <p class="text-sm font-semibold mb-2">📚 CONTINUE LEARNING</p>
                <h3 class="text-2xl font-bold mb-2">Interested in learning more?</h3>
                <p class="text-white/90">
                    Check out our related course for deeper insights
                </p>
            @endif
        </div>

        @if($course)
            <div class="flex-shrink-0">
                <a href="{{ route('course.show', $course->slug) }}"
                   class="inline-block px-8 py-3 bg-white text-pink-600 font-bold rounded-lg hover:bg-gray-100 transition">
                    View {{ $course->title }}
                </a>
            </div>
        @else
            <div class="flex-shrink-0">
                <a href="{{ route('course.index') }}"
                   class="inline-block px-8 py-3 bg-white text-pink-600 font-bold rounded-lg hover:bg-gray-100 transition">
                    Browse All Courses
                </a>
            </div>
        @endif
    </div>
</div>
```

**Step 3: Update Article Show View**

Modify: `resources/views/article/show.blade.php`

Add after article content (around 60% through the article):

```blade
{{-- Contextual CTA (appears mid-article for educational content) --}}
@if($article->content_type === 'educational' && $relatedCourses->count() > 0)
    <x-course-cta :course="$relatedCourses->first()" context="educational" />
@elseif($article->categories->contains('slug', 'mythbuster') && $relatedCourses->count() > 0)
    <x-course-cta :course="$relatedCourses->first()" context="mythbuster" />
@endif
```

Add before comments section:

```blade
{{-- Related Courses Widget --}}
@if($relatedCourses->count() > 0)
    <div class="my-12">
        <x-related-courses :courses="$relatedCourses" />
    </div>
@endif
```

**Step 4: Update HomeController to fetch related courses**

Already implemented in Task 6 Step 3. Verify `showArticle()` method fetches related courses based on content type.

**Step 5: Test manually**

Navigate to an educational article or course preview article

Expected: CTAs and related courses display appropriately

**Step 6: Commit**

```bash
git add resources/views/components/related-courses.blade.php
git add resources/views/components/course-cta.blade.php
git add resources/views/article/show.blade.php
git commit -m "feat: add article-to-course conversion with contextual CTAs"
```

---

## Phase 8: Performance & Polish

### Task 11: Add View Tracking Middleware

**Files:**
- Create: `app/Http/Middleware/TrackArticleView.php`
- Modify: `app/Http/Kernel.php` or `bootstrap/app.php`
- Test: `tests/Feature/ViewTrackingMiddlewareTest.php`

**Step 1: Write the failing test**

Create test file:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewTrackingMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_view_increments_on_visit()
    {
        $article = Article::factory()->create([
            'status' => 'published',
            'views_count' => 5,
        ]);

        $this->assertEquals(5, $article->views_count);

        $this->get(route('article.show', $article->slug));

        $this->assertEquals(6, $article->fresh()->views_count);
    }

    public function test_course_view_increments_on_visit()
    {
        $course = \App\Models\Course::factory()->create([
            'views_count' => 10,
        ]);

        $this->assertEquals(10, $course->views_count);

        $this->get(route('course.show', $course->slug));

        $this->assertEquals(11, $course->fresh()->views_count);
    }
}
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/ViewTrackingMiddlewareTest.php`

Expected: Tests might pass if `recordView()` already called in controller. This middleware is for consistency.

**Step 3: Create TrackArticleView Middleware**

Create: `app/Http/Middleware/TrackArticleView.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackArticleView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track view after response sent (non-blocking)
        if ($response->isSuccessful()) {
            $route = $request->route();

            if ($route && $route->hasParameter('article')) {
                $article = $route->parameter('article');
                if ($article && !$request->session()->has('viewed_article_' . $article->id)) {
                    $article->recordView();
                    $request->session()->put('viewed_article_' . $article->id, true);
                }
            }

            if ($route && $route->hasParameter('course')) {
                $course = $route->parameter('course');
                if ($course && !$request->session()->has('viewed_course_' . $course->id)) {
                    $course->recordView();
                    $request->session()->put('viewed_course_' . $course->id, true);
                }
            }
        }

        return $response;
    }
}
```

**Step 4: Register Middleware**

In Laravel 12, modify: `bootstrap/app.php`

Add to middleware groups:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\TrackArticleView::class,
    ]);
})
```

Or in `app/Http/Kernel.php` if using traditional approach:

```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\TrackArticleView::class,
    ],
];
```

**Step 5: Update Controllers to remove duplicate recordView()**

Since middleware handles it, remove `$article->recordView()` from `HomeController::showArticle()` if exists.

**Step 6: Run test to verify it passes**

Run: `php artisan test tests/Feature/ViewTrackingMiddlewareTest.php`

Expected: PASS

**Step 7: Commit**

```bash
git add app/Http/Middleware/TrackArticleView.php
git add bootstrap/app.php
git add app/Http/Controllers/HomeController.php
git add tests/Feature/ViewTrackingMiddlewareTest.php
git commit -m "feat: add view tracking middleware with session deduplication"
```

---

## Testing & Deployment

### Task 12: Create Feature Test Suite

**Files:**
- Create: `tests/Feature/MediaPlatformIntegrationTest.php`

**Step 1: Create Integration Test**

Create test file:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Course;
use App\Models\User;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaPlatformIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_homepage_displays_all_components()
    {
        // Setup
        Article::factory()->count(15)->create(['status' => 'published', 'is_featured' => false]);
        $featured = Article::factory()->create(['status' => 'published', 'is_featured' => true]);
        Course::factory()->count(5)->create(['views_count' => 100]);
        ArticleCategory::factory()->count(3)->create();

        // Visit homepage
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
        $response->assertSee($featured->title);
        $response->assertViewHas('latestArticles');
        $response->assertViewHas('trendingCourses');
        $response->assertViewHas('categories');
    }

    public function test_search_flow_works_end_to_end()
    {
        $this->markTestSkipped('Requires Meilisearch - run manually');

        $article = Article::factory()->create([
            'title' => 'Hyaluronic Acid Benefits',
            'status' => 'published',
        ]);

        $response = $this->get('/search/advanced?q=hyaluronic');

        $response->assertStatus(200);
        $response->assertSee('Hyaluronic Acid Benefits');
    }

    public function test_article_to_course_conversion_flow()
    {
        $category = ArticleCategory::factory()->create();
        $article = Article::factory()->create([
            'status' => 'published',
            'content_type' => 'educational',
        ]);
        $article->categories()->attach($category);

        $course = Course::factory()->create();

        $response = $this->get(route('article.show', $article->slug));

        $response->assertStatus(200);
        $response->assertViewHas('relatedCourses');
    }

    public function test_comment_workflow()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['status' => 'published']);

        // Post comment
        $response = $this->actingAs($user)
            ->post(route('comment.store', $article), [
                'content' => 'Great article!',
            ]);

        $response->assertRedirect();

        // Verify comment appears
        $response = $this->get(route('article.show', $article->slug));
        $response->assertSee('Great article!');
        $response->assertSee($user->name);
    }

    public function test_social_sharing_buttons_present()
    {
        $article = Article::factory()->create(['status' => 'published']);

        $response = $this->get(route('article.show', $article->slug));

        $response->assertSee('facebook.com/sharer');
        $response->assertSee('twitter.com/intent/tweet');
        $response->assertSee('whatsapp.com');
    }
}
```

**Step 2: Run full test suite**

Run: `php artisan test`

Expected: All tests pass (except Meilisearch-dependent ones)

**Step 3: Commit**

```bash
git add tests/Feature/MediaPlatformIntegrationTest.php
git commit -m "test: add comprehensive integration tests for media platform"
```

---

## Documentation

### Task 13: Create Implementation Documentation

**Step 1: Create setup documentation**

Create: `docs/MEDIA_PLATFORM_SETUP.md`

```markdown
# Media Platform Setup Guide

## Overview

Beautyversity has been transformed from a basic LMS+CMS into a full Educational Media Platform with:
- Media-first homepage with hero section
- Advanced search (Laravel Scout + Meilisearch)
- Comments system with nested replies
- Social sharing integration
- Article-to-course conversion funnel
- View tracking and trending content

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL or SQLite
- Meilisearch (for search functionality)

## Installation

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Run Migrations

```bash
php artisan migrate --seed
```

This will create:
- `article_comments` table
- `views_count` columns for articles and courses
- `is_featured` and `content_type` fields

### 3. Setup Meilisearch

Follow instructions in `docs/MEILISEARCH_SETUP.md`

```bash
# Start Meilisearch
docker run -d -p 7700:7700 getmeili/meilisearch:latest

# Index existing content
php artisan scout:import "App\Models\Article"
php artisan scout:import "App\Models\Course"
```

### 4. Compile Assets

```bash
npm run dev
# Or for production
npm run build
```

### 5. Start Development Server

```bash
composer dev
# Runs: server, queue, logs, vite concurrently
```

## Content Management Strategy

### Content Pyramid (60-30-10)

**Light Content (60%)** - Quick tips, trends, how-tos
- Set `content_type = 'light'`
- Focus on SEO keywords
- Shareable on social media

**Educational Content (30%)** - Science-based, myth-busting
- Set `content_type = 'educational'`
- Include contextual course CTAs
- Build authority

**Course Previews (10%)** - Direct conversion
- Set `content_type = 'course_preview'`
- Strong CTAs to related courses
- Success stories, testimonials

### Featured Content

Mark hero content with:
```php
$article->is_featured = true;
$article->save();
```

Only 1-2 articles/courses should be featured at a time for homepage hero.

## Features

### Search

- **Basic**: `/search?q=keyword`
- **Advanced**: `/search/advanced` with filters

Filters available:
- Content type (articles/courses)
- Category
- Content type (light/educational/preview)
- Sort (relevance/date/popular)

### Comments

Users must be authenticated to comment. Comments support:
- Nested replies (1 level deep)
- User deletion of own comments
- Auto-approval (add moderation later)

### Social Sharing

Automatic Open Graph tags via `ralphjsmit/laravel-seo`. Sharing buttons on all articles.

### View Tracking

Automatic via middleware. Session-based deduplication prevents spam.

Access popular content:
```php
Article::popular(10)->get()
Course::trending(5)->get()
```

## Performance

### Recommended Optimizations

1. **Image Optimization**
   - Use WebP format for thumbnails
   - Lazy load images below fold

2. **Caching** (Production)
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Database Indexing**
   - Already indexed: `views_count`, `is_featured`, `content_type`
   - Monitor slow queries

4. **CDN** (Optional)
   - Serve static assets via CDN
   - Configure in `filesystems.php`

## Testing

```bash
# All tests
php artisan test

# Specific suite
php artisan test --testsuite=Feature

# With coverage
php artisan test --coverage
```

**Note**: Search tests require Meilisearch running locally.

## Troubleshooting

**Search not working?**
- Ensure Meilisearch is running: `curl http://127.0.0.1:7700/health`
- Reindex: `php artisan scout:import "App\Models\Article"`

**Comments not saving?**
- Check user is authenticated
- Verify migration ran: `article_comments` table exists

**Views not incrementing?**
- Clear session: `php artisan cache:clear`
- Check middleware registered in `bootstrap/app.php`

## Next Steps

### Phase 2 Features (Future)

- Newsletter subscription system
- Video article support
- Advanced analytics dashboard
- A/B testing for CTAs
- User bookmarks/favorites
- Mobile PWA optimization
```

**Step 2: Update main README**

Modify: `README.md`

Add section:

```markdown
## Media Platform Features

Beautyversity has evolved into a comprehensive Educational Media Platform:

### Discovery & Engagement
- **Media-First Homepage**: Hero section with featured content, latest articles grid
- **Advanced Search**: Powered by Meilisearch with filters and facets
- **Mega Menu Navigation**: Easy browsing by 8 beauty categories
- **Social Sharing**: One-click sharing to Facebook, Twitter, WhatsApp, LinkedIn

### Community
- **Comments System**: Nested replies, user moderation
- **View Tracking**: Popular and trending content
- **Social Proof**: View counts, engagement metrics

### Conversion Funnel
- **Content Pyramid**: 60% light, 30% educational, 10% course previews
- **Contextual CTAs**: Smart course recommendations within articles
- **Related Courses Widget**: Automatic matching by category and content type

For detailed setup, see `docs/MEDIA_PLATFORM_SETUP.md`
```

**Step 3: Commit documentation**

```bash
git add docs/MEDIA_PLATFORM_SETUP.md
git add README.md
git commit -m "docs: add media platform setup and usage documentation"
```

---

## Final Steps

### Task 14: Create Seeder for Demo Content

**Files:**
- Create: `database/seeders/MediaPlatformDemoSeeder.php`

**Step 1: Create Demo Seeder**

Create: `database/seeders/MediaPlatformDemoSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Course;
use App\Models\ArticleComment;
use App\Models\User;

class MediaPlatformDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo users
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@beautyversity.id'],
            [
                'name' => 'Demo User',
                'username' => 'demo',
                'password' => bcrypt('password'),
            ]
        );

        // Mark some existing articles as featured
        $articles = Article::published()->inRandomOrder()->limit(2)->get();
        foreach ($articles as $article) {
            $article->update([
                'is_featured' => true,
                'content_type' => fake()->randomElement(['light', 'educational', 'course_preview']),
                'views_count' => fake()->numberBetween(50, 500),
            ]);
        }

        // Mark some courses as featured
        $courses = Course::inRandomOrder()->limit(1)->get();
        foreach ($courses as $course) {
            $course->update([
                'is_featured' => true,
                'views_count' => fake()->numberBetween(100, 1000),
            ]);
        }

        // Add demo comments
        $articlesForComments = Article::published()->inRandomOrder()->limit(5)->get();
        foreach ($articlesForComments as $article) {
            // Create 2-3 comments per article
            $commentCount = fake()->numberBetween(2, 3);

            for ($i = 0; $i < $commentCount; $i++) {
                $comment = ArticleComment::create([
                    'article_id' => $article->id,
                    'user_id' => $demoUser->id,
                    'content' => fake()->paragraph(),
                    'is_approved' => true,
                ]);

                // Some comments get replies
                if (fake()->boolean(60)) {
                    ArticleComment::create([
                        'article_id' => $article->id,
                        'user_id' => $demoUser->id,
                        'parent_id' => $comment->id,
                        'content' => fake()->sentence(),
                        'is_approved' => true,
                    ]);
                }
            }
        }

        $this->command->info('Media platform demo data seeded successfully!');
    }
}
```

**Step 2: Run seeder**

Run: `php artisan db:seed --class=MediaPlatformDemoSeeder`

Expected: Demo content created

**Step 3: Commit**

```bash
git add database/seeders/MediaPlatformDemoSeeder.php
git commit -m "feat: add demo seeder for media platform features"
```

---

## Summary

**Implementation Complete!**

You've successfully transformed Beautyversity into an Educational Media Platform with:

✅ **Foundation** (Phase 1)
- Comments system with nested replies
- View tracking for articles and courses
- Featured content and content type classification

✅ **Search** (Phase 2)
- Laravel Scout + Meilisearch integration
- Advanced search with filters (category, content type, sort)

✅ **Homepage** (Phase 3)
- Media-first layout with hero section
- Latest articles grid (12 items)
- Trending courses (4 items)
- Popular articles widget
- Category browsing

✅ **Navigation** (Phase 4)
- Hybrid mega menu with category dropdowns
- Mobile-responsive navigation

✅ **Community** (Phase 5)
- Article comments with replies
- User moderation (delete own comments)

✅ **Social** (Phase 6)
- Social sharing buttons (FB, Twitter, WhatsApp, LinkedIn)
- Enhanced SEO meta tags

✅ **Conversion** (Phase 7)
- Contextual course CTAs in articles
- Related courses widget
- Smart matching by category and content type

✅ **Polish** (Phase 8)
- View tracking middleware with session deduplication
- Comprehensive test suite
- Documentation

**Total Implementation Time**: ~16-20 hours for experienced developer

**Next Recommended Steps**:
1. Start Meilisearch and index content
2. Run demo seeder for testing
3. Review all pages manually
4. Adjust styling to match brand
5. Deploy to staging environment

---
