<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(PostType::cases());
        $startsAt = $type === PostType::Event
            ? now()->addDays(fake()->numberBetween(3, 45))
            : null;

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'type' => $type,
            'status' => PostStatus::Published,
            'title' => fake()->randomElement([
                'Open Recruitment UKM Robotika ITS',
                'Seminar Nasional Teknologi Maritim',
                'Pendaftaran Beasiswa Prestasi Semester Ganjil',
                'Lomba Desain UI/UX Tingkat Nasional',
                'Info Magang di PT PAL Indonesia',
                'Workshop Kecerdasan Buatan untuk Mahasiswa',
                'Sosialisasi Program Pertukaran Mahasiswa',
                'Kompetisi Hackathon Smart Campus ITS',
            ]),
            'body' => fake()->paragraphs(3, true),
            'is_pinned' => false,
            'published_at' => now()->subDays(fake()->numberBetween(1, 21)),
            'event_starts_at' => $startsAt,
            'event_ends_at' => $startsAt?->addHours(3),
            'event_location' => $type === PostType::Event
                ? fake()->randomElement([
                    'Gedung TC ITS',
                    'Auditorium Research Center ITS',
                    'Gedung Robotika ITS',
                    'Aula Fakultas Teknologi Industri',
                ])
                : null,
        ];
    }

    /**
     * Indicate that the post is an event.
     */
    public function event(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PostType::Event,
            'event_starts_at' => now()->addDays(10),
            'event_ends_at' => now()->addDays(10)->addHours(3),
            'event_location' => 'Auditorium Research Center ITS',
        ]);
    }

    /**
     * Indicate that the post is a plain announcement.
     */
    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PostType::Announcement,
            'event_starts_at' => null,
            'event_ends_at' => null,
            'event_location' => null,
        ]);
    }

    /**
     * Indicate that the post is pinned to the top of the feed.
     */
    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the post has been archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Archived,
        ]);
    }
}
