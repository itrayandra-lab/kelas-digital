<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCourseLearnersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_who_enrolled_their_progress_and_module_scores(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $category = CourseCategory::create(['name' => 'Marketing', 'description' => '-']);
        $course = Course::create([
            'title' => 'Kelas Uji', 'instructor' => 'Tim', 'description' => '-', 'course_type' => 'paid', 'price' => 100000,
            'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner',
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Modul Satu', 'order' => 1, 'is_free' => true]);
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Materi', 'type' => 'text', 'content' => '<p>x</p>', 'order' => 1, 'module' => 'Modul Satu']);
        $section->questions()->create(['course_id' => $course->id, 'kind' => 'pre', 'type' => 'single', 'question' => 'A?', 'order' => 1]);
        $section->questions()->create(['course_id' => $course->id, 'kind' => 'post', 'type' => 'single', 'question' => 'B?', 'order' => 1]);

        $learner = fn (string $name, array $origin = []) => User::factory()->create(['name' => $name, 'username' => strtolower($name)] + $origin)->assignRole('student');
        $enroll = fn (User $u, array $attrs) => Enrollment::create(['user_id' => $u->id, 'course_id' => $course->id, 'status' => 'active'] + $attrs);

        // Paid and finished: pre 40, lesson done, post 90.
        $enroll($ani = $learner('Ani', ['origin_type' => 'mahasiswa', 'institution' => '  Universitas   Padjadjaran ']), ['payment_status' => 'completed', 'payment_method' => 'midtrans', 'paid_amount' => 100000]);
        $ani->completedLessons()->attach($lesson->id, ['completed_at' => now()]);
        foreach ([['pre', 40, false], ['post', 90, true]] as [$kind, $score, $passed]) {
            QuizAttempt::create(['user_id' => $ani->id, 'course_id' => $course->id, 'section_id' => $section->id, 'kind' => $kind, 'status' => 'graded', 'score' => $score, 'passed' => $passed, 'answers' => []]);
        }
        $enroll($learner('Budi', ['origin_type' => 'karyawan', 'institution' => 'RayCorp']), ['payment_status' => 'pending', 'payment_method' => 'trial']);
        $enroll($learner('Citra'), ['payment_status' => 'pending', 'status' => 'pending']);
        // An instructor who enrolled to check the course isn't a learner.
        $enroll(User::factory()->create(['name' => 'Pak Pengecek', 'username' => 'pengecek'])->assignRole('instructor'), ['payment_status' => 'completed']);

        $this->actingAs(User::factory()->create(['username' => 'adminray'])->assignRole('admin'));
        $this->get(route('admin.courses.show', $course->id))->assertOk()
            ->assertSee('Ani')->assertSee('Budi')->assertSee('Citra')->assertDontSee('Pak Pengecek')
            ->assertSeeInOrder(['Total pendaftar', '3'])
            ->assertSee('Kelas selesai')
            ->assertSee('Rp100.000')                 // revenue, visible to admins
            ->assertSeeInOrder(['Modul Satu', '40', '90', '+50']);

        // Where learners come from: one chip per university or company, filterable, and searchable by name.
        $this->get(route('admin.courses.show', $course->id))
            ->assertSeeInOrder(['Asal peserta', 'RayCorp (1)', 'Universitas Padjadjaran (1)', 'Belum diisi (1)']);
        $this->get(route('admin.courses.show', [$course->id, 'origin' => 'Universitas Padjadjaran']))->assertSee('Ani')->assertDontSee('Budi');
        $this->get(route('admin.courses.show', [$course->id, 'q' => 'raycorp']))->assertSee('Budi')->assertDontSee('Citra');
        $this->get(route('admin.courses.show', [$course->id, 'status' => 'trial']))->assertSee('Budi')->assertDontSee('Ani');
        $this->get(route('admin.courses.show', [$course->id, 'q' => 'citra']))->assertSee('Citra')->assertDontSee('Budi');

        $csv = $this->get(route('admin.courses.learners.export', $course))->assertOk()->streamedContent();
        $lines = array_map(fn ($l) => str_getcsv($l, ';'),array_filter(explode("\n", substr($csv, 3)))); // skip the UTF-8 BOM
        $this->assertSame("\xEF\xBB\xBF", substr($csv, 0, 3));
        $this->assertSame(['Pre-test Modul Satu', 'Post-test Modul Satu'], array_slice($lines[0], -2));
        $ani = collect($lines)->firstWhere(0, 'Ani');
        $this->assertSame(['Mahasiswa / Universitas', 'Universitas Padjadjaran', 'Lunas', '100'], [$ani[3], $ani[4], $ani[5], $ani[7]]);
        $this->assertSame(['Belum diisi', ''], array_slice(collect($lines)->firstWhere(0, 'Citra'), 3, 2));
        $this->assertSame(['40', '90'], array_slice($ani, -2));
        $this->assertCount(4, $lines); // header + 3 learners, no instructor

        // Instructors see the learners but not the money.
        $this->actingAs(User::factory()->create(['username' => 'instruktur'])->assignRole('instructor'));
        $this->get(route('admin.courses.show', $course->id))->assertOk()->assertSee('Ani')->assertDontSee('Pendapatan');
    }
}
