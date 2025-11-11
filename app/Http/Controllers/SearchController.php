<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle keyword search for courses and articles with filters.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));
        $categoryId = $request->input('category_id');
        $tagIds = $request->input('tag_id', []);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $courses = collect();
        $articles = collect();

        if ($keyword !== '') {
            // Build course query
            $courses = Course::with('category')
                ->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('instructor', 'like', "%{$keyword}%");
                })
                ->orderByDesc('created_at')
                ->limit(12)
                ->get();

            // Build article query
            $articleQuery = Article::published()
                ->with('categories', 'tags')
                ->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('excerpt', 'like', "%{$keyword}%")
                        ->orWhere('content', 'like', "%{$keyword}%")
                        ->orWhere('author', 'like', "%{$keyword}%");
                });

            // Add category filter
            if ($categoryId) {
                $articleQuery->whereHas('categories', function ($query) use ($categoryId) {
                    $query->where('article_categories.id', $categoryId);
                });
            }

            // Add tag filter
            if (!empty($tagIds)) {
                $tagIds = is_array($tagIds) ? $tagIds : [$tagIds];
                $articleQuery->whereHas('tags', function ($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                });
            }

            // Add date range filter
            if ($dateFrom || $dateTo) {
                if ($dateFrom) {
                    $articleQuery->where('published_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $articleQuery->where('published_at', '<=', $dateTo);
                }
            }

            // Paginate articles (12 per page)
            $articles = $articleQuery->orderByDesc('published_at')->paginate(12);
        }

        // For AJAX requests, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'articles' => $articles->items(),
                'has_more_pages' => $articles->hasMorePages(),
                'current_page' => $articles->currentPage(),
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
            ]);
        }

        // Fetch filter options
        $categories = ArticleCategory::withCount('articles')->where('id', '>', 0)->get();
        $tags = Tag::withCount('articles')
            ->whereHas('articles', function ($query) {
                $query->published();
            })
            ->orderBy('articles_count', 'desc')
            ->limit(15)
            ->get();

        // Build active filters
        $activeFilters = [];
        if ($categoryId) {
            $activeFilters['category'] = ArticleCategory::find($categoryId);
        }
        if (!empty($tagIds)) {
            $activeFilters['tags'] = Tag::whereIn('id', $tagIds)->get();
        }
        if ($dateFrom || $dateTo) {
            $activeFilters['dates'] = ['from' => $dateFrom, 'to' => $dateTo];
        }

        return view('search.index', [
            'keyword' => $keyword,
            'courses' => $courses,
            'articles' => $articles,
            'categories' => $categories,
            'tags' => $tags,
            'activeFilters' => $activeFilters,
            'selectedCategoryId' => $categoryId,
            'selectedTagIds' => $tagIds,
            'selectedDateFrom' => $dateFrom,
            'selectedDateTo' => $dateTo,
        ]);
    }
}
