<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OriginPromptTest extends TestCase
{
    use RefreshDatabase;

    public function test_learners_who_signed_up_earlier_fill_in_their_origin_from_the_dashboard(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['username' => 'lama'])->assignRole('student');
        $this->actingAs($user);

        $this->get(route('dashboard'))->assertOk()->assertSee('Kamu belajar dari mana?');

        $this->from(route('dashboard'))->put(route('profile.origin'), ['origin_type' => 'mahasiswa', 'institution' => ''])
            ->assertRedirect(route('dashboard'))->assertSessionHasErrors('institution');

        $this->from(route('dashboard'))->put(route('profile.origin'), ['origin_type' => 'mahasiswa', 'institution' => ' Universitas  Padjadjaran '])
            ->assertRedirect(route('dashboard'))->assertSessionHasNoErrors();
        $this->assertSame('Universitas Padjadjaran', $user->fresh()->origin());

        $this->get(route('dashboard'))->assertDontSee('Kamu belajar dari mana?');
    }
}
