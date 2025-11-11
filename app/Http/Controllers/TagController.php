<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display all tags with article counts
     */
    public function index()
    {
        $tags = Tag::withCount('articles')
            ->whereHas('articles', function ($query) {
                $query->published();
            })
            ->orderBy('articles_count', 'desc')
            ->get();

        return view('tag.index', compact('tags'));
    }

    /**
     * Display articles for a specific tag with pagination
     */
    public function show(Tag $tag, Request $request)
    {
        $articles = $tag->articles()
            ->published()
            ->with('categories', 'tags')
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        // For AJAX requests, return only articles and pagination data
        if ($request->expectsJson()) {
            return response()->json([
                'articles' => $articles->items(),
                'has_more_pages' => $articles->hasMorePages(),
                'current_page' => $articles->currentPage(),
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
            ]);
        }

        return view('tag.show', compact('tag', 'articles'));
    }
}
