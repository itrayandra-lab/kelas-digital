<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoFilesTest extends TestCase
{
    use RefreshDatabase;

    private function content(): void
    {
        $category = CourseCategory::create(['name' => 'Technology & AI', 'slug' => 'technology-ai']);
        Course::create(['title' => 'Basic Programming', 'instructor' => 'Taufan', 'description' => '<p>Belajar logika program.</p>', 'price' => 50000,
            'course_type' => 'paid', 'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner']);
        $event = Event::create(['title' => 'Kelas Formulasi', 'format' => 'offline', 'event_date' => today()->addWeek(), 'timezone' => 'WITA', 'venue' => 'EGO Corner Cafe', 'is_published' => true, 'registration_open' => true]);
        $event->sessions()->create(['label' => 'Sesi 1', 'starts_at' => '14:00', 'capacity' => 15]);
        Event::create(['title' => 'Draf Rahasia', 'format' => 'online', 'event_date' => today()->addWeek(), 'timezone' => 'WIB', 'is_published' => false]);
    }

    public function test_only_the_live_domain_may_be_indexed(): void
    {
        $this->get('http://staging.test/robots.txt')->assertOk()->assertSee('Disallow: /');

        $robots = $this->get('http://rayacademy.id/robots.txt')->assertOk()->getContent();
        $this->assertStringContainsString("User-agent: GPTBot\nAllow: /", $robots);
        $this->assertStringContainsString('Disallow: /admin/', $robots);
        $this->assertStringContainsString('Sitemap: https://rayacademy.id/sitemap.xml', $robots);
        $this->get('http://rayacademy.id/ai.txt')->assertOk()->assertSee('https://rayacademy.id/llms.txt');
    }

    public function test_sitemaps_and_llms_list_public_content_only(): void
    {
        $this->content();

        $this->get('http://rayacademy.id/sitemap.xml')->assertOk()->assertSee('https://rayacademy.id/sitemap/events.xml');
        $this->get('http://rayacademy.id/sitemap/courses.xml')->assertOk()->assertSee('https://rayacademy.id/course/basic-programming');
        $this->get('http://rayacademy.id/sitemap/events.xml')->assertOk()->assertSee('/events/kelas-formulasi')->assertDontSee('draf-rahasia');
        $this->get('http://rayacademy.id/sitemap/pages.xml')->assertOk()->assertSee('/courses?category=technology-ai');
        $this->get('http://rayacademy.id/sitemap/nope.xml')->assertNotFound();

        $llms = $this->get('http://rayacademy.id/llms.txt')->assertOk()->getContent();
        foreach (['# RayAcademy', 'Sekolah Kosmetik Indonesia', '[Basic Programming](https://rayacademy.id/course/basic-programming)', 'Kelas Formulasi', '## FAQ'] as $part) {
            $this->assertStringContainsString($part, $llms);
        }
        $this->assertStringNotContainsString('Draf Rahasia', $llms);
        $this->get('http://rayacademy.id/llms-full.txt')->assertOk()->assertSee('KELAS: Basic Programming')->assertSee('EVENT: Kelas Formulasi');
        $this->get('http://rayacademy.id/feed.xml')->assertOk()->assertSee('Event: Kelas Formulasi', false);
        $this->get('/indexnow.txt')->assertNotFound();
    }

    public function test_pages_carry_structured_data(): void
    {
        $this->content();

        $this->get('/')->assertOk()->assertSee('"@type":"FAQPage"', false)->assertSee('"@type":"SearchAction"', false);
        $this->get(route('events.show', 'kelas-formulasi'))->assertSee('"@type":"Event"', false)->assertSee('"startDate":"'.today()->addWeek()->format('Y-m-d').'T14:00:00+08:00"', false)->assertSee('"@type":"BreadcrumbList"', false);
        $this->get(route('course.show', 'basic-programming'))->assertSee('"@type":"Course"', false)->assertSee('"priceCurrency":"IDR"', false);
    }
}
