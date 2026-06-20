<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     *
     * @return View
     */
    public function index()
    {
        $articles = Article::published()
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('article.index', compact('articles')); // Pass articles to the view
    }

    /**
     * Load more articles via AJAX.
     *
     * @return JsonResponse
     */
    public function loadMore(Request $request)
    {
        $page = $request->input('page', 1);
        $categorySlug = $request->input('category_slug');

        $query = Article::published()
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc');

        if ($categorySlug) {
            $query->whereHas('categories', function ($builder) use ($categorySlug) {
                $builder->where('slug', $categorySlug);
            });
        }

        $articles = $query->paginate(9, ['*'], 'page', $page);

        // Return only the articles view fragment for AJAX
        $articlesHtml = view('article.partials.articles', compact('articles'))->render();

        return response()->json([
            'articles_html' => $articlesHtml,
            'has_more' => $articles->hasMorePages(),
        ]);
    }

    /**
     * Display articles filtered by category slug.
     *
     * @param  string  $slug
     * @return View
     */
    public function showByCategory($slug)
    {
        $category = ArticleCategory::where('slug', $slug)->firstOrFail();

        $articles = Article::published()
            ->with('categories', 'tags')
            ->whereHas('categories', function ($builder) use ($category) {
                $builder->where('article_categories.id', $category->id);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('article.category', compact('articles', 'category'));
    }
}
