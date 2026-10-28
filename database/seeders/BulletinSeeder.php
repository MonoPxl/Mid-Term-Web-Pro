<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\EventRegistration;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class BulletinSeeder extends Seeder
{
    /**
     * Seed the bulletin board with realistic ITS campus content.
     */
    public function run(): void
    {
        $categories = $this->seedCategories();
        $users = $this->seedUsers();

        $posts = $this->seedPosts($categories, $users);
        $this->seedComments($posts, $users);
        $this->seedBookmarks($posts, $users);
        $this->seedEventRegistrations($posts, $users);
    }

    /**
     * Create the campus categories used by the feed.
     *
     * @return array<string, Category>
     */
    protected function seedCategories(): array
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

        $seeded = [];

        foreach ($categories as $slug => $category) {
            $seeded[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $category['name'], 'description' => $category['description']],
            );
        }

        return $seeded;
    }

    /**
     * Create demo accounts for students and a moderator.
     *
     * @return array<string, User>
     */
    protected function seedUsers(): array
    {
        $moderator = User::firstOrCreate(
            ['email' => 'moderator@its.ac.id'],
            ['name' => 'Rina Kusuma', 'password' => 'password'],
        );

        $moderator->forceFill([
            'is_moderator' => true,
            'email_verified_at' => $moderator->email_verified_at ?? now(),
        ])->save();

        $students = [
            'student' => ['name' => 'Aditya Pratama', 'email' => 'student@its.ac.id'],
            'salsabila' => ['name' => 'Salsabila Putri', 'email' => 'salsabila@student.its.ac.id'],
            'bagas' => ['name' => 'Bagas Wirawan', 'email' => 'bagas@student.its.ac.id'],
            'nadia' => ['name' => 'Nadia Rahma', 'email' => 'nadia@student.its.ac.id'],
        ];

        $seeded = ['moderator' => $moderator];

        foreach ($students as $key => $student) {
            $seeded[$key] = User::firstOrCreate(
                ['email' => $student['email']],
                ['name' => $student['name'], 'password' => 'password'],
            );

            $seeded[$key]->forceFill([
                'email_verified_at' => $seeded[$key]->email_verified_at ?? now(),
            ])->save();
        }

        return $seeded;
    }

    /**
     * Create the demonstration posts.
     *
     * @param  array<string, Category>  $categories
     * @param  array<string, User>  $users
     * @return array<string, Post>
     */
    protected function seedPosts(array $categories, array $users): array
    {
        $posts = [
            'krs' => [
                'author' => 'moderator',
                'category' => 'akademik',
                'type' => PostType::Announcement,
                'status' => PostStatus::Published,
                'title' => 'Pembukaan Pengisian KRS Semester Ganjil 2026/2027',
                'body' => "Pengisian Kartu Rencana Studi (KRS) semester ganjil 2026/2027 dibuka mulai 3 November 2026 pukul 08.00 WIB melalui portal akademik ITS.\n\nMahasiswa diharapkan menyelesaikan pengisian KRS sebelum 12 November 2026. Pastikan tidak ada mata kuliah yang bentrok dan konsultasikan rencana studi dengan dosen wali masing-masing.\n\nBagi mahasiswa yang mengalami kendala teknis, silakan menghubungi helpdesk akademik melalui layanan tiket di portal ITS.",
                'is_pinned' => true,
                'published_at' => now()->subDays(2),
            ],
            'open-recruitment-robotika' => [
                'author' => 'moderator',
                'category' => 'organisasi',
                'type' => PostType::Announcement,
                'status' => PostStatus::Published,
                'title' => 'Open Recruitment UKM Robotika ITS 2026',
                'body' => "UKM Robotika ITS membuka pendaftaran anggota baru untuk divisi mekanik, elektronik, dan pemrograman. Pendaftaran dibuka untuk seluruh mahasiswa ITS angkatan 2024 dan 2025.\n\nTidak diperlukan pengalaman sebelumnya. Setiap pendaftar akan mengikuti sesi pengenalan dan pelatihan dasar sebelum tahap seleksi.\n\nBatas pendaftaran adalah 20 November 2026. Informasi lengkap dapat dilihat di akun Instagram resmi UKM Robotika ITS.",
                'is_pinned' => true,
                'published_at' => now()->subDays(3),
            ],
            'seminar-ai-maritime' => [
                'author' => 'salsabila',
                'category' => 'kegiatan-kampus',
                'type' => PostType::Event,
                'status' => PostStatus::Published,
                'title' => 'Seminar Nasional: AI for Sustainable Maritime Industry',
                'body' => "Departemen Teknik Kelautan bekerja sama dengan ITS Maritime Center mengadakan seminar nasional tentang pemanfaatan kecerdasan buatan untuk industri maritim yang berkelanjutan.\n\nPembicara berasal dari ITS, Kementerian Kelautan dan Perikanan, serta industri galangan kapal. Seminar terbuka untuk seluruh mahasiswa dan praktisi.\n\nPeserta yang hadir akan mendapatkan sertifikat elektronik. Daftarkan diri sebelum kuota terpenuhi.",
                'published_at' => now()->subDays(5),
                'event_starts_at' => now()->addDays(10)->setTime(9, 0),
                'event_ends_at' => now()->addDays(10)->setTime(15, 0),
                'event_location' => 'Auditorium Research Center ITS',
            ],
            'beasiswa-unggulan' => [
                'author' => 'moderator',
                'category' => 'beasiswa',
                'type' => PostType::Announcement,
                'status' => PostStatus::Published,
                'title' => 'Beasiswa Unggulan ITS untuk Mahasiswa Berprestasi',
                'body' => "Direktorat Kemahasiswaan ITS membuka pendaftaran Beasiswa Unggulan untuk mahasiswa semester 3 hingga semester 7 dengan prestasi akademik dan non-akademik.\n\nDokumen yang perlu disiapkan meliputi transkrip nilai, sertifikat prestasi, surat rekomendasi dosen, dan esai motivasi.\n\nPendaftaran ditutup pada 30 November 2026. Seleksi administrasi dan wawancara akan dilaksanakan pada awal Desember 2026.",
                'published_at' => now()->subDay(),
            ],
            'lomba-poster-lingkungan' => [
                'author' => 'nadia',
                'category' => 'kompetisi',
                'type' => PostType::Event,
                'status' => PostStatus::Published,
                'title' => 'Lomba Desain Poster Hari Lingkungan Hidup',
                'body' => "Himpunan Mahasiswa Teknik Lingkungan ITS mengadakan lomba desain poster bertema \"Kampus Hijau untuk Masa Depan\". Lomba terbuka untuk mahasiswa aktif dari seluruh perguruan tinggi di Indonesia.\n\nKarya dikirim dalam format PDF dan PNG dengan resolusi minimal 300 dpi. Penilaian mempertimbangkan pesan, kreativitas, dan kualitas visual.\n\nPengumpulan karya paling lambat 18 November 2026 pukul 23.59 WIB.",
                'published_at' => now()->subDays(6),
                'event_starts_at' => now()->addDays(14)->setTime(13, 0),
                'event_ends_at' => now()->addDays(14)->setTime(16, 0),
                'event_location' => 'Gedung Arsitektur ITS',
            ],
            'workshop-git' => [
                'author' => 'student',
                'category' => 'akademik',
                'type' => PostType::Event,
                'status' => PostStatus::Published,
                'title' => 'Workshop Git dan GitHub untuk Project Kuliah',
                'body' => "Laboratorium Pemrograman ITS mengadakan workshop praktik penggunaan Git dan GitHub untuk mahasiswa. Materi mencakup dasar version control, kolaborasi tim, serta pengelolaan repository tugas besar.\n\nPeserta wajib membawa laptop dan sudah memasang Git. Workshop dibatasi 40 peserta agar sesi praktik berjalan efektif.\n\nAcara akan dilaksanakan di Lab Komputer TC lantai 2.",
                'published_at' => now()->subDays(8),
                'event_starts_at' => now()->addDays(7)->setTime(15, 30),
                'event_ends_at' => now()->addDays(7)->setTime(18, 0),
                'event_location' => 'Lab Komputer TC ITS',
            ],
            'magang-pal' => [
                'author' => 'bagas',
                'category' => 'karier',
                'type' => PostType::Announcement,
                'status' => PostStatus::Published,
                'title' => 'Info Magang PT PAL Indonesia Batch 2',
                'body' => "PT PAL Indonesia membuka program magang batch 2 untuk mahasiswa tingkat akhir jurusan teknik mesin, teknik kelautan, teknik elektro, dan informatika.\n\nProgram berlangsung selama enam bulan dengan kesempatan terlibat langsung dalam proyek perkapalan. Pelamar diharapkan memiliki IPK minimal 3,00.\n\nBerkas lamaran dikirim melalui portal karier PT PAL paling lambat 25 November 2026.",
                'published_at' => now()->subDays(4),
            ],
            'its-cup-futsal' => [
                'author' => 'bagas',
                'category' => 'kegiatan-kampus',
                'type' => PostType::Event,
                'status' => PostStatus::Published,
                'title' => 'Turnamen Futsal Antardepartemen ITS Cup 2026',
                'body' => "Badan Eksekutif Mahasiswa ITS mengadakan ITS Cup 2026 cabang futsal. Setiap departemen dapat mengirimkan satu tim yang terdiri dari maksimal 12 pemain.\n\nPendaftaran tim dibuka sampai 15 November 2026. Technical meeting akan dilaksanakan seminggu sebelum pertandingan pertama.\n\nPertandingan berlangsung di Lapangan Futsal ITS setiap akhir pekan.",
                'published_at' => now()->subDays(9),
                'event_starts_at' => now()->addDays(20)->setTime(8, 0),
                'event_ends_at' => now()->addDays(20)->setTime(17, 0),
                'event_location' => 'Lapangan Futsal ITS',
            ],
            'student-exchange-utm' => [
                'author' => 'moderator',
                'category' => 'akademik',
                'type' => PostType::Announcement,
                'status' => PostStatus::Published,
                'title' => 'Pendaftaran Student Exchange ke Universiti Teknologi Malaysia',
                'body' => "Kantor Urusan Internasional ITS membuka pendaftaran program pertukaran mahasiswa satu semester ke Universiti Teknologi Malaysia (UTM).\n\nPendaftar harus aktif di semester 4 atau 5, memiliki IPK minimal 3,25, dan menguasai bahasa Inggris dengan baik. Biaya kuliah ditanggung melalui skema pertukaran, sedangkan biaya hidup menjadi tanggung jawab peserta.\n\nBatas pendaftaran 28 November 2026.",
                'published_at' => now()->subDays(7),
            ],
            'hackathon-smart-campus' => [
                'author' => 'salsabila',
                'category' => 'kompetisi',
                'type' => PostType::Event,
                'status' => PostStatus::Published,
                'title' => 'Hackathon Smart Campus 2026',
                'body' => "Hackathon Smart Campus mengajak mahasiswa merancang solusi digital untuk permasalahan nyata di kampus, mulai dari layanan akademik hingga pengelolaan energi.\n\nTim terdiri dari 3 sampai 4 orang dan boleh berasal dari departemen berbeda. Peserta akan didampingi mentor dari industri selama 24 jam pengerjaan.\n\nHadiah utama berupa dana pengembangan produk dan kesempatan inkubasi di inkubator ITS.",
                'published_at' => now()->subDays(10),
                'event_starts_at' => now()->addDays(30)->setTime(8, 0),
                'event_ends_at' => now()->addDays(31)->setTime(8, 0),
                'event_location' => 'Gedung Techno Park ITS',
            ],
            'webinar-karier' => [
                'author' => 'student',
                'category' => 'karier',
                'type' => PostType::Event,
                'status' => PostStatus::Published,
                'title' => 'Webinar Karier: Menyiapkan Diri untuk Dunia Industri Teknologi',
                'body' => "Career Development Center ITS mengadakan webinar karier bersama alumni yang bekerja di perusahaan teknologi. Materi mencakup penyusunan CV, persiapan wawancara, dan strategi membangun portofolio.\n\nWebinar terbuka untuk seluruh mahasiswa dan alumni. Sesi tanya jawab akan dilaksanakan setelah pemaparan materi.\n\nTautan rapat akan dikirim melalui email kepada peserta yang terdaftar.",
                'published_at' => now()->subDays(12),
                'event_starts_at' => now()->addDays(18)->setTime(19, 0),
                'event_ends_at' => now()->addDays(18)->setTime(21, 0),
                'event_location' => 'Online via Zoom',
            ],
            'kuesioner-kepuasan' => [
                'author' => 'moderator',
                'category' => 'akademik',
                'type' => PostType::Announcement,
                'status' => PostStatus::Published,
                'title' => 'Pengingat: Batas Pengisian Kuesioner Kepuasan Mahasiswa',
                'body' => "Pengisian kuesioner kepuasan mahasiswa terhadap layanan akademik dan fasilitas kampus akan ditutup pada 5 November 2026.\n\nPartisipasi mahasiswa sangat penting untuk membantu ITS meningkatkan kualitas layanan. Pengisian membutuhkan waktu sekitar 10 menit.\n\nKuesioner dapat diakses melalui portal akademik menggunakan akun ITS masing-masing.",
                'published_at' => now()->subDays(11),
            ],
            'rapat-dies-natalis' => [
                'author' => 'moderator',
                'category' => 'kegiatan-kampus',
                'type' => PostType::Announcement,
                'status' => PostStatus::Archived,
                'title' => 'Rapat Koordinasi Panitia Dies Natalis ITS',
                'body' => "Rapat koordinasi panitia Dies Natalis ITS telah selesai dilaksanakan. Catatan rapat dan pembagian tugas telah dibagikan kepada seluruh koordinator divisi.\n\nPost ini diarsipkan karena acara telah berlangsung.",
                'published_at' => now()->subDays(45),
            ],
            'rencana-november' => [
                'author' => 'moderator',
                'category' => 'kegiatan-kampus',
                'type' => PostType::Announcement,
                'status' => PostStatus::Draft,
                'title' => 'Rencana Kegiatan Kampus Bulan November',
                'body' => "Draf rangkuman kegiatan kampus yang sedang disusun. Jadwal dan narahubung masih menunggu konfirmasi dari masing-masing panitia.\n\nPost ini belum dipublikasikan.",
                'published_at' => null,
            ],
        ];

        $seeded = [];

        foreach ($posts as $key => $data) {
            $post = Post::updateOrCreate(
                ['slug' => str($data['title'])->slug()->value()],
                [
                    'user_id' => $users[$data['author']]->id,
                    'category_id' => $categories[$data['category']]->id,
                    'type' => $data['type'],
                    'status' => $data['status'],
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'is_pinned' => $data['is_pinned'] ?? false,
                    'published_at' => $data['published_at'] ?? null,
                    'event_starts_at' => $data['event_starts_at'] ?? null,
                    'event_ends_at' => $data['event_ends_at'] ?? null,
                    'event_location' => $data['event_location'] ?? null,
                ],
            );

            $seeded[$key] = $post;
        }

        return $seeded;
    }

    /**
     * Add a few comments to recent posts.
     *
     * @param  array<string, Post>  $posts
     * @param  array<string, User>  $users
     */
    protected function seedComments(array $posts, array $users): void
    {
        $comments = [
            ['post' => 'krs', 'user' => 'salsabila', 'body' => 'Apakah pengisian KRS untuk mahasiswa tingkat akhir tetap mengikuti jadwal yang sama?'],
            ['post' => 'krs', 'user' => 'bagas', 'body' => 'Sudah saya cek di portal, jadwal per departemen ternyata berbeda. Terima kasih infonya!'],
            ['post' => 'open-recruitment-robotika', 'user' => 'student', 'body' => 'Untuk divisi pemrograman apakah wajib sudah menguasai C++?'],
            ['post' => 'open-recruitment-robotika', 'user' => 'nadia', 'body' => 'Tidak wajib, nanti ada pelatihan dasar sebelum seleksi.'],
            ['post' => 'seminar-ai-maritime', 'user' => 'bagas', 'body' => 'Kuotanya masih tersedia? Saya ingin mendaftar bersama teman satu tim.'],
            ['post' => 'workshop-git', 'user' => 'salsabila', 'body' => 'Workshop ini cocok untuk yang baru pertama kali memakai Git.'],
        ];

        foreach ($comments as $comment) {
            Comment::firstOrCreate([
                'post_id' => $posts[$comment['post']]->id,
                'user_id' => $users[$comment['user']]->id,
                'body' => $comment['body'],
            ]);
        }
    }

    /**
     * Save a few bookmarks for the demo student.
     *
     * @param  array<string, Post>  $posts
     * @param  array<string, User>  $users
     */
    protected function seedBookmarks(array $posts, array $users): void
    {
        foreach (['beasiswa-unggulan', 'magang-pal', 'student-exchange-utm'] as $postKey) {
            Bookmark::firstOrCreate([
                'user_id' => $users['student']->id,
                'post_id' => $posts[$postKey]->id,
            ]);
        }
    }

    /**
     * Register demo students for upcoming events.
     *
     * @param  array<string, Post>  $posts
     * @param  array<string, User>  $users
     */
    protected function seedEventRegistrations(array $posts, array $users): void
    {
        $registrations = [
            ['post' => 'seminar-ai-maritime', 'user' => 'student'],
            ['post' => 'seminar-ai-maritime', 'user' => 'salsabila'],
            ['post' => 'workshop-git', 'user' => 'student'],
            ['post' => 'workshop-git', 'user' => 'bagas'],
            ['post' => 'webinar-karier', 'user' => 'nadia'],
        ];

        foreach ($registrations as $registration) {
            EventRegistration::firstOrCreate([
                'post_id' => $posts[$registration['post']]->id,
                'user_id' => $users[$registration['user']]->id,
            ]);
        }
    }
}
