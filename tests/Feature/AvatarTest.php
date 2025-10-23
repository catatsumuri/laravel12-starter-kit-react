<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up fake storage for testing
        Storage::fake('public');
    }

    public function test_authenticated_user_can_view_avatar()
    {
        $user = User::factory()->create();

        // Upload an avatar
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $user->addMedia($file)->toMediaCollection('avatar');

        $response = $this
            ->actingAs($user)
            ->get(route('avatar.show', $user));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_authenticated_user_can_view_other_users_avatar()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Upload an avatar for other user
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $otherUser->addMedia($file)->toMediaCollection('avatar');

        $response = $this
            ->actingAs($user)
            ->get(route('avatar.show', $otherUser));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_unauthenticated_user_cannot_view_avatar()
    {
        $user = User::factory()->create();

        // Upload an avatar
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $user->addMedia($file)->toMediaCollection('avatar');

        $response = $this->get(route('avatar.show', $user));

        $response->assertRedirect(route('login'));
    }

    public function test_returns_404_when_user_has_no_avatar()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('avatar.show', $user));

        $response->assertNotFound();
    }

    public function test_user_model_includes_avatar_url_in_json()
    {
        $user = User::factory()->create();

        // Upload an avatar
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $user->addMedia($file)->toMediaCollection('avatar');

        $userData = $user->fresh()->toArray();

        $this->assertArrayHasKey('avatar', $userData);
        $this->assertStringContainsString('/avatars/'.$user->id, $userData['avatar']);
    }

    public function test_user_model_returns_null_avatar_when_no_media_exists()
    {
        $user = User::factory()->create();

        $userData = $user->toArray();

        $this->assertArrayHasKey('avatar', $userData);
        $this->assertNull($userData['avatar']);
    }

    public function test_avatar_has_proper_cache_headers()
    {
        $user = User::factory()->create();

        // Upload an avatar
        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $user->addMedia($file)->toMediaCollection('avatar');

        $response = $this
            ->actingAs($user)
            ->get(route('avatar.show', $user));

        $response->assertOk();
        $response->assertHeader('Cache-Control', 'public, max-age=604800');
    }
}
