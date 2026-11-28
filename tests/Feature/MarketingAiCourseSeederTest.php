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

    public function test_modul_03_and_04_are_added_once_and_admin_content_of_modul_02_is_kept(): void
    {
        Storage::fake('local');
        $this->seed(MarketingAiCourseSeeder::class);
        $course = Course::where('slug', 'marketing-strategy-in-the-ai-era')->firstOrFail();

        // Modul 02 is filled from the admin, as on the server; reseeding must leave it alone.
        $modul2 = $course->sections()->where('order', 2)->firstOrFail();
        $modul2->questions()->create(['course_id' => $course->id, 'kind' => 'pre', 'type' => 'single', 'question' => 'Soal admin', 'order' => 1]);
        $this->seed(MarketingAiCourseSeeder::class);

        $this->assertSame(1, $modul2->testQuestions('pre')->count());
        foreach ([3 => [10, 'M03 – Sintesis Eksekutif Strategi'], 4 => [9, 'M04 – Strategi Pemasaran Era AI']] as $order => [$lessons, $last]) {
            $modul = $course->sections()->where('order', $order)->firstOrFail();
            $this->assertSame($lessons, $modul->lessons()->count());
            $this->assertSame($last, $modul->lessons()->orderBy('order')->get()->last()->title);
            $this->assertSame(15, $modul->testQuestions('pre')->count());
            $this->assertSame(17, $modul->testQuestions('post')->count());
            $this->assertSame(2, $modul->testQuestions('post')->where('type', 'essay')->count());
            $this->assertSame(1, $modul->pretest_after);
        }
    }
}
