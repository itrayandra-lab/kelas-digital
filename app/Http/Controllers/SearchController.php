<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));
        $categoryId = $request->input('category_id');
        $tagIds = $request->input('tag_id', []);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $courses = collect();
        $articles = collect();

        // Popular data for landing state (no keyword)
        $popularCourses = collect();
        $popularArticles = collect();
        $popularCategories = collect();

        if ($keyword !== '') {
            $courses = Course::with('category')
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%")
                      ->orWhere('instructor', 'like', "%{$keyword}%");
                })
                ->orderByDesc('created_at')
                ->limit(12)
                ->get();

            $articleQuery = Article::published()
                ->with('categories', 'tags')
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('excerpt', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%")
                      ->orWhere('author', 'like', "%{$keyword}%");
                });

            if ($categoryId) {
                $articleQuery->whereHas('categories', fn($q) =>
                    $q->where('article_categories.id', $categoryId));
            }
            if (!empty($tagIds)) {
                $tagIds = is_array($tagIds) ? $tagIds : [$tagIds];
                $articleQuery->whereHas('tags', fn($q) =>
                    $q->whereIn('tags.id', $tagIds));
            }
            if ($dateFrom) $articleQuery->where('published_at', '>=', $dateFrom);
            if ($dateTo)   $articleQuery->where('published_at', '<=', $dateTo);

            $articles = $articleQuery->orderByDesc('published_at')->paginate(12);

        } else {
            // No keyword — show popular/featured content
            $popularCourses = Course::with('category')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            $popularArticles = Article::published()
                ->with('categories')
                ->orderByDesc('published_at')
                ->limit(6)
                ->get();

            $popularCategories = ArticleCategory::withCount('articles')
                ->having('articles_count', '>', 0)
                ->orderByDesc('articles_count')
                ->limit(6)
                ->get();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'articles' => $articles->items(),
                'has_more_pages' => $articles->hasMorePages(),
                'current_page' => $articles->currentPage(),
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
            ]);
        }

        $categories = ArticleCategory::withCount('articles')->where('id', '>', 0)->get();
        $tags = Tag::withCount('articles')
            ->whereHas('articles', fn($q) => $q->published())
            ->orderBy('articles_count', 'desc')
            ->limit(15)
            ->get();

        $activeFilters = [];
        if ($categoryId) $activeFilters['category'] = ArticleCategory::find($categoryId);
        if (!empty($tagIds)) $activeFilters['tags'] = Tag::whereIn('id', $tagIds)->get();
        if ($dateFrom || $dateTo) $activeFilters['dates'] = ['from' => $dateFrom, 'to' => $dateTo];

        return view('search.index', compact(
            'keyword', 'courses', 'articles',
            'categories', 'tags',
            'activeFilters',
            'popularCourses', 'popularArticles', 'popularCategories',
            'categoryId' , 'tagIds', 'dateFrom', 'dateTo'
        ) + [
            'selectedCategoryId' => $categoryId,
            'selectedTagIds'     => $tagIds,
            'selectedDateFrom'   => $dateFrom,
            'selectedDateTo'     => $dateTo,
        ]);
    }
}