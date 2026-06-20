<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_profile_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.index'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.index');
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
        $response->assertSee($this->user->username);
    }

    /** @test */
    public function guest_cannot_view_profile_page()
    {
        $response = $this->get(route('profile.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_edit_profile_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
        $response->assertSee($this->user->username);
    }

    /** @test */
    public function authenticated_user_can_update_profile()
    {
        $updateData = [
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update'), $updateData);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success', 'Profile updated successfully.');

        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('updateduser', $this->user->username);
        $this->assertEquals('updated@example.com', $this->user->email);
    }

    /** @test */
    public function profile_update_validation_works()
    {
        $invalidData = [
            'name' => '', // Required field
            'username' => 'ab', // Too short
            'email' => 'invalid-email', // Invalid email
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update'), $invalidData);

        $response->assertSessionHasErrors(['name', 'username', 'email']);
    }

    /** @test */
    public function username_must_be_unique_on_update()
    {
        $otherUser = User::factory()->create(['username' => 'existinguser']);

        $updateData = [
            'name' => 'Updated Name',
            'username' => 'existinguser', // Already taken
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update'), $updateData);

        $response->assertSessionHasErrors(['username']);
    }

    /** @test */
    public function email_must_be_unique_on_update()
    {
        $otherUser = User::factory()->create([
            'email' => 'existing@example.com',
            'username' => 'existinguser',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'username' => 'updateduser',
            'email' => 'existing@example.com', // Already taken
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update'), $updateData);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_can_update_own_email_and_username()
    {
        $updateData = [
            'name' => 'Updated Name',
            'username' => $this->user->username, // Same username
            'email' => $this->user->email, // Same email
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update'), $updateData);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success', 'Profile updated successfully.');
    }

    /** @test */
    public function authenticated_user_can_view_change_password_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.change-password'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.change-password');
    }

    /** @test */
    public function authenticated_user_can_change_password()
    {
        $passwordData = [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update-password'), $passwordData);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success', 'Password updated successfully.');

        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    /** @test */
    public function password_change_requires_current_password()
    {
        $passwordData = [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update-password'), $passwordData);

        $response->assertSessionHasErrors(['current_password']);
    }

    /** @test */
    public function password_change_validation_works()
    {
        $invalidData = [
            'current_password' => 'password',
            'password' => '123', // Too short
            'password_confirmation' => 'different', // Doesn't match
        ];

        $response = $this->actingAs($this->user)
            ->put(route('profile.update-password'), $invalidData);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function guest_cannot_access_profile_routes()
    {
        $routes = [
            'profile.index',
            'profile.edit',
            'profile.change-password',
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect(route('login'));
        }

        // Test POST routes
        $response = $this->put(route('profile.update'), []);
        $response->assertRedirect(route('login'));

        $response = $this->put(route('profile.update-password'), []);
        $response->assertRedirect(route('login'));
    }
}
