<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Expert;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolPageTest extends TestCase
{
    use RefreshDatabase;

    private function course(CourseCategory $category, Expert $expert): Course
    {
        return Course::create(['title' => 'Marketing Strategy in the AI Era', 'course_category_id' => $category->id, 'description' => 'Strategi marketing.',
            'instructor' => 'ditimpa', 'expert_id' => $expert->id, 'price' => 0, 'course_type' => 'free', 'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'level' => 'Beginner']);
    }

    public function test_name_words_drop_titles_and_degrees(): void
    {
        $this->assertSame(['Anis', 'Yohana', 'Chaerunisaa'], Expert::nameWords('Prof. Dr. rer. nat. apt. Anis Yohana Chaerunisaa'));
        $this->assertSame(['Ria', 'Christiana'], Expert::nameWords('Ria R. Christiana, MBA'));
    }

    public function test_school_page_lists_its_classes_and_experts(): void
    {
        // The migration adds the eight experts of the homepage photo.
        $wendra = Expert::where('name', 'Wendra Wilendra, M.MT')->firstOrFail();
        $course = $this->course(CourseCategory::create(['name' => 'Digital Marketing', 'slug' => 'digital-marketing']), $wendra);

        $this->assertSame('Wendra Wilendra, M.MT', $course->instructor);
        $this->get(route('school.show', 'dobetter-class'))
            ->assertOk()
            ->assertSee('Marketing Strategy in the AI Era')
            ->assertSee('Wendra Wilendra, M.MT');

        $this->get(route('experts.index'))->assertOk()->assertSee('Mengajar', false);
        $this->get(route('home'))->assertOk()->assertSee('Prof. Dr. apt. Sriwidodo, M.Si');
        $this->get('/schools/tidak-ada')->assertNotFound();
    }

    public function test_school_without_classes_shows_coming_soon(): void
    {
        $this->get(route('school.show', 'sekolah-kosmetik-indonesia'))->assertOk()->assertSee('sedang disiapkan');
    }

    public function test_renaming_an_expert_renames_their_classes_and_articles_and_used_experts_stay(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->actingAs(User::factory()->create(['username' => 'adminray'])->assignRole('admin'));
        $expert = Expert::create(['name' => 'Tamu Satu, S.E.', 'schools' => []]);
        $course = $this->course(CourseCategory::create(['name' => 'Bisnis', 'slug' => 'bisnis']), $expert);
        $article = Article::create(['title' => 'Tips', 'content' => '<p>Isi.</p>', 'status' => 'draft', 'post_type' => 'post', 'author' => 'x', 'expert_id' => $expert->id]);

        $this->put(route('admin.experts.update', $expert), ['name' => 'Tamu Satu, S.E., M.M.', 'schools' => ['levelup-class']])->assertSessionHasNoErrors();
        $this->assertSame('Tamu Satu, S.E., M.M.', $course->fresh()->instructor);
        $this->assertSame('Tamu Satu, S.E., M.M.', $article->fresh()->author);

        $this->delete(route('admin.experts.destroy', $expert));
        $this->assertModelExists($expert);

        // The course form only takes an expert from the list, never typed text.
        $this->put(route('admin.courses.update', $course->id), ['title' => 'Baru', 'instructor' => 'Diketik', 'description' => 'D', 'course_type' => 'free',
            'course_category_id' => $course->course_category_id, 'level' => 'Beginner'])->assertSessionHasErrors('expert_id');
    }
}
