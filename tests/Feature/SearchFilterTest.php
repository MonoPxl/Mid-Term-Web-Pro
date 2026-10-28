<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

test('posts can be searched by title and body content', function () {
    $author = User::factory()->create();

    Post::factory()->for($author, 'author')->create([
        'title' => 'Seminar on maritime technology',
        'body' => 'A public session about ships and ports.',
    ]);

    Post::factory()->for($author, 'author')->create([
        'title' => 'Scholarship announcement',
        'body' => 'Details about the maritime scholarship programme.',
    ]);

    Post::factory()->for($author, 'author')->draft()->create([
        'title' => 'Secret maritime draft',
        'body' => 'This draft must never appear in public search.',
    ]);

    $this->get(route('home', ['q' => 'maritime']))
        ->assertOk()
        ->assertSee('Seminar on maritime technology')
        ->assertSee('Scholarship announcement')
        ->assertDontSee('Secret maritime draft');
});

test('posts can be filtered by category', function () {
    $author = User::factory()->create();
    $academic = Category::factory()->create(['name' => 'Academic', 'slug' => 'academic']);
    $career = Category::factory()->create(['name' => 'Career', 'slug' => 'career']);

    Post::factory()->for($author, 'author')->for($academic)->create(['title' => 'Academic calendar update']);
    Post::factory()->for($author, 'author')->for($career)->create(['title' => 'Internship opportunity']);

    $this->get(route('home', ['category' => $academic->slug]))
        ->assertOk()
        ->assertSee('Academic calendar update')
        ->assertDontSee('Internship opportunity');
});

test('posts can be filtered by post type', function () {
    $author = User::factory()->create();

    Post::factory()->for($author, 'author')->announcement()->create(['title' => 'Announcement only']);
    Post::factory()->for($author, 'author')->event()->create(['title' => 'Event only']);

    $this->get(route('home', ['type' => 'event']))
        ->assertOk()
        ->assertSee('Event only')
        ->assertDontSee('Announcement only');
});

test('search and filters can be combined', function () {
    $author = User::factory()->create();
    $academic = Category::factory()->create(['name' => 'Academic', 'slug' => 'academic']);
    $career = Category::factory()->create(['name' => 'Career', 'slug' => 'career']);

    Post::factory()->for($author, 'author')->for($academic)->event()->create([
        'title' => 'Campus seminar on robotics',
    ]);

    Post::factory()->for($author, 'author')->for($career)->event()->create([
        'title' => 'Campus seminar on careers',
    ]);

    $this->get(route('home', [
        'q' => 'robotics',
        'category' => $academic->slug,
        'type' => 'event',
    ]))
        ->assertOk()
        ->assertSee('Campus seminar on robotics')
        ->assertDontSee('Campus seminar on careers');
});

test('pagination preserves active search and filter parameters', function () {
    $author = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Events', 'slug' => 'events']);

    foreach (range(1, 10) as $number) {
        Post::factory()->for($author, 'author')->for($category)->event()->create([
            'title' => "Campus event {$number}",
        ]);
    }

    $response = $this->get(route('home', [
        'q' => 'Campus',
        'category' => 'events',
        'type' => 'event',
    ]));

    $response->assertOk();

    $posts = $response->viewData('posts');

    expect($posts->total())->toBe(10)
        ->and($posts->lastPage())->toBe(2)
        ->and($posts->url(2))->toContain('q=Campus')
        ->and($posts->url(2))->toContain('category=events')
        ->and($posts->url(2))->toContain('type=event');
});

test('an empty search result shows a helpful message', function () {
    $this->get(route('home', ['q' => 'nothing-matches-this']))
        ->assertOk()
        ->assertSee('No published posts match your search.');
});
