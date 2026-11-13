<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LessonPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_uploads_a_pdf_lesson_that_only_enrolled_learners_can_read(): void
    {
        Storage::fake('local');
        $this->seed(RolePermissionSeeder::class);
        $category = CourseCategory::create(['name' => 'Marketing', 'description' => '-']);
        $course = Course::create([
            'title' => 'Kelas PDF', 'instructor' => 'Tim', 'description' => 'Kelas.', 'course_type' => 'paid', 'price' => 50000,
            'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner',
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Bab 1', 'order' => 1]);

        $admin = User::factory()->create(['username' => 'admin1'])->assignRole('admin');
        $this->actingAs($admin)->post(route('admin.curriculum.lessons.store', $section), [
            'title' => 'Modul 01 lengkap', 'type' => 'pdf',
            'pdf' => UploadedFile::fake()->create('modul01.pdf', 200, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $lesson = Lesson::firstOrFail();
        $this->assertTrue($lesson->isPdf());
        Storage::disk('local')->assertExists($lesson->pdf_path);

        // Saving again without a new file keeps the PDF; switching to text removes it.
        $this->put(route('admin.curriculum.lessons.update', $lesson), ['title' => 'Modul 01', 'type' => 'pdf'])->assertSessionHasNoErrors();
        $this->assertSame($lesson->pdf_path, $lesson->fresh()->pdf_path);

        $url = route('course.lesson.pdf', [$course->slug, $lesson]);
        auth()->logout();
        $this->get($url)->assertRedirect(route('login'));

        $learner = User::factory()->create(['username' => 'peserta'])->assignRole('student');
        $this->actingAs($learner)->get($url)->assertForbidden();

        Enrollment::create([
            'user_id' => $learner->id, 'course_id' => $course->id, 'status' => 'active',
            'enrolled_at' => now(), 'payment_status' => 'completed', 'payment_method' => 'midtrans',
        ]);
        $this->get($url)->assertOk()->assertHeader('Content-Disposition', 'inline');
        $this->get(route('course.lesson', [$course->slug, $lesson]))->assertOk()->assertSee('pdf-pages', false);

        $this->actingAs($admin)->put(route('admin.curriculum.lessons.update', $lesson), ['title' => 'Modul 01', 'type' => 'text', 'content' => '<p>Isi</p>']);
        Storage::disk('local')->assertMissing($lesson->pdf_path);
    }
}
