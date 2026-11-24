<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolController;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_words_drop_titles_and_degrees(): void
    {
        $this->assertSame(['Anis', 'Yohana', 'Chaerunisaa'], SchoolController::nameWords('Prof. Dr. rer. nat. apt. Anis Yohana Chaerunisaa'));
        $this->assertSame(['Ria', 'Christiana'], SchoolController::nameWords('Ria R. Christiana, MBA'));
    }

    public function test_school_page_lists_its_classes_and_experts(): void
    {
        $category = CourseCategory::create(['name' => 'Digital Marketing', 'slug' => 'digital-marketing']);
        Course::create(['title' => 'Marketing Strategy in the AI Era', 'course_category_id' => $category->id, 'description' => 'Strategi marketing.', 'instructor' => 'Wendra Wilendra M.MT.', 'price' => 0, 'course_type' => 'free', 'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'level' => 'Beginner']);

        $this->get(route('school.show', 'dobetter-class'))
            ->assertOk()
            ->assertSee('Marketing Strategy in the AI Era')
            ->assertSee('Wendra Wilendra, M.MT');

        $this->get(route('experts.index'))->assertOk()->assertSee('Mengajar', false);
        $this->get('/schools/tidak-ada')->assertNotFound();
    }

    public function test_school_without_classes_shows_coming_soon(): void
    {
        $this->get(route('school.show', 'sekolah-kosmetik-indonesia'))->assertOk()->assertSee('sedang disiapkan');
    }
}
