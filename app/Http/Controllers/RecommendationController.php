<?php

namespace App\Http\Controllers;

use App\Models\Article;

class RecommendationController extends Controller
{
    /**
     * Display all recommended articles with pagination
     */
    public function index()
    {
        $articles = Article::published()
            ->with('categories', 'tags')
            ->recommended()
            ->paginate(12);

        return view('recommendations.index', compact('articles'));
    }
}
