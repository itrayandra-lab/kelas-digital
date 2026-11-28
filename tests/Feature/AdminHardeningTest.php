<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_html_reaches_the_page_without_scripts(): void
    {
        $article = Article::create([
            'title' => 'Tips Branding', 'content' => '<p>Isi <b>tebal</b>.</p><script>alert(1)</script><img src="x.jpg" onerror="alert(2)">',
            'excerpt' => 'Ringkasan.', 'status' => 'published', 'published_at' => now(), 'post_type' => 'post', 'author' => 'Tim',
        ]);

        $html = $this->get(route('article.show', $article->slug))->assertOk()->getContent();

        $this->assertStringContainsString('<b>tebal</b>', $html);
        $this->assertStringNotContainsString('alert(1)', $html);
        $this->assertStringNotContainsString('alert(2)', $html);
        $this->assertStringNotContainsString('onerror', $article->processed_content);
    }

    public function test_an_admin_cannot_hand_out_super_admin_or_change_their_own_role(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['username' => 'adminray'])->assignRole('admin');
        $student = User::factory()->create(['username' => 'murid'])->assignRole('student');
        $this->actingAs($admin);

        $form = fn (User $u, string $role) => ['name' => $u->name, 'username' => $u->username, 'email' => $u->email, 'role' => $role];

        $this->put(route('admin.users.update', $student), $form($student, 'Super-Admin'))->assertSessionHasErrors('role');
        $this->assertFalse($student->fresh()->hasRole('Super-Admin'));

        $this->put(route('admin.users.update', $admin), $form($admin, 'student'));
        $this->assertTrue($admin->fresh()->hasRole('admin'));

        $this->put(route('admin.users.update', $student), $form($student, 'instructor'))->assertSessionHasNoErrors();
        $this->assertTrue($student->fresh()->hasRole('instructor'));
    }
}
