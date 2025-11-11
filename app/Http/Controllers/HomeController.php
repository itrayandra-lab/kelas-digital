<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Hero Slider - Hybrid approach (manual + fallback)
        $heroArticles = \App\Models\Article::published()
            ->inHeroSlider()
            ->with('categories', 'tags')
            ->limit(5)
            ->get();

        // Fallback: fill remaining slots with latest articles
        if ($heroArticles->count() < 5) {
            $needed = 5 - $heroArticles->count();
            $excludedIds = $heroArticles->pluck('id');

            $fallbackArticles = \App\Models\Article::published()
                ->whereNull('hero_slider_order')
                ->whereNotIn('id', $excludedIds)
                ->with('categories', 'tags')
                ->orderBy('published_at', 'desc')
                ->limit($needed)
                ->get();

            $heroArticles = $heroArticles->merge($fallbackArticles);
        }

        // 2. Latest Article - 6 most recent published articles
        $latestArticles = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // 3. Terpopuler (Popular) - 4 most-viewed articles (all-time)
        $popularArticles = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->popular()
            ->limit(4)
            ->get();

        // 4. Recommendation - 4 manually curated articles
        $recommendedArticles = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->recommended()
            ->limit(4)
            ->get();

        // 4.5. Featured Courses - 4 manually curated courses (fallback to latest if empty)
        $featuredCourses = \App\Models\Course::featured()
            ->with(['category', 'enrollments'])
            ->withCount('enrollments')
            ->limit(4)
            ->get();

        // Fallback to latest courses if no featured courses
        if ($featuredCourses->isEmpty()) {
            $featuredCourses = \App\Models\Course::latest()
                ->with(['category', 'enrollments'])
                ->withCount('enrollments')
                ->limit(4)
                ->get();
            $isFeaturedFallback = true;
        } else {
            $isFeaturedFallback = false;
        }

        // 5. Trending - 6 articles from last 30 days by views
        $trendingArticles = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->trending()
            ->limit(6)
            ->get();

        // 6. Featured Category - 3 articles from featured category
        $featuredCategory = \App\Models\ArticleCategory::featured()->first();
        $featuredCategoryArticles = collect();

        if ($featuredCategory) {
            $featuredCategoryArticles = \App\Models\Article::published()
                ->with('categories', 'tags')
                ->whereHas('categories', function ($query) use ($featuredCategory) {
                    $query->where('article_categories.id', $featuredCategory->id);
                })
                ->popular()
                ->limit(3)
                ->get();
        }

        // 7. More Articles - 3 articles excluding all previously shown
        $shownIds = collect()
            ->merge($heroArticles->pluck('id'))
            ->merge($latestArticles->pluck('id'))
            ->merge($popularArticles->pluck('id'))
            ->merge($recommendedArticles->pluck('id'))
            ->merge($trendingArticles->pluck('id'))
            ->merge($featuredCategoryArticles->pluck('id'))
            ->unique();

        $moreArticles = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->whereNotIn('id', $shownIds)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('home', compact(
            'heroArticles',
            'latestArticles',
            'popularArticles',
            'recommendedArticles',
            'featuredCourses',
            'isFeaturedFallback',
            'trendingArticles',
            'featuredCategory',
            'featuredCategoryArticles',
            'moreArticles'
        ));
    }
    
    public function showArticle(string $slug)
    {
        $article = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->withRichText('body')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count without updating updated_at
        $article->incrementViews();

        // Load related articles
        $relatedArticles = $article->getRelatedArticles();

        return view('article.show', compact('article', 'relatedArticles'));
    }
}
