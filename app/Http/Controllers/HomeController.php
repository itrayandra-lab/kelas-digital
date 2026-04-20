<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArticleCategory;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Hero Slider - course-platform focused (max 5 slides)
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

        // 2. Featured Courses - main section (up to 8 courses)
        $featuredCourses = \App\Models\Course::featured()
            ->with(['category', 'enrollments'])
            ->withCount('enrollments')
            ->limit(8)
            ->get();

        // Fallback to latest courses if no featured courses
        if ($featuredCourses->isEmpty()) {
            $featuredCourses = \App\Models\Course::latest()
                ->with(['category', 'enrollments'])
                ->withCount('enrollments')
                ->limit(8)
                ->get();
            $isFeaturedFallback = true;
        } else {
            $isFeaturedFallback = false;
        }

        // 3. Course Categories - for filter tabs
        $courseCategories = \App\Models\CourseCategory::whereHas('courses')
            ->withCount('courses')
            ->orderBy('courses_count', 'desc')
            ->get();

        // Course model has no published() scope — query directly
        $instructorCourses = \App\Models\Course::with('category')
            ->whereNotNull('instructor')
            ->whereNotNull('thumbnail')
            ->inRandomOrder()
            ->limit(10) // fetch more to ensure 5 unique instructors after dedup
            ->get()
            ->unique('instructor')
            ->take(5);

        // 5. Latest Articles - ONE section only (4 articles)
        $latestArticles = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        // 6. Site stats
        $stats = [
            'total_courses'  => \App\Models\Course::count(),
            'total_students' => \App\Models\User::count(),
            'total_articles' => \App\Models\Article::published()->count(),
        ];

        return view('home', compact(
            'heroArticles',
            'featuredCourses',
            'isFeaturedFallback',
            'courseCategories',
            'instructorCourses',
            'latestArticles',
            'stats'
        ));
    }

    public function showArticle(Request $request, string $slug)
    {
        $article = \App\Models\Article::published()
            ->with('categories', 'tags')
            ->withRichText('body')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count with IP-based spam protection
        $article->incrementViewsWithProtection($request->ip());

        // Load related articles
        $relatedArticles = $article->getRelatedArticles();

        // Article categories for sidebar
        $articleCategories = \App\Models\ArticleCategory::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->get();

        return view('article.show', compact('article', 'relatedArticles', 'articleCategories'));
    }
}