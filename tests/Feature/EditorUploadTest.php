<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditorUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_staff_can_upload_editor_images_and_svg_is_refused(): void
    {
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
        $image = fn (string $name) => ['attachment' => UploadedFile::fake()->create($name, 10, $name === 'x.svg' ? 'image/svg+xml' : 'image/png')];

        $student = User::factory()->create(['username' => 'siswa'])->assignRole('student');
        $this->actingAs($student)->post(route('attachments.store'), $image('a.png'))->assertForbidden();

        $admin = User::factory()->create(['username' => 'staf'])->assignRole('admin');
        $this->actingAs($admin)->post(route('attachments.store'), $image('x.svg'))->assertSessionHasErrors('attachment');
        $this->actingAs($admin)->post(route('attachments.store'), $image('a.png'))->assertOk()->assertJsonStructure(['image_url']);
    }
}
