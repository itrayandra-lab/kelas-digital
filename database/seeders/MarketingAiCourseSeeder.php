<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * "Marketing Strategy in the AI Era" from docs/docs (Master Course Structure V2.3, Modul 01, Module 02 tests).
 * Content lives in data/marketing-ai-course.json; diagrams in public/img/courses/marketing-ai.
 * Safe to run on production: when the course already exists it only adds tests that chapters are still missing.
 *
 * php artisan db:seed --class=MarketingAiCourseSeeder
 */
class MarketingAiCourseSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/marketing-ai-course.json')), true, flags: JSON_THROW_ON_ERROR);

        if ($existing = Course::where('slug', $data['slug'])->first()) {
            $this->addMissingTests($existing, $data['sections']);

            return;
        }

        DB::transaction(function () use ($data) {
            $course = Course::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'instructor' => 'Tim Ray Academy',
                'description' => $data['description'],
                'benefits' => $data['benefits'],
                // shortcut: placeholder price, not in the docs; set the real one in Admin → Manage Courses.
                'price' => 299000,
                'course_type' => 'paid',
                'level' => 'Beginner',
                'thumbnail' => 'default-course.jpg',
                'trailer_video_id' => $data['trailer'],
                'course_category_id' => CourseCategory::firstOrCreate(['name' => 'Digital Marketing'], ['description' => 'Kelas digital marketing'])->id,
            ]);

            foreach ($data['sections'] as $sectionOrder => $sectionData) {
                $section = $course->sections()->create([
                    'title' => $sectionData['title'],
                    'order' => $sectionOrder + 1,
                    'is_free' => $sectionData['is_free'],
                ]);

                foreach ($sectionData['lessons'] as $lessonOrder => $lesson) {
                    $section->lessons()->create([
                        'course_id' => $course->id,
                        'title' => $lesson['title'],
                        'type' => $lesson['type'],
                        'youtube_video_id' => $lesson['video'] ?? null,
                        'content' => $lesson['html'] ?? null,
                        'module' => $sectionData['title'],
                        'order' => $lessonOrder + 1,
                    ]);
                }

                foreach (['pre', 'post'] as $kind) {
                    $this->createQuestions($section, $kind, $sectionData[$kind]);
                }
            }
        });
    }

    /**
     * For a course already on the server: adds a chapter's tests only where that chapter has none of that
     * kind yet, so questions an admin wrote or edited are never touched.
     */
    private function addMissingTests(Course $course, array $sections): void
    {
        DB::transaction(function () use ($course, $sections) {
            foreach ($sections as $order => $sectionData) {
                $section = $course->sections()->where('order', $order + 1)->first();

                foreach (['pre', 'post'] as $kind) {
                    if ($section && $sectionData[$kind] && ! $section->testQuestions($kind)->exists()) {
                        $this->createQuestions($section, $kind, $sectionData[$kind]);
                        $this->command?->info("{$section->title}: {$kind}-test ditambahkan.");
                    }
                }
            }
        });
    }

    private function createQuestions(CourseSection $section, string $kind, array $questions): void
    {
        foreach ($questions as $order => $q) {
            $question = $section->questions()->create([
                'course_id' => $section->course_id,
                'kind' => $kind,
                'type' => $q['type'],
                'question' => $q['question'],
                'answer_key' => $q['answer_key'] ?? null,
                'order' => $order + 1,
            ]);
            foreach ($q['options'] ?? [] as $option) {
                $question->options()->create(['text' => $option['text'], 'is_correct' => $option['correct']]);
            }
        }
    }
}
