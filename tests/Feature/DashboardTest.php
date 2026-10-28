<?php

use App\Models\Post;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the dashboard shows only the authenticated users posts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Post::factory()->for($user, 'author')->create(['title' => 'My published post']);
    Post::factory()->for($user, 'author')->draft()->create(['title' => 'My private draft']);
    Post::factory()->for($otherUser, 'author')->create(['title' => 'Another users post']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('My published post')
        ->assertSee('My private draft')
        ->assertDontSee('Another users post');
});

test('the dashboard shows user specific bookmark information', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $bookmarked = Post::factory()->create(['title' => 'Saved by me']);
    $otherBookmarked = Post::factory()->create(['title' => 'Saved by another user']);

    $user->bookmarks()->create(['post_id' => $bookmarked->id]);
    $otherUser->bookmarks()->create(['post_id' => $otherBookmarked->id]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Saved by me')
        ->assertDontSee('Saved by another user');
});
