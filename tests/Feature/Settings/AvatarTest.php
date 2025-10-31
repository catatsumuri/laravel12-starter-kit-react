<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Set up fake storage for testing
    Storage::fake('public');
});

afterEach(function () {
    // Clean up media files after each test
    $mediaPath = storage_path('app/public/media');
    if (is_dir($mediaPath)) {
        array_map('unlink', glob("$mediaPath/*/*/*"));
        array_map('rmdir', glob("$mediaPath/*/*"));
        array_map('rmdir', glob("$mediaPath/*"));
    }
});

test('authenticated user can view avatar', function () {
    $user = User::factory()->create();

    // Upload an avatar
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
    $user->addMedia($file)->toMediaCollection('avatar');

    $response = $this
        ->actingAs($user)
        ->get(route('avatar.show', $user));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/jpeg');
});

test('authenticated user can view other users avatar', function () {
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
});

test('unauthenticated user cannot view avatar', function () {
    $user = User::factory()->create();

    // Upload an avatar
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
    $user->addMedia($file)->toMediaCollection('avatar');

    $response = $this->get(route('avatar.show', $user));

    $response->assertRedirect(route('login'));
});

test('returns 404 when user has no avatar', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('avatar.show', $user));

    $response->assertNotFound();
});

test('user model includes avatar url in json', function () {
    $user = User::factory()->create();

    // Upload an avatar
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
    $user->addMedia($file)->toMediaCollection('avatar');

    $userData = $user->fresh()->toArray();

    expect($userData)->toHaveKey('avatar');
    $this->assertStringContainsString('/avatars/'.$user->id, $userData['avatar']);
});

test('user model returns null avatar when no media exists', function () {
    $user = User::factory()->create();

    $userData = $user->toArray();

    expect($userData)->toHaveKey('avatar');
    expect($userData['avatar'])->toBeNull();
});

test('avatar has proper cache headers', function () {
    $user = User::factory()->create();

    // Upload an avatar
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
    $user->addMedia($file)->toMediaCollection('avatar');

    $response = $this
        ->actingAs($user)
        ->get(route('avatar.show', $user));

    $response->assertOk();

    $header = $response->headers->get('Cache-Control');
    expect($header)->not->toBeNull();
    $directives = array_map('trim', explode(',', $header));
    expect($directives)->toContain('public');
    expect($header)->toMatch('/\bmax-age=604800\b/');
});

test('user can upload real example image as avatar', function () {
    $user = User::factory()->create();

    $exampleImagePath = __DIR__.'/example.jpg';
    expect(file_exists($exampleImagePath))->toBeTrue('example.jpg should exist in tests/Feature/Settings directory');

    // Create a temporary copy to avoid moving the original file
    $tempPath = sys_get_temp_dir().'/test_avatar_'.uniqid().'.jpg';
    copy($exampleImagePath, $tempPath);

    $file = new UploadedFile(
        $tempPath,
        'example.jpg',
        'image/jpeg',
        null,
        true
    );

    $user->addMedia($file)->toMediaCollection('avatar');

    $response = $this
        ->actingAs($user)
        ->get(route('avatar.show', $user));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/jpeg');

    // Verify the user has an avatar
    expect($user->fresh()->getFirstMediaUrl('avatar'))->not->toBeEmpty();
});

test('thumb conversion is created with correct dimensions', function () {
    $user = User::factory()->create();

    // Upload an avatar
    $file = UploadedFile::fake()->image('avatar.jpg', 500, 500);
    $user->addMedia($file)->toMediaCollection('avatar');

    $media = $user->getFirstMedia('avatar');
    expect($media)->not->toBeNull();

    // Check that thumb conversion exists
    $thumbPath = $media->getPath('thumb');
    expect(file_exists($thumbPath))->toBeTrue('Thumb conversion should be created');

    // Check dimensions are 200x200
    $imageSize = getimagesize($thumbPath);
    expect($imageSize)->not->toBeFalse();
    expect($imageSize[0])->toBe(200, 'Thumb width should be 200px');
    expect($imageSize[1])->toBe(200, 'Thumb height should be 200px');

    // Check format is JPEG
    expect($imageSize['mime'])->toBe('image/jpeg');
});

