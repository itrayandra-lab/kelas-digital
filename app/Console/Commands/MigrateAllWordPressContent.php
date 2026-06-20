<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateAllWordPressContent extends Command
{
    protected $signature = 'migrate:all-wordpress-content {--type=all : Type of content to migrate: all, posts, attachments}';

    protected $description = 'Migrate all WordPress content including posts, drafts, and attachments to Laravel (excluding revisions)';

    public function handle()
    {
        $type = $this->option('type');

        $this->info('Starting WordPress to Laravel comprehensive content migration...');

        if ($type === 'all' || $type === 'posts') {
            $this->migratePostsAndRevisions();
        }

        if ($type === 'all' || $type === 'attachments') {
            $this->migrateAttachments();
        }

        $this->info('All requested content migrated successfully!');
    }

    private function migratePostsAndRevisions()
    {
        $this->info('Migrating posts (excluding ebooks, events, and revisions), drafts...');

        // Connect to WordPress database
        $wp_connection = DB::connection('wordpress');

        // Fetch articles from WordPress - include only posts (exclude ebooks, events, and revisions)
        $wp_articles = $wp_connection->select("
            SELECT
                p.ID as wp_id,
                p.post_title as title,
                p.post_content as content,
                p.post_excerpt as excerpt,
                p.post_name as slug,
                p.post_date as created_at,
                p.post_modified as updated_at,
                p.post_type as post_type,
                p.post_status as post_status,
                u.display_name as author
            FROM wp_posts p
            LEFT JOIN wp_users u ON p.post_author = u.ID
            WHERE p.post_type = 'post'
            AND p.post_status IN ('publish', 'draft')
        ");

        $this->info('Found '.count($wp_articles).' articles to migrate');

        foreach ($wp_articles as $wp_content) {
            $this->processAndSaveContent($wp_content, $wp_connection);
        }

        $this->info('Posts and drafts migration completed!');
    }

    private function migrateAttachments()
    {
        $this->info('Migrating attachments...');

        // Connect to WordPress database
        $wp_connection = DB::connection('wordpress');

        // Fetch attachments from WordPress
        $wp_attachments = $wp_connection->select("
            SELECT
                p.ID as wp_id,
                p.post_title as title,
                p.post_content as content,
                p.post_excerpt as excerpt,
                p.post_date as created_at,
                p.post_modified as updated_at,
                p.post_type as post_type,
                p.guid as file_url,
                p.post_parent as parent_id,
                u.display_name as author
            FROM wp_posts p
            LEFT JOIN wp_users u ON p.post_author = u.ID
            WHERE p.post_type = 'attachment'
            AND p.post_status = 'inherit'
        ");

        $this->info('Found '.count($wp_attachments).' attachments to migrate');

        foreach ($wp_attachments as $wp_attachment) {
            // Download and save the attachment
            $file_path = $this->downloadAndSaveAttachment($wp_attachment->file_url);

            if ($file_path) {
                $this->info("Migrated attachment: {$wp_attachment->title} -> {$file_path}");
            } else {
                $this->error("Failed to migrate attachment: {$wp_attachment->title}");
            }
        }

        $this->info('Attachments migration completed!');
    }

    private function processAndSaveContent($wp_content, $wp_connection)
    {
        // Process content to handle inline images
        $processed_content = $this->processArticleContent($wp_content->content, $wp_connection);

        // Get featured image (skip for revisions)
        $featured_image_path = null;
        if ($wp_content->post_type !== 'revision') {
            $featured_image_path = $this->getFeaturedImage($wp_content->wp_id, $wp_connection);
        }

        // Get categories and tags (skip for revisions)
        $categories = [];
        $tags = [];
        if ($wp_content->post_type !== 'revision') {
            $categories = $this->getCategories($wp_content->wp_id, $wp_connection);
            $tags = $this->getTags($wp_content->wp_id, $wp_connection);
        }

        // Insert into Laravel articles table
        // For revisions, include parent_id in the title to make it unique
        $title = $wp_content->title;
        if ($wp_content->post_type === 'revision') {
            $title = $title.' [REVISION-'.$wp_content->wp_id.']';
        }

        $article = Article::updateOrCreate(
            ['title' => $title],
            [
                'title' => $title,
                'slug' => $this->resolveSlug($wp_content, $title),
                'content' => $processed_content,
                'content_format' => 'wordpress',
                'excerpt' => $wp_content->excerpt,
                'author' => $wp_content->author ?? 'Admin',
                'post_type' => $wp_content->post_type,
                'thumbnail' => $featured_image_path ?? 'default-article.jpg',
                'created_at' => $wp_content->created_at,
                'updated_at' => $wp_content->updated_at,
            ]
        );

        if ($wp_content->post_type !== 'revision') {
            $categoryIds = collect($categories)
                ->map(function ($name) {
                    $slug = Str::slug($name);

                    return ArticleCategory::firstOrCreate([
                        'slug' => $slug,
                    ], [
                        'name' => $name,
                        'slug' => $slug,
                    ])->id;
                })
                ->all();

            $tagIds = collect($tags)
                ->map(function ($name) {
                    $slug = Str::slug($name);

                    return Tag::firstOrCreate([
                        'slug' => $slug,
                    ], [
                        'name' => $name,
                        'slug' => $slug,
                    ])->id;
                })
                ->all();

            $article->categories()->sync($categoryIds);
            $article->tags()->sync($tagIds);
        }

        $this->info("Migrated {$wp_content->post_type}: {$title}");
    }

    private function resolveSlug($wp_content, string $fallbackTitle): string
    {
        $base = trim((string) ($wp_content->slug ?? ''));

        if ($base !== '') {
            return $base;
        }

        return Str::slug($fallbackTitle) ?: 'article-'.$wp_content->wp_id;
    }

    private function processArticleContent($content, $wp_connection)
    {
        // Find all image tags in the content
        $pattern = '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i';

        $content = preg_replace_callback($pattern, function ($matches) {
            $original_src = $matches[1];

            // Download and save the image
            $new_image_path = $this->downloadAndSaveImage($original_src);

            if ($new_image_path) {
                // Replace the image source with the new path
                $new_img_tag = str_replace($original_src, asset('storage/'.$new_image_path), $matches[0]);

                return $new_img_tag;
            }

            return $matches[0]; // Return original if download failed
        }, $content);

        return $content;
    }

    private function getFeaturedImage($post_id, $wp_connection)
    {
        // Get the featured image ID
        $meta = $wp_connection->selectOne("
            SELECT m.meta_value as image_id
            FROM wp_postmeta m
            WHERE m.post_id = ? AND m.meta_key = '_thumbnail_id'
        ", [$post_id]);

        if ($meta) {
            // Get the image URL from the attachment post
            $image_post = $wp_connection->selectOne('
                SELECT guid, post_title
                FROM wp_posts
                WHERE ID = ?
            ', [$meta->image_id]);

            if ($image_post) {
                // Download and save the featured image
                $filename = basename($image_post->guid);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $new_filename = Str::slug(pathinfo($filename, PATHINFO_FILENAME)).'_'.time().'.'.$extension;

                return $this->downloadAndSaveImage($image_post->guid, $new_filename, 'articles');
            }
        }

        return null;
    }

    private function downloadAndSaveImage($url, $filename = null, $directory = 'articles')
    {
        try {
            // Generate filename if not provided
            if (! $filename) {
                $filename = basename($url);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $filename = Str::slug(pathinfo($filename, PATHINFO_FILENAME)).'_'.time().'.'.$extension;
            }

            // Create directory if it doesn't exist
            $full_directory = storage_path('app/public/'.$directory);
            if (! file_exists($full_directory)) {
                mkdir($full_directory, 0755, true);
            }

            // Download the image
            $image_content = file_get_contents($url);
            if ($image_content !== false) {
                $path = $full_directory.'/'.$filename;
                file_put_contents($path, $image_content);

                return $directory.'/'.$filename;
            }
        } catch (\Exception $e) {
            $this->error("Failed to download image: {$url} - ".$e->getMessage());
        }

        return null;
    }

    private function downloadAndSaveAttachment($url)
    {
        try {
            // Extract filename from URL
            $filename = basename(parse_url($url, PHP_URL_PATH));
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);

            // Create a unique filename
            $new_filename = Str::slug($basename).'_'.time().'.'.$extension;

            // Create directory if it doesn't exist
            $full_directory = storage_path('app/public/attachments');
            if (! file_exists($full_directory)) {
                mkdir($full_directory, 075, true);
            }

            // Download the file
            $file_content = file_get_contents($url);
            if ($file_content !== false) {
                $path = $full_directory.'/'.$new_filename;
                file_put_contents($path, $file_content);

                return 'attachments/'.$new_filename;
            }
        } catch (\Exception $e) {
            $this->error("Failed to download attachment: {$url} - ".$e->getMessage());
        }

        return null;
    }

    private function getCategories($post_id, $wp_connection)
    {
        $category_ids = $wp_connection->select("
            SELECT tr.term_taxonomy_id
            FROM wp_term_relationships tr
            JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tr.object_id = ? AND tt.taxonomy = 'category'
        ", [$post_id]);

        $categories = [];
        foreach ($category_ids as $cat_id) {
            $term = $wp_connection->selectOne('
                SELECT name
                FROM wp_terms
                WHERE term_id = ?
            ', [$cat_id->term_taxonomy_id]);

            if ($term) {
                $categories[] = $term->name;
            }
        }

        return $categories;
    }

    private function getTags($post_id, $wp_connection)
    {
        $tag_ids = $wp_connection->select("
            SELECT tr.term_taxonomy_id
            FROM wp_term_relationships tr
            JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tr.object_id = ? AND tt.taxonomy = 'post_tag'
        ", [$post_id]);

        $tags = [];
        foreach ($tag_ids as $tag_id) {
            $term = $wp_connection->selectOne('
                SELECT name
                FROM wp_terms
                WHERE term_id = ?
            ', [$tag_id->term_taxonomy_id]);

            if ($term) {
                $tags[] = $term->name;
            }
        }

        return $tags;
    }
}
