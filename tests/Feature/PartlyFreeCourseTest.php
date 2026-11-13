<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Voucher;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartlyFreeCourseTest extends TestCase
{
    use RefreshDatabase;

    private Course $course;

    private Lesson $freeLesson;

    private Lesson $paidLesson;

    protected function setUp(): void
    {
        parent::setUp();

        $category = CourseCategory::create(['name' => 'Marketing', 'description' => '-']);
        $this->course = Course::create([
            'title' => 'Marketing di Era AI', 'instructor' => 'Tim', 'description' => 'Kelas.', 'course_type' => 'paid',
            'price' => 100000, 'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner',
        ]);
        $free = CourseSection::create(['course_id' => $this->course->id, 'title' => 'Bab gratis', 'order' => 1, 'is_free' => true]);
        $paid = CourseSection::create(['course_id' => $this->course->id, 'title' => 'Bab berbayar', 'order' => 2]);
        $this->freeLesson = $this->lesson($free, 'Materi gratis');
        $this->paidLesson = $this->lesson($paid, 'Materi berbayar');
    }

    private function lesson(CourseSection $section, string $title): Lesson
    {
        return Lesson::create([
            'course_id' => $this->course->id, 'section_id' => $section->id, 'title' => $title, 'type' => 'text',
            'content' => '<p>Isi singkat.</p>', 'order' => 1, 'module' => $section->title,
        ]);
    }

    private function student(): User
    {
        $this->seed(RolePermissionSeeder::class);

        return User::factory()->create(['username' => 'peserta'])->assignRole('student');
    }

    public function test_free_chapters_are_studied_for_free_and_the_rest_waits_for_payment(): void
    {
        $slug = $this->course->slug;
        $this->assertTrue($this->course->isPartlyFree());

        // Only the trailer is public: visitors and users who haven't started can't open any lesson.
        $this->get(route('course.lesson', [$slug, $this->freeLesson]))->assertRedirect(route('course.show', $slug));
        $this->get(route('course.lesson', [$slug, $this->paidLesson]))->assertRedirect(route('course.show', $slug));
        $this->get(route('course.show', $slug))->assertSee('Sebagian Gratis')->assertSee('Login untuk Mulai Gratis')
            ->assertDontSee(route('course.lesson', [$slug, $this->freeLesson]));

        $this->actingAs($user = $this->student());
        $this->get(route('course.lesson', [$slug, $this->freeLesson]))->assertRedirect(route('course.show', $slug));
        $this->post(route('course.enroll', $slug))->assertRedirect(route('course.learn', $slug));
        $this->assertSame('trial', Enrollment::firstOrFail()->payment_method);
        session()->put("intro_seen.{$this->course->id}", true);

        // The free chapter runs like a paid course, with progress saved.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.lesson', [$slug, $this->freeLesson]));
        $this->get(route('course.lesson', [$slug, $this->freeLesson]))->assertOk();
        $this->travel(15)->seconds();
        $this->postJson(route('course.lesson.complete', [$slug, $this->freeLesson]))->assertOk();

        // The next chapter is paid: the learner is sent to checkout and can't open it directly.
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.show', $slug))->assertSessionHas('checkout');
        $this->get(route('course.lesson', [$slug, $this->paidLesson]))->assertRedirect(route('course.learn', $slug));
        $this->postJson(route('course.lesson.complete', [$slug, $this->paidLesson]))->assertForbidden();
        $this->get(route('course.show', $slug))->assertSee('Beli Kelas Lengkap')->assertSee('Lanjut Belajar');
        $this->get(route('dashboard'))->assertSee('Marketing di Era AI')->assertDontSee('Menunggu pembayaran (1)');

        // Paying opens the rest, continuing from where the learner stopped.
        Enrollment::firstOrFail()->markPaid(['payment_method' => 'midtrans']);
        $this->get(route('course.learn', $slug))->assertRedirect(route('course.lesson', [$slug, $this->paidLesson]));
        $this->assertTrue($user->fresh()->enrolledCourses->contains($this->course));
    }

    public function test_vouchers_discount_the_price_and_count_each_payment_once(): void
    {
        $slug = $this->course->slug;
        $this->actingAs($this->student());
        $other = Course::create($this->course->only(['instructor', 'description', 'course_type', 'price', 'thumbnail', 'trailer_video_id', 'course_category_id', 'level']) + ['title' => 'Kelas lain']);

        Voucher::create(['code' => 'hemat50', 'discount_type' => 'percent', 'discount_value' => 50]);
        Voucher::create(['code' => 'LEWAT', 'discount_type' => 'percent', 'discount_value' => 50, 'expires_at' => now()->subDay()]);
        Voucher::create(['code' => 'KHUSUS', 'discount_type' => 'fixed', 'discount_value' => 20000, 'course_id' => $other->id]);
        $gratis = Voucher::create(['code' => 'GRATIS', 'discount_type' => 'fixed', 'discount_value' => 500000, 'max_uses' => 1]);

        $check = fn (string $code) => $this->postJson(route('payment.voucher', $slug), ['voucher' => $code])->json();

        $this->assertSame(['ok' => true, 'total_label' => 'Rp50.000'], array_intersect_key($check(' Hemat50 '), ['ok' => 0, 'total_label' => 0]));
        $this->assertFalse($check('LEWAT')['ok']);
        $this->assertFalse($check('KHUSUS')['ok']);
        $this->assertFalse($check('TIDAKADA')['ok']);
        $this->postJson(route('payment.checkout', $slug), ['voucher' => 'LEWAT'])->assertStatus(422)->assertJson(['error' => 'voucher']);

        // A voucher covering the whole price opens the course without Midtrans, and is used up.
        $this->postJson(route('payment.checkout', $slug), ['voucher' => 'GRATIS'])->assertOk()->assertJson(['paid_by_voucher' => true]);
        $enrollment = Enrollment::firstOrFail();
        $this->assertSame('completed', $enrollment->payment_status);
        $this->assertSame(0, $enrollment->paid_amount);
        $enrollment->markPaid();
        $this->assertSame(1, $gratis->fresh()->used_count);
        $this->assertFalse($check('GRATIS')['ok']);
    }
}
