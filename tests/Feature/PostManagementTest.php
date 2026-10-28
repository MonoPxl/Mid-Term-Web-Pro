<?php

use App\Enums\PostType;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function postPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'New campus announcement',
        'category_id' => Category::factory()->create()->id,
        'type' => 'announcement',
        'body' => 'This is the announcement body for ITS students.',
        'status' => 'draft',
    ], $overrides);
}

test('guests cannot access post creation or write routes', function () {
    $post = Post::factory()->create();

    $this->get(route('posts.create'))->assertRedirect(route('login'));
    $this->post(route('posts.store'), postPayload())->assertRedirect(route('login'));
    $this->get(route('posts.edit', $post))->assertRedirect(route('login'));
    $this->put(route('posts.update', $post), postPayload())->assertRedirect(route('login'));
    $this->delete(route('posts.destroy', $post))->assertRedirect(route('login'));
});

test('authenticated users can save a new post as a draft', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Draft announcement for ITS',
        'category_id' => $category->id,
        'type' => 'announcement',
        'body' => 'This draft is still being prepared.',
        'status' => 'draft',
    ]);

    $post = Post::where('title', 'Draft announcement for ITS')->firstOrFail();

    $response->assertRedirect(route('dashboard'));
    expect($post->user_id)->toBe($user->id)
        ->and($post->status->value)->toBe('draft')
        ->and($post->published_at)->toBeNull()
        ->and($post->slug)->toBe('draft-announcement-for-its');
});

test('authenticated users can publish an event', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'ITS robotics open house',
        'category_id' => $category->id,
        'type' => 'event',
        'body' => 'Meet the robotics team and see their latest projects.',
        'status' => 'published',
        'event_starts_at' => '2026-11-20 09:00:00',
        'event_ends_at' => '2026-11-20 12:00:00',
        'event_location' => 'Gedung Robotika ITS',
    ]);

    $post = Post::where('title', 'ITS robotics open house')->firstOrFail();

    $response->assertRedirect(route('posts.show', $post));
    expect($post->type->value)->toBe('event')
        ->and($post->status->value)->toBe('published')
        ->and($post->event_location)->toBe('Gedung Robotika ITS')
        ->and($post->published_at)->not->toBeNull();
});

test('post creation validates required fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [])
        ->assertSessionHasErrors(['title', 'category_id', 'type', 'body', 'status']);
});

test('event posts require valid event details', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), postPayload(['type' => 'event']))
        ->assertSessionHasErrors(['event_starts_at', 'event_ends_at', 'event_location']);

    $this->actingAs($user)
        ->post(route('posts.store'), postPayload([
            'type' => 'event',
            'event_starts_at' => '2026-11-20 12:00:00',
            'event_ends_at' => '2026-11-20 09:00:00',
            'event_location' => 'Auditorium ITS',
        ]))
        ->assertSessionHasErrors('event_ends_at');
});

test('event fields are ignored for announcements', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('posts.store'), postPayload([
        'title' => 'Plain announcement',
        'event_starts_at' => '2026-11-20 09:00:00',
        'event_ends_at' => '2026-11-20 12:00:00',
        'event_location' => 'Auditorium ITS',
    ]));

    $post = Post::where('title', 'Plain announcement')->firstOrFail();

    expect($post->event_starts_at)->toBeNull()
        ->and($post->event_ends_at)->toBeNull()
        ->and($post->event_location)->toBeNull();
});

test('authenticated users can add a cover image to a post', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'Post with a cover image',
        'category_id' => $category->id,
        'type' => 'announcement',
        'body' => 'This post has a custom cover image.',
        'status' => 'published',
        'cover_image' => UploadedFile::fake()->image('cover.jpg', 1200, 630),
    ])->assertRedirect();

    $post = Post::where('title', 'Post with a cover image')->firstOrFail();

    expect($post->cover_image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($post->cover_image_path);
});

test('authors can remove a cover image from a post', function () {
    Storage::fake('public');
    Storage::disk('public')->put('posts/covers/old-cover.jpg', 'fake-image');

    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->create([
        'type' => PostType::Announcement,
        'cover_image_path' => 'posts/covers/old-cover.jpg',
    ]);

    $this->actingAs($author)->put(route('posts.update', $post), [
        'title' => $post->title,
        'category_id' => $post->category_id,
        'type' => $post->type->value,
        'body' => $post->body,
        'status' => 'published',
        'remove_cover_image' => '1',
    ])->assertRedirect(route('posts.show', $post));

    expect($post->fresh()->cover_image_path)->toBeNull();
    Storage::disk('public')->assertMissing('posts/covers/old-cover.jpg');
});

test('post cover images must be valid image files', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user)->post(route('posts.store'), postPayload([
        'cover_image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ]))->assertSessionHasErrors('cover_image');
});

test('deleting a post removes its cover image', function () {
    Storage::fake('public');
    Storage::disk('public')->put('posts/covers/delete-me.jpg', 'fake-image');

    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->create([
        'cover_image_path' => 'posts/covers/delete-me.jpg',
    ]);

    $this->actingAs($author)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('dashboard'));

    Storage::disk('public')->assertMissing('posts/covers/delete-me.jpg');
});

test('authors can edit their own posts', function () {
    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->draft()->create([
        'title' => 'Original draft title',
    ]);
    $category = Category::factory()->create();

    $this->actingAs($author)
        ->get(route('posts.edit', $post))
        ->assertOk()
        ->assertSee('Original draft title');

    $this->actingAs($author)
        ->put(route('posts.update', $post), [
            'title' => 'Updated announcement title',
            'category_id' => $category->id,
            'type' => 'announcement',
            'body' => 'The updated body of the announcement.',
            'status' => 'published',
        ])
        ->assertRedirect(route('posts.show', $post));

    $post->refresh();

    expect($post->title)->toBe('Updated announcement title')
        ->and($post->category_id)->toBe($category->id)
        ->and($post->status->value)->toBe('published')
        ->and($post->published_at)->not->toBeNull();
});

test('users cannot edit another users post', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($owner, 'author')->create([
        'title' => 'Owner post',
    ]);

    $this->actingAs($otherUser)
        ->get(route('posts.edit', $post))
        ->assertForbidden();

    $this->actingAs($otherUser)
        ->put(route('posts.update', $post), postPayload(['title' => 'Hijacked title']))
        ->assertForbidden();

    expect($post->fresh()->title)->toBe('Owner post');
});

test('users cannot delete another users post', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($owner, 'author')->create();

    $this->actingAs($otherUser)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();

    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});

test('authors can delete their own posts', function () {
    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->create();

    $this->actingAs($author)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('post owners can preview their own drafts but other users cannot', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->for($owner, 'author')->draft()->create([
        'title' => 'Owner draft preview',
    ]);

    $this->actingAs($owner)
        ->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('Owner draft preview')
        ->assertSee('Only you can see this preview');

    $this->actingAs($otherUser)
        ->get(route('posts.show', $post))
        ->assertNotFound();
});
