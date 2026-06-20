<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class PublishScheduledArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all scheduled articles that are ready to be published';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled articles ready to be published...');

        // Get articles that are scheduled and ready to be published
        $scheduledArticles = Article::readyToPublish()->get();

        if ($scheduledArticles->isEmpty()) {
            $this->info('No scheduled articles ready to be published.');

            return 0;
        }

        $this->info("Found {$scheduledArticles->count()} scheduled article(s) ready to be published.");

        $publishedCount = 0;
        $failedCount = 0;

        foreach ($scheduledArticles as $article) {
            try {
                $article->publish();
                $this->line("✓ Published: {$article->title}");
                $publishedCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to publish: {$article->title} - {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->info('Publishing completed!');
        $this->info("Successfully published: {$publishedCount} article(s)");

        if ($failedCount > 0) {
            $this->warn("Failed to publish: {$failedCount} article(s)");
        }

        return 0;
    }
}
