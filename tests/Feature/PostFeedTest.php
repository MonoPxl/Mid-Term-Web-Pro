<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

test('the feed shows published posts and hides drafts and archived posts', function () {
    $author = User::factory()->create();
    $category = Category::factory()->create();

    Post::factory()->for($author, 'author')->for($category)->create([
        'title' => 'Published campus news',
    ]);

    Post::factory()->for($author, 'author')->for($category)->draft()->create([
        'title' => 'Unpublished draft',
    ]);

    Post::factory()->for($author, 'author')->for($category)->archived()->create([
        'title' => 'Old archived post',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Published campus news')
        ->assertDontSee('Unpublished draft')
        ->assertDontSee('Old archived post');
});

test('pinned data is not exposed as a feed feature', function () {
    $post = Post::factory()->pinned()->create(['title' => 'Pinned campus announcement']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Pinned campus announcement')
        ->assertDontSee('Pinned announcements');
});

test('a published post can be viewed by its slug', function () {
    $post = Post::factory()->create([
        'title' => 'Seminar Nasional Teknologi Maritim',
        'body' => 'A public announcement for ITS students.',
    ]);

    $this->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('Seminar Nasional Teknologi Maritim')
        ->assertSee('A public announcement for ITS students.');
});

test('event details are displayed on the post detail page', function () {
    $post = Post::factory()->event()->create([
        'title' => 'Campus technology seminar',
        'event_location' => 'Auditorium Research Center ITS',
    ]);

    $this->get(route('posts.show', $post))
        ->assertOk()
        ->assertSee('Campus technology seminar')
        ->assertSee('Event details')
        ->assertSee('Auditorium Research Center ITS');
});

test('draft posts are hidden from guests', function () {
    $post = Post::factory()->draft()->create(['title' => 'Private draft']);

    $this->get(route('posts.show', $post))->assertNotFound();
});

test('post slugs are generated and kept unique', function () {
    $first = Post::factory()->create(['title' => 'Open Recruitment Robotika ITS']);
    $second = Post::factory()->create(['title' => 'Open Recruitment Robotika ITS']);

    expect($first->slug)->toBe('open-recruitment-robotika-its')
        ->and($second->slug)->toBe('open-recruitment-robotika-its-2');
});

test('posts expose their author and related records', function () {
    $author = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->for($author, 'author')->for($category)->event()->create();

    $comment = Comment::factory()->for($post)->create();

    expect($post->author->is($author))->toBeTrue()
        ->and($post->category->is($category))->toBeTrue()
        ->and($post->comments->contains($comment))->toBeTrue()
        ->and($post->isEvent())->toBeTrue();
});
