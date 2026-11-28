<?php

namespace App\Http\View\Composers;

use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryComposer
{
    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        // Get all article categories for Ray Academy
        // Footer list on every page; categories change rarely, so ten minutes of staleness is fine.
        $categories = Cache::remember('layout:article-categories', 600, fn () => ArticleCategory::whereHas('articles')
            ->withCount('articles')
            ->orderBy('name')
            ->get());

        $view->with('articleCategories', $categories);
    }
}
