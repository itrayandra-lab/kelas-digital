<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleListingTest extends TestCase
{
    use RefreshDatabase;

    private function article(string $title, array $attributes = []): Article
    {
        return Article::create($attributes + [
            'title' => $title, 'content' => '<p>Isi.</p>', 'excerpt' => "Ringkasan {$title}.",
            'status' => 'published', 'published_at' => now(), 'post_type' => 'post', 'author' => 'Tim',
        ]);
    }

    public function test_listing_filters_by_category_tag_and_search(): void
    {
        $marketing = ArticleCategory::create(['name' => 'Digital Marketing', 'slug' => 'digital-marketing']);
        $tag = Tag::create(['name' => 'Branding', 'slug' => 'branding']);

        $this->article('Strategi Konten', ['thumbnail' => 'https://picsum.photos/seed/x/800/600'])->categories()->attach($marketing);
        $this->article('Membangun Brand')->tags()->attach($tag);
        $this->article('Draf Rahasia', ['status' => 'draft']);

        $this->get(route('article.index'))->assertOk()
            ->assertSee('Strategi Konten')->assertSee('Membangun Brand')->assertDontSee('Draf Rahasia')
            // Imported thumbnails are full URLs and are used as they are.
            ->assertSee('src="https://picsum.photos/seed/x/800/600"', false);

        $this->get(route('article.category', 'digital-marketing'))->assertOk()
            ->assertSee('Strategi Konten')->assertDontSee('Membangun Brand');

        $this->get(route('tag.show', 'branding'))->assertOk()
            ->assertSee('Artikel tentang')->assertSee('Membangun Brand')->assertDontSee('Strategi Konten');

        $this->get(route('article.index', ['q' => 'brand']))->assertOk()
            ->assertSee('Membangun Brand')->assertDontSee('Strategi Konten');

        $this->get('/articles/tags')->assertRedirect('/articles');
    }
}
