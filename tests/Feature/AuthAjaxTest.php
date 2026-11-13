<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_answer_json_for_the_ajax_forms(): void
    {
        $this->seed(RolePermissionSeeder::class);
        User::factory()->create(['username' => 'peserta', 'email' => 'peserta@example.com', 'password' => bcrypt('rahasia123')])->assignRole('student');

        $this->postJson(route('login'), ['login' => 'peserta', 'password' => 'salah'])
            ->assertStatus(422)->assertJsonPath('errors.login.0', 'Username/email atau password salah.');
        $this->assertGuest();

        $this->postJson(route('login'), ['login' => 'peserta@example.com', 'password' => 'rahasia123'])
            ->assertOk()->assertJson(['redirect' => url('/dashboard')]);
        $this->assertAuthenticated();

        auth()->logout();
        $this->postJson(route('register'), ['name' => 'Baru', 'username' => 'peserta', 'email' => 'x@gmail.com', 'password' => 'a', 'password_confirmation' => 'b'])
            ->assertStatus(422)->assertJsonValidationErrors(['username', 'password']);

        // The plain form post still redirects, for browsers without JavaScript.
        $this->post(route('login'), ['login' => 'peserta', 'password' => 'salah'])->assertSessionHasErrors('login');
    }
}