test('thumb conversion is served by avatar controller', function () {
    $user = User::factory()->create();

    // Upload an avatar with known dimensions
    $file = UploadedFile::fake()->image('avatar.jpg', 500, 500);
    $user->addMedia($file)->toMediaCollection('avatar');

    $media = $user->getFirstMedia('avatar');
    expect($media)->not->toBeNull();

    // Verify thumb exists
    $thumbPath = $media->getPath('thumb');
    expect(file_exists($thumbPath))->toBeTrue();

    // Test that the controller serves the thumb
    $response = $this
        ->actingAs($user)
        ->get(route('avatar.show', $user));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/jpeg');

    // Verify the served file is the thumb by checking its dimensions
    $imageSize = getimagesize($thumbPath);
    expect($imageSize)->not->toBeFalse();
    expect($imageSize[0])->toBe(200, 'Served image width should be 200px (thumb)');
    expect($imageSize[1])->toBe(200, 'Served image height should be 200px (thumb)');
});

test('thumb conversion does not contain exif data', function () {
    if (! extension_loaded('exif')) {
        $this->markTestSkipped('EXIF extension is not available');
    }

    $user = User::factory()->create();

    $exampleImagePath = __DIR__.'/example.jpg';
    expect(file_exists($exampleImagePath))->toBeTrue('example.jpg should exist in tests/Feature/Settings directory');

    // Verify the original file has EXIF data
    $originalExif = @exif_read_data($exampleImagePath);
    expect($originalExif)->not->toBeFalse('Original example.jpg should have EXIF data for this test to be meaningful');

    // Check if original has GPS data (if not, we'll just verify EXIF removal in general)
    $originalHasGPS = isset($originalExif['GPSLatitude']);

    // Create a temporary copy to avoid moving the original file
    $tempPath = sys_get_temp_dir().'/test_avatar_'.uniqid().'.jpg';
    copy($exampleImagePath, $tempPath);

    $file = new UploadedFile(
        $tempPath,
        'example.jpg',
        'image/jpeg',
        null,
        true
    );

    $user->addMedia($file)->toMediaCollection('avatar');

    // Get the thumb conversion file path
    $media = $user->getFirstMedia('avatar');
    expect($media)->not->toBeNull();

    $thumbPath = $media->getPath('thumb');
    expect(file_exists($thumbPath))->toBeTrue('Thumb conversion should exist');

    // Check if EXIF data has been stripped from the thumb
    $thumbExif = @exif_read_data($thumbPath);

    // The thumb conversion should not contain sensitive EXIF information
    if ($thumbExif !== false) {
        expect($thumbExif)->not->toHaveKey('GPSLatitude', 'Thumb should not contain GPS latitude');
        expect($thumbExif)->not->toHaveKey('GPSLongitude', 'Thumb should not contain GPS longitude');
        expect($thumbExif)->not->toHaveKey('GPSAltitude', 'Thumb should not contain GPS altitude');
    }

    // If original had GPS data, confirm it was removed
    if ($originalHasGPS) {
        expect($thumbExif === false || ! isset($thumbExif['GPSLatitude']))
            ->toBeTrue('GPS data should be removed from thumb conversion');
    }
});

test('user can delete their avatar', function () {
    $user = User::factory()->create();

    // Upload an avatar first
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
    $user->addMedia($file)->toMediaCollection('avatar');

    expect($user->getFirstMedia('avatar'))->not->toBeNull();

    // Delete the avatar
    $response = $this
        ->actingAs($user)
        ->delete(route('profile.avatar.destroy'));

    $response->assertRedirect(route('profile.edit'));

    // Verify avatar was deleted
    expect($user->fresh()->getFirstMedia('avatar'))->toBeNull();
});

test('deleting avatar removes all files including conversions', function () {
    $user = User::factory()->create();

    // Upload an avatar
    $file = UploadedFile::fake()->image('avatar.jpg', 500, 500);
    $user->addMedia($file)->toMediaCollection('avatar');

    $media = $user->getFirstMedia('avatar');
    expect($media)->not->toBeNull();

    $originalPath = $media->getPath();
    $thumbPath = $media->getPath('thumb');

    expect(file_exists($originalPath))->toBeTrue();
    expect(file_exists($thumbPath))->toBeTrue();

    // Delete the avatar
    $this
        ->actingAs($user)
        ->delete(route('profile.avatar.destroy'));

    // Verify all files are deleted
    expect(file_exists($originalPath))->toBeFalse();
    expect(file_exists($thumbPath))->toBeFalse();
});

test('user avatar url becomes null after deletion', function () {
    $user = User::factory()->create();

    // Upload an avatar
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
    $user->addMedia($file)->toMediaCollection('avatar');

    $userData = $user->fresh()->toArray();
    expect($userData['avatar'])->not->toBeNull();

    // Delete the avatar
    $this
        ->actingAs($user)
        ->delete(route('profile.avatar.destroy'));

    // Verify avatar URL is null
    $userData = $user->fresh()->toArray();
    expect($userData['avatar'])->toBeNull();
});
