<?php

use App\Enums\PostStatus;
use App\Livewire\BookmarkButton;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('guests cannot bookmark posts', function () {
    $post = Post::factory()->create();

    $this->post(route('bookmarks.store', $post))->assertRedirect(route('login'));
    $this->delete(route('bookmarks.destroy', $post))->assertRedirect(route('login'));
});

test('authenticated users can bookmark a published post once', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('bookmarks.store', $post))
        ->assertRedirect();

    $this->actingAs($user)
        ->post(route('bookmarks.store', $post))
        ->assertRedirect();

    $this->assertDatabaseCount('bookmarks', 1);
    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('users can remove their own bookmark', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)->post(route('bookmarks.store', $post));

    $this->actingAs($user)
        ->delete(route('bookmarks.destroy', $post))
        ->assertRedirect();

    $this->assertDatabaseMissing('bookmarks', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('removing a bookmark does not affect another users bookmark', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($firstUser)->post(route('bookmarks.store', $post));
    $this->actingAs($secondUser)->post(route('bookmarks.store', $post));

    $this->actingAs($secondUser)->delete(route('bookmarks.destroy', $post));

    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $firstUser->id,
        'post_id' => $post->id,
    ]);

    $this->assertDatabaseMissing('bookmarks', [
        'user_id' => $secondUser->id,
        'post_id' => $post->id,
    ]);
});

test('the feed shows the quick bookmark state for the authenticated user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Save for later');

    $this->actingAs($user)->post(route('bookmarks.store', $post));

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Remove bookmark');
});

test('the quick bookmark button toggles the bookmark without a full page reload', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user);

    Livewire::test(BookmarkButton::class, [
        'post' => $post,
        'bookmarked' => false,
    ])
        ->assertSet('bookmarked', false)
        ->call('toggle')
        ->assertSet('bookmarked', true)
        ->assertDispatched('bookmark-toggled');

    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    Livewire::test(BookmarkButton::class, [
        'post' => $post,
        'bookmarked' => true,
    ])
        ->call('toggle')
        ->assertSet('bookmarked', false);

    $this->assertDatabaseMissing('bookmarks', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

test('draft posts cannot be bookmarked', function () {
    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->draft()->create();

    $this->actingAs($author)
        ->post(route('bookmarks.store', $post))
        ->assertNotFound();

    $this->assertDatabaseMissing('bookmarks', [
        'user_id' => $author->id,
        'post_id' => $post->id,
    ]);
});

test('the dashboard lists only published bookmarks for the authenticated user', function () {
    $author = User::factory()->create();
    $reader = User::factory()->create();
    $published = Post::factory()->for($author, 'author')->create([
        'title' => 'Bookmarked published post',
    ]);
    $unpublished = Post::factory()->for($author, 'author')->create([
        'title' => 'Bookmarked post that becomes a draft',
    ]);

    $this->actingAs($reader)->post(route('bookmarks.store', $published));
    $this->actingAs($reader)->post(route('bookmarks.store', $unpublished));

    $unpublished->update([
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    $this->actingAs($reader)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Bookmarked published post')
        ->assertDontSee('Bookmarked post that becomes a draft');

    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $reader->id,
        'post_id' => $unpublished->id,
    ]);
});
