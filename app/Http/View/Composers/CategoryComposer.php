<?php

namespace App\Http\View\Composers;

use App\Models\ArticleCategory;
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
        $categories = ArticleCategory::whereHas('articles')
            ->withCount('articles')
            ->orderBy('name')
            ->get();

        $view->with('articleCategories', $categories);
    }
}
