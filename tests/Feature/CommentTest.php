<?php

use App\Models\Post;
use App\Models\User;

test('guests cannot comment or delete comments', function () {
    $post = Post::factory()->create();
    $comment = $post->comments()->create([
        'user_id' => User::factory()->create()->id,
        'body' => 'Existing comment',
    ]);

    $this->post(route('comments.store', $post), ['body' => 'Guest comment'])
        ->assertRedirect(route('login'));

    $this->delete(route('comments.destroy', $comment))
        ->assertRedirect(route('login'));
});

test('authenticated users can comment on published posts', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('comments.store', $post), ['body' => 'Useful information, thank you!'])
        ->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'body' => 'Useful information, thank you!',
    ]);

    $this->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('Useful information, thank you!')
        ->assertSee($user->name);
});

test('authenticated users can reply to a comment', function () {
    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->create();
    $comment = $post->comments()->create([
        'user_id' => $author->id,
        'body' => 'Original comment',
    ]);

    $this->actingAs($author)
        ->post(route('comments.store', $post), [
            'body' => 'A threaded reply',
            'parent_id' => $comment->id,
        ])
        ->assertRedirect(route('posts.show', $post).'#comments');

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $author->id,
        'parent_id' => $comment->id,
        'body' => 'A threaded reply',
    ]);

    $this->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('Original comment')
        ->assertSee('A threaded reply')
        ->assertSee('data-testid="author-badge"', false);
});

test('replies to replies are flattened into the original thread', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = $post->comments()->create([
        'user_id' => $user->id,
        'body' => 'Original comment',
    ]);
    $reply = $post->comments()->create([
        'user_id' => $user->id,
        'parent_id' => $comment->id,
        'body' => 'First reply',
    ]);

    $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'body' => 'Reply to a reply',
            'parent_id' => $reply->id,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'body' => 'Reply to a reply',
        'parent_id' => $comment->id,
    ]);
});

test('a reply must belong to the same post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $otherComment = Post::factory()->create()->comments()->create([
        'user_id' => $user->id,
        'body' => 'Comment on another post',
    ]);

    $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'body' => 'Cross-post reply',
            'parent_id' => $otherComment->id,
        ])
        ->assertSessionHasErrors('parent_id');

    $this->assertDatabaseMissing('comments', ['body' => 'Cross-post reply']);
});

test('comment whitespace is trimmed', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('comments.store', $post), ['body' => "  \n  Trimmed comment  \n "])
        ->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'body' => 'Trimmed comment',
    ]);
});

test('deleting a comment also deletes its replies', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = $post->comments()->create([
        'user_id' => $user->id,
        'body' => 'Parent comment',
    ]);
    $reply = $post->comments()->create([
        'user_id' => $user->id,
        'parent_id' => $comment->id,
        'body' => 'Child reply',
    ]);

    $this->actingAs($user)
        ->delete(route('comments.destroy', $comment))
        ->assertRedirect();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    $this->assertDatabaseMissing('comments', ['id' => $reply->id]);
});

test('comment content is validated', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->post(route('comments.store', $post), ['body' => ''])
        ->assertSessionHasErrors('body');

    $this->actingAs($user)
        ->post(route('comments.store', $post), ['body' => str_repeat('a', 2001)])
        ->assertSessionHasErrors('body');
});

test('comments cannot be added to drafts', function () {
    $author = User::factory()->create();
    $post = Post::factory()->for($author, 'author')->draft()->create();

    $this->actingAs($author)
        ->post(route('comments.store', $post), ['body' => 'Comment on a draft'])
        ->assertNotFound();

    $this->assertDatabaseMissing('comments', [
        'post_id' => $post->id,
        'body' => 'Comment on a draft',
    ]);
});

test('users can delete their own comments', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = $post->comments()->create([
        'user_id' => $user->id,
        'body' => 'My own comment',
    ]);

    $this->actingAs($user)
        ->delete(route('comments.destroy', $comment))
        ->assertRedirect();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('users cannot delete another users comment', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $comment = Post::factory()->create()->comments()->create([
        'user_id' => $owner->id,
        'body' => 'Owned by another user',
    ]);

    $this->actingAs($otherUser)
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();

    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});
