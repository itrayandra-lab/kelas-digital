<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Hero Slider - course-platform focused (max 5 slides)
        $heroArticles = Article::published()
            ->inHeroSlider()
            ->with('categories', 'tags')
            ->limit(5)
            ->get();

        // Fallback: fill remaining slots with latest articles
        if ($heroArticles->count() < 5) {
            $needed = 5 - $heroArticles->count();
            $excludedIds = $heroArticles->pluck('id');

            $fallbackArticles = Article::published()
                ->whereNull('hero_slider_order')
                ->whereNotIn('id', $excludedIds)
                ->with('categories', 'tags')
                ->orderBy('published_at', 'desc')
                ->limit($needed)
                ->get();

            $heroArticles = $heroArticles->merge($fallbackArticles);
        }

        // Card data shared by the "Mau belajar apa?" tabs.
        $cardData = fn ($q) => $q->with('category')
            ->withCount(['lessons', 'enrollments as students_count' => fn ($e) => $e->where('payment_status', 'completed')]);

        // 2. Featured Courses for the first tab, falling back to the latest ones.
        $featuredCourses = $cardData(Course::featured())->limit(4)->get();
        $isFeaturedFallback = $featuredCourses->isEmpty();

        if ($isFeaturedFallback) {
            $featuredCourses = $cardData(Course::latest())->limit(4)->get();
        }

        // 3. Course Categories, each with its 4 newest courses for the category tabs.
        $courseCategories = CourseCategory::whereHas('courses')
            ->withCount('courses')
            ->with(['courses' => fn ($q) => $cardData($q)->latest()->limit(4)])
            ->orderBy('courses_count', 'desc')
            ->get();

        // Course model has no published() scope — query directly
        $instructorCourses = Course::with('category')
            ->whereNotNull('instructor')
            ->whereNotNull('thumbnail')
            ->inRandomOrder()
            ->limit(10) // fetch more to ensure 5 unique instructors after dedup
            ->get()
            ->unique('instructor')
            ->take(5);

        // 5. Latest Articles - ONE section only (4 articles)
        $latestArticles = Article::published()
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        // 6. Site stats
        $stats = [
            'total_courses' => Course::count(),
            'total_students' => User::count(),
            'total_articles' => Article::published()->count(),
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
        $article = Article::published()
            ->with('categories', 'tags')
            ->withRichText('body')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count with IP-based spam protection
        $article->incrementViewsWithProtection($request->ip());

        // Load related articles
        $relatedArticles = $article->getRelatedArticles();

        // Article categories for sidebar
        $articleCategories = ArticleCategory::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->get();

        return view('article.show', compact('article', 'relatedArticles', 'articleCategories'));
    }
}
