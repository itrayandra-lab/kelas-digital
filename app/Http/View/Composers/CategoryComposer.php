<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\ArticleCategory;

class CategoryComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Daftar kategori yang ingin ditampilkan sesuai dengan desain header
        $desiredCategories = [
            'MYTHBUSTER', 
            'SKINCARE',
            'PERSONALCARE',
            'HAIRCARE',
            'DECORATIVE',
            'MENZONE',
            'BAHANAKTIF',
            'BEAUTYLIFE',
            'COSMETICNEWS'
        ];

        $categories = ArticleCategory::whereIn('name', $desiredCategories)
            ->whereHas('articles')
            ->withCount('articles')
            ->orderByRaw("FIELD(name, '" . implode("', '", $desiredCategories) . "')")
            ->get();

        $view->with('articleCategories', $categories);
    }
}
