<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Course;
use App\Models\Event;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $families = collect(config('homepage.families'))->where('active', true)->sortBy('order')->values();
        $flagship = $families->firstWhere('featured', true);
        $otherFamilies = $families->reject(fn ($family) => $family === $flagship)->values();

        // "Discover your next class": featured classes first, topped up with the newest ones.
        $nextClasses = Course::featured()->with('category')->limit(4)->get();
        $nextClasses = $nextClasses->concat(
            Course::with('category')->whereKeyNot($nextClasses->modelKeys())->latest()->limit(4 - $nextClasses->count())->get()
        );
        // Each class is labelled with its Learning Family, or its own category when no family claims it.
        $nextClasses->each(function (Course $course) use ($families) {
            $family = $families->first(fn ($f) => in_array($course->category?->slug, $f['categories'], true));
            $course->family_name = $family['name'] ?? $course->category?->name;
            $course->family_theme = $family['theme'] ?? 'navy';
        });

        // Knowledge & Insights: a recommended article leads, the four newest others follow.
        $leadArticle = Article::published()->recommended()->with('categories')->first()
            ?? Article::published()->with('categories')->latest('published_at')->first();
        $moreArticles = Article::published()->with('categories')
            ->whereKeyNot($leadArticle?->getKey())
            ->latest('published_at')
            ->limit(4)
            ->get();

        $events = Event::published()->upcoming()->with(['sessions' => fn ($q) => $q->withCount('registrations')])->limit(3)->get();

        $whatsapp = Setting::get('social_whatsapp');
        $links = [
            'raya' => config('homepage.links.raya') ?: route('search'),
            'corporate' => config('homepage.links.corporate')
                ?: ($whatsapp ?: 'mailto:'.Setting::get('contact_email').'?subject='.rawurlencode('Konsultasi Corporate Training')),
            'smart_book' => config('homepage.links.smart_book'),
        ];

        return view('home', compact('flagship', 'otherFamilies', 'nextClasses', 'leadArticle', 'moreArticles', 'events', 'links'));
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
