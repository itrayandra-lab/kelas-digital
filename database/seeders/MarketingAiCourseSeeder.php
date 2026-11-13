<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * "Marketing Strategy in the AI Era" from docs/docs (Master Course Structure V2.3, Modul 01, Module 02 tests).
 * Content lives in data/marketing-ai-course.json; module PDFs next to it in data/.
 * Safe to run on production: when the course already exists it only adds the lessons and tests a chapter is
 * still missing, and never touches lessons or questions an admin added or edited.
 *
 * php artisan db:seed --class=MarketingAiCourseSeeder
 */
class MarketingAiCourseSeeder extends Seeder
{
    /** Bab 1 used to split the Modul 01 PDF into reading lessons; the module PDF now replaces them. */
    private const OLD_BAB1_READING = '/^(01\.\d Unit \d: |Tujuan belajar dan peta Modul 01$)/';

    private const PRICE = 599000;

    /** The first seed went out with this placeholder; an admin-set price is never overwritten. */
    private const PLACEHOLDER_PRICE = 299000;

    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/marketing-ai-course.json')), true, flags: JSON_THROW_ON_ERROR);

        if ($existing = Course::where('slug', $data['slug'])->first()) {
            $this->updateExisting($existing, $data['sections']);

            return;
        }

        DB::transaction(function () use ($data) {
            $course = Course::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'instructor' => 'Tim Ray Academy',
                'description' => $data['description'],
                'benefits' => $data['benefits'],
                'price' => self::PRICE,
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
                    $this->createLesson($section, $lesson, $lessonOrder + 1);
                }

                foreach (['pre', 'post'] as $kind) {
                    $this->createQuestions($section, $kind, $sectionData[$kind]);
                }
            }
        });
    }

    /**
     * For a course already on the server: per chapter, adds lessons (matched by title) and tests it is still
     * missing, puts the seeded lessons in the seeded order, and removes Bab 1's old split reading lessons.
     */
    private function updateExisting(Course $course, array $sections): void
    {
        DB::transaction(function () use ($course, $sections) {
            if ((int) $course->price === self::PLACEHOLDER_PRICE) {
                $course->update(['price' => self::PRICE]);
                $this->command?->info('Harga kelas diubah jadi Rp'.number_format(self::PRICE, 0, ',', '.').'.');
            }

            foreach ($sections as $order => $sectionData) {
                $section = $course->sections()->where('order', $order + 1)->first();
                if (! $section) {
                    continue;
                }

                // Chapters were first seeded as "Bab N: ..."; the course calls them modules. Admin-renamed titles stay.
                if (str_starts_with($section->title, 'Bab ') && $section->title !== $sectionData['title']) {
                    $section->update(['title' => $sectionData['title']]);
                }

                if ($order === 0) {
                    $old = $section->lessons()->get()->filter(fn (Lesson $l) => $l->type === 'text' && preg_match(self::OLD_BAB1_READING, $l->title));
                    $old->each->delete();
                    if ($old->isNotEmpty()) {
                        $this->command?->info("{$section->title}: {$old->count()} bacaan lama dihapus.");
                    }
                }

                $existing = $section->lessons()->get()->keyBy('title');
                foreach ($sectionData['lessons'] as $lessonOrder => $lesson) {
                    if ($found = $existing->get($lesson['title'])) {
                        $found->update(['order' => $lessonOrder + 1]);
                    } else {
                        $this->createLesson($section, $lesson, $lessonOrder + 1);
                        $this->command?->info("{$section->title}: materi \"{$lesson['title']}\" ditambahkan.");
                    }
                }

                foreach (['pre', 'post'] as $kind) {
                    if ($sectionData[$kind] && ! $section->testQuestions($kind)->exists()) {
                        $this->createQuestions($section, $kind, $sectionData[$kind]);
                        $this->command?->info("{$section->title}: {$kind}-test ditambahkan.");
                    }
                }
            }
        });
    }

    private function createLesson(CourseSection $section, array $lesson, int $order): void
    {
        $pdfPath = null;
        if (isset($lesson['pdf'])) {
            // Lesson PDFs live on the private disk and are only streamed to learners allowed to open the lesson.
            $pdfPath = 'lesson-pdfs/'.$lesson['pdf'];
            Storage::disk('local')->put($pdfPath, file_get_contents(database_path('seeders/data/'.$lesson['pdf'])));
        }

        $section->lessons()->create([
            'course_id' => $section->course_id,
            'title' => $lesson['title'],
            'type' => $lesson['type'],
            'youtube_video_id' => $lesson['video'] ?? null,
            'content' => $lesson['html'] ?? null,
            'pdf_path' => $pdfPath,
            'module' => $section->title,
            'order' => $order,
        ]);
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
