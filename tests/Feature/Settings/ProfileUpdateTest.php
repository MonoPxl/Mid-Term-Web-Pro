<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('profile.edit'))->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('profile photo can be uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('photo', UploadedFile::fake()->image('avatar.jpg', 400, 400))
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar_path);
    expect($user->avatarUrl())->toContain('/storage/avatars/');
});

test('profile photo must be a supported image', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('photo', UploadedFile::fake()->image('avatar.gif', 100, 100))
        ->call('updateProfileInformation')
        ->assertHasErrors(['photo']);
});

test('profile photo can be removed', function () {
    Storage::fake('public');
    Storage::disk('public')->put('avatars/old-avatar.jpg', 'fake-image');

    $user = User::factory()->create(['avatar_path' => 'avatars/old-avatar.jpg']);

    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->call('removeAvatar')
        ->assertHasNoErrors();

    expect($user->refresh()->avatar_path)->toBeNull();
    Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
});

test('avatar file is deleted when the account is deleted', function () {
    Storage::fake('public');
    Storage::disk('public')->put('avatars/delete-me.jpg', 'fake-image');

    $user = User::factory()->create(['avatar_path' => 'avatars/delete-me.jpg']);

    $this->actingAs($user);

    Livewire::test('pages::settings.delete-user-modal')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing('avatars/delete-me.jpg');
});

test('post cover images are deleted when the account is deleted', function () {
    Storage::fake('public');
    Storage::disk('public')->put('posts/covers/delete-with-account.jpg', 'fake-image');

    $user = User::factory()->create();
    Post::factory()->for($user, 'author')->create([
        'cover_image_path' => 'posts/covers/delete-with-account.jpg',
    ]);

    $this->actingAs($user);

    Livewire::test('pages::settings.delete-user-modal')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing('posts/covers/delete-with-account.jpg');
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-modal')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-modal')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});
