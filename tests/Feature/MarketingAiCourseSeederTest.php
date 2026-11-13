<?php

namespace Tests\Feature;

use App\Models\Course;
use Database\Seeders\MarketingAiCourseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingAiCourseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_bab_1_is_five_videos_and_the_module_pdf_and_reseeding_adds_nothing(): void
    {
        Storage::fake('local');
        $this->seed(MarketingAiCourseSeeder::class);
        $this->seed(MarketingAiCourseSeeder::class);

        $bab1 = Course::where('slug', 'marketing-strategy-in-the-ai-era')->firstOrFail()->sections()->where('order', 1)->firstOrFail();

        $this->assertSame(['video', 'video', 'video', 'video', 'video', 'pdf'], $bab1->lessons->pluck('type')->all());
        Storage::disk('local')->assertExists($bab1->lessons->last()->pdf_path);
        $this->assertSame(15, $bab1->testQuestions('pre')->count());
        $this->assertSame(17, $bab1->testQuestions('post')->count());
    }
}
