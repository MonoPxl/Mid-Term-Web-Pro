<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionDemoSeeder extends Seeder
{
    /**
     * Seed public demo content without creating a usable demo login account.
     * All text is clearly labelled as sample content, not an official ITS announcement.
     */
    public function run(): void
    {
        $categories = [
            'akademik' => [
                'name' => 'Akademik',
                'description' => 'Academic announcements, schedules, and campus policy updates.',
            ],
            'kegiatan-kampus' => [
                'name' => 'Kegiatan Kampus',
                'description' => 'Events, seminars, and activities held around ITS.',
            ],
            'kompetisi' => [
                'name' => 'Kompetisi',
                'description' => 'Competitions and challenges for ITS students.',
            ],
            'organisasi' => [
                'name' => 'Organisasi',
                'description' => 'Student organizations, UKM, and recruitment information.',
            ],
            'beasiswa' => [
                'name' => 'Beasiswa',
                'description' => 'Scholarship opportunities and financial aid updates.',
            ],
            'karier' => [
                'name' => 'Karier',
                'description' => 'Internships, career fairs, and job opportunities.',
            ],
        ];

        foreach ($categories as $slug => $category) {
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ],
            );
        }

        // This account is only a content author. Its random password is never displayed or seeded as a known credential.
        $demoAuthor = User::firstOrCreate(
            ['email' => 'demo.publisher@myitsbroadcast.invalid'],
            [
                'name' => 'myITSBroadcast Demo Publisher',
                'password' => Hash::make(Str::random(80)),
            ],
        );

        $samples = [
            [
                'slug' => 'demo-student-innovation-showcase',
                'category' => 'kompetisi',
                'type' => PostType::Announcement,
                'title' => 'DEMO: Student Innovation Showcase',
                'body' => 'Sample content for website demonstration only. This is not an official ITS announcement. Replace this example with a verified campus announcement before using the site for real information.',
                'published_at' => now()->subHours(2),
            ],
            [
                'slug' => 'demo-web-development-workshop',
                'category' => 'kegiatan-kampus',
                'type' => PostType::Event,
                'title' => 'DEMO: Web Development Workshop',
                'body' => 'Sample event content for demonstration only. This is not an actual confirmed ITS event. Date, time, and location are illustrative placeholders.',
                'published_at' => now()->subHours(5),
                'event_starts_at' => now()->addDays(7)->setTime(10, 0),
                'event_ends_at' => now()->addDays(7)->setTime(12, 0),
                'event_location' => 'Demo location — confirm details before actual use.',
            ],
            [
                'slug' => 'demo-scholarship-information',
                'category' => 'beasiswa',
                'type' => PostType::Announcement,
                'title' => 'DEMO: Scholarship Information Board',
                'body' => 'Sample scholarship notice for demonstrating categories and search. This is not a real scholarship offer. Confirm eligibility and deadlines with an official provider before acting on any scholarship information.',
                'published_at' => now()->subDay(),
            ],
            [
                'slug' => 'demo-student-organization-introduction',
                'category' => 'organisasi',
                'type' => PostType::Event,
                'title' => 'DEMO: Student Organization Introduction',
                'body' => 'Sample event content for the myITSBroadcast demo. This is not an official ITS event; the date and location are illustrative.',
                'published_at' => now()->subDays(2),
                'event_starts_at' => now()->addDays(10)->setTime(13, 0),
                'event_ends_at' => now()->addDays(10)->setTime(15, 0),
                'event_location' => 'Demo venue — not a confirmed location.',
            ],
        ];

        foreach ($samples as $sample) {
            $category = Category::where('slug', $sample['category'])->firstOrFail();

            Post::firstOrCreate(
                ['slug' => $sample['slug']],
                [
                    'user_id' => $demoAuthor->id,
                    'category_id' => $category->id,
                    'type' => $sample['type'],
                    'status' => PostStatus::Published,
                    'title' => $sample['title'],
                    'body' => $sample['body'],
                    'published_at' => $sample['published_at'],
                    'event_starts_at' => $sample['event_starts_at'] ?? null,
                    'event_ends_at' => $sample['event_ends_at'] ?? null,
                    'event_location' => $sample['event_location'] ?? null,
                ],
            );
        }
    }
}
