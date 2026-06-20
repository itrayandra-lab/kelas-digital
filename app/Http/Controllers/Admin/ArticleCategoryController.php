<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = ArticleCategory::withCount('articles')->latest()->paginate(10);

        return view('admin.article-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.article-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name',
            'description' => 'nullable|string',
        ]);

        ArticleCategory::create($data);

        return redirect()->route('admin.article-categories.index')->with('success', 'Article category created successfully.');
    }

    public function edit(ArticleCategory $articleCategory)
    {
        return view('admin.article-categories.edit', [
            'category' => $articleCategory,
        ]);
    }

    public function update(Request $request, ArticleCategory $articleCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name,'.$articleCategory->id,
            'description' => 'nullable|string',
        ]);

        $articleCategory->update($data);

        return redirect()->route('admin.article-categories.index')->with('success', 'Article category updated successfully.');
    }

    public function destroy(ArticleCategory $articleCategory)
    {
        $articleCategory->delete();

        return redirect()->route('admin.article-categories.index')->with('success', 'Article category deleted successfully.');
    }
}
