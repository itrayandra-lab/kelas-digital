<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    /**
     * Handle tag creation and return array of tag IDs
     */
    private function handleTags($tags)
    {
        if (empty($tags)) {
            return [];
        }

        $tagIds = [];

        foreach ($tags as $tag) {
            $tagName = trim($tag);
            if (empty($tagName)) {
                continue;
            }

            // Check if tag exists by slug (more reliable than name)
            $slug = Str::slug($tagName);
            $existingTag = Tag::where('slug', $slug)->first();

            if ($existingTag) {
                $tagIds[] = $existingTag->id;
            } else {
                // Create new tag - let Sluggable handle the slug generation
                $newTag = Tag::create([
                    'name' => $tagName,
                ]);
                $tagIds[] = $newTag->id;
            }
        }

        return $tagIds;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with('categories', 'tags')
            ->orderBy('status', 'asc')
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ArticleCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.articles.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'content_format' => 'required|string|in:wordpress,rich_text',
            'content' => 'nullable|required_if:content_format,wordpress',
            'body' => 'nullable|required_if:content_format,rich_text',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'post_type' => 'nullable|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:article_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255', // Allow both existing IDs and new tag names
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => 'nullable|date|after_or_equal:now|required_if:status,scheduled',
            'is_recommended' => 'nullable|boolean',
            'hero_slider_order' => 'nullable|integer|min:1|max:5|unique:articles,hero_slider_order',
        ], [
            'hero_slider_order.unique' => 'This slider position is already taken. Please choose a different order (1-5).',
        ]);

        // Auto-fill author if empty (backup safety)
        if (empty($data['author'])) {
            $data['author'] = Auth::user()->name;
        }

        $thumbnail = 'default-article.jpg';
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->store('articles', 'public');
        }

        $article = Article::create([
            'content_format' => $data['content_format'],
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'] ?? null,
            'body' => $data['body'] ?? null,
            'thumbnail' => $thumbnail,
            'author' => $data['author'],
            'excerpt' => $data['excerpt'] ?? null,
            'post_type' => $data['post_type'] ?? 'post',
            'status' => $data['status'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'published_at' => $data['status'] === 'published' ? now() : null,
            'is_recommended' => $request->has('is_recommended') ? true : false,
            'recommended_at' => $request->has('is_recommended') ? now() : null,
            'hero_slider_order' => $data['hero_slider_order'] ?? null,
        ]);

        $article->categories()->sync($data['categories']);

        // Handle tags (both existing and new ones)
        $tagIds = $this->handleTags($data['tags'] ?? []);
        $article->tags()->sync($tagIds);

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::with('categories', 'tags')
            ->withRichText('body')
            ->findOrFail($id);

        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = Article::with('categories', 'tags')
            ->withRichText('body')
            ->findOrFail($id);
        $categories = ArticleCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.articles.edit', compact('article', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($request->filled('slug')) {
            $request->merge([
                'slug' => Str::slug($request->input('slug')),
            ]);
        }

        $article = Article::findOrFail($id);

        // Build validation rules dynamically
        $scheduledAtRules = ['nullable', 'date', 'required_if:status,scheduled'];

        // Only validate 'after_or_equal:now' if scheduled_at is being changed to a new value
        // Compare datetime values properly to avoid format mismatch (form uses 'Y-m-d\TH:i', DB uses 'Y-m-d H:i:s')
        if ($request->filled('scheduled_at')) {
            $originalScheduledAt = $article->scheduled_at ? $article->scheduled_at->format('Y-m-d H:i') : null;
            $requestScheduledAt = Carbon::parse($request->scheduled_at)->format('Y-m-d H:i');

            if ($requestScheduledAt !== $originalScheduledAt) {
                $scheduledAtRules[] = 'after_or_equal:now';
            }
        }

        // If in_hero_slider checkbox is unchecked, set hero_slider_order to null
        if (! $request->boolean('in_hero_slider')) {
            $request->merge(['hero_slider_order' => null]);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('articles', 'slug')->ignore($id),
            ],
            'content_format' => 'required|string|in:wordpress,rich_text',
            'content' => 'nullable|required_if:content_format,wordpress',
            'body' => 'nullable|required_if:content_format,rich_text',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'post_type' => 'nullable|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:article_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:255', // Allow both existing IDs and new tag names
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => $scheduledAtRules,
            'is_recommended' => 'nullable|boolean',
            'hero_slider_order' => [
                'nullable',
                'integer',
                'min:1',
                'max:5',
                Rule::unique('articles', 'hero_slider_order')->ignore($id),
            ],
        ], [
            'hero_slider_order.unique' => 'This slider position is already taken. Please choose a different order (1-5).',
        ]);

        // Auto-fill author if empty (backup safety)
        if (empty($data['author'])) {
            $data['author'] = Auth::user()->name;
        }

        $payload = [
            'content_format' => $data['content_format'],
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'] ?? null,
            'body' => $data['body'] ?? null,
            'author' => $data['author'],
            'excerpt' => $data['excerpt'] ?? null,
            'post_type' => $data['post_type'] ?? 'post',
            'status' => $data['status'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'hero_slider_order' => $data['hero_slider_order'] ?? null,
        ];

        // Handle is_recommended and recommended_at
        $wasRecommended = $article->is_recommended;
        $isNowRecommended = $request->has('is_recommended');

        if (! $wasRecommended && $isNowRecommended) {
            // Just recommended: set recommended_at to now
            $payload['is_recommended'] = true;
            $payload['recommended_at'] = now();
        } elseif ($wasRecommended && ! $isNowRecommended) {
            // Just unrecommended: clear both fields
            $payload['is_recommended'] = false;
            $payload['recommended_at'] = null;
        }
        // If both true or both false, keep existing recommended_at

        // Handle published_at based on status
        if ($data['status'] === 'published' && ! $article->published_at) {
            $payload['published_at'] = now();
        } elseif ($data['status'] !== 'published') {
            $payload['published_at'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            \Log::info('Thumbnail file detected', [
                'original_name' => $request->file('thumbnail')->getClientOriginalName(),
                'size' => $request->file('thumbnail')->getSize(),
                'mime' => $request->file('thumbnail')->getMimeType(),
            ]);

            $payload['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');

            \Log::info('Thumbnail stored', ['path' => $payload['thumbnail']]);

            session()->flash('thumbnail_info', 'Thumbnail uploaded: '.$request->file('thumbnail')->getClientOriginalName());
        } else {
            \Log::info('No thumbnail file in request', [
                'has_file' => $request->hasFile('thumbnail'),
                'all_files' => $request->allFiles(),
            ]);

            session()->flash('thumbnail_info', 'No thumbnail file detected in request');
        }

        $article->update($payload);
        $article->categories()->sync($data['categories']);

        // Handle tags (both existing and new ones)
        $tagIds = $this->handleTags($data['tags'] ?? []);
        $article->tags()->sync($tagIds);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    }

    /**
     * Publish a scheduled article immediately
     */
    public function publish(string $id)
    {
        $article = Article::findOrFail($id);

        if (! $article->isScheduled()) {
            return redirect()->back()->with('error', 'Only scheduled articles can be published.');
        }

        $article->publish();

        return redirect()->back()->with('success', 'Article published successfully.');
    }

    /**
     * Unschedule a scheduled article (make it draft)
     */
    public function unschedule(string $id)
    {
        $article = Article::findOrFail($id);

        if (! $article->isScheduled()) {
            return redirect()->back()->with('error', 'Only scheduled articles can be unscheduled.');
        }

        $article->unschedule();

        return redirect()->back()->with('success', 'Article unscheduled successfully.');
    }
}
