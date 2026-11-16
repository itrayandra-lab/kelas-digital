<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request);
    }

    public function showByCategory(Request $request, string $slug): View
    {
        return $this->listing($request, category: ArticleCategory::where('slug', $slug)->firstOrFail());
    }

    public function showByTag(Request $request, Tag $tag): View
    {
        return $this->listing($request, tag: $tag);
    }

    /** All, per-category and per-tag listings share one page, laid out like the course catalog. */
    private function listing(Request $request, ?ArticleCategory $category = null, ?Tag $tag = null): View
    {
        $search = is_string($request->query('q')) ? trim($request->query('q')) : '';
        $sort = $request->query('sort') === 'popular' ? 'popular' : null;

        $articles = Article::published()
            ->with('categories')
            ->when($category, fn ($q) => $q->whereHas('categories', fn ($c) => $c->whereKey($category->id)))
            ->when($tag, fn ($q) => $q->whereHas('tags', fn ($t) => $t->whereKey($tag->id)))
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%")))
            ->when($sort, fn ($q) => $q->orderByDesc('views_count'))
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $published = fn ($q) => $q->published();
        $categories = ArticleCategory::whereHas('articles', $published)->withCount(['articles' => $published])->orderBy('name')->get();
        $tags = Tag::whereHas('articles', $published)->withCount(['articles' => $published])->orderByDesc('articles_count')->take(15)->get();

        return view('article.index', [
            'articles' => $articles,
            'categories' => $categories,
            'tags' => $tags,
            'activeCategory' => $category,
            'activeTag' => $tag,
            'search' => $search,
            'sort' => $sort,
            'query' => array_filter(['q' => $search ?: null, 'sort' => $sort]),
        ]);
    }
}
