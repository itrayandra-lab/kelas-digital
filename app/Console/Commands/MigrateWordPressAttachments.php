<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateWordPressAttachments extends Command
{
    protected $signature = 'migrate:wordpress-attachments';

    protected $description = 'Migrate media attachments from WordPress to Laravel storage';

    public function handle()
    {
        $this->info('Starting WordPress to Laravel attachment migration...');

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

        $this->info('All attachments migrated successfully!');
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
                mkdir($full_directory, 0755, true);
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
}
