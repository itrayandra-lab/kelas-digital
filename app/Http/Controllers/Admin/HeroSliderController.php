<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeroSliderController extends Controller
{
    /**
     * Display hero slider management page
     */
    public function index()
    {
        $this->authorize('manage articles');

        $heroArticles = Article::inHeroSlider()
            ->with('categories')
            ->get();

        $lastUpdated = Article::whereNotNull('hero_slider_order')
            ->max('updated_at');

        $daysSinceUpdate = $lastUpdated ? now()->diffInDays($lastUpdated) : null;

        return view('admin.hero-slider.index', compact('heroArticles', 'daysSinceUpdate'));
    }

    /**
     * Update hero slider order
     */
    public function update(Request $request)
    {
        $this->authorize('manage articles');

        $validated = $request->validate([
            'articles' => 'required|array|max:5',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.order' => 'required|integer|min:1|max:5|distinct',
        ]);

        DB::transaction(function () use ($validated) {
            // Clear all existing hero slider orders
            Article::whereNotNull('hero_slider_order')->update(['hero_slider_order' => null]);

            // Set new orders
            foreach ($validated['articles'] as $articleData) {
                Article::find($articleData['id'])->update([
                    'hero_slider_order' => $articleData['order']
                ]);
            }
        });

        return redirect()->route('admin.hero-slider.index')
            ->with('success', 'Hero slider updated successfully');
    }

    /**
     * Remove article from hero slider
     */
    public function remove(Article $article)
    {
        $this->authorize('manage articles');

        $article->update(['hero_slider_order' => null]);

        return redirect()->route('admin.hero-slider.index')
            ->with('success', 'Article removed from hero slider');
    }
}
