<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Two complete demo courses (sections, video + text lessons, per-section pre/post-tests with every question type)
 * so the learning flow can be tried end to end. Re-running replaces only these two courses.
 */
class DemoCourseSeeder extends Seeder
{
    public function run(): void
    {
        $branding = $this->course([
            'slug' => 'demo-personal-branding-untuk-pemula',
            'title' => 'Personal Branding untuk Pemula',
            'category' => 'Personal Branding',
            'instructor' => 'Tim Ray Academy',
            'course_type' => 'paid',
            'price' => 99000,
            'level' => 'Beginner',
            'trailer_video_id' => 'u695RkzIH58',
            'post_test_pass_score' => 70,
            'description' => "Kelas ini membantumu membangun personal branding dari nol: mengenali nilai unik dirimu, menyusun pesan yang konsisten, lalu menampilkannya di media sosial.\n\nCocok untuk mahasiswa, fresh graduate, profesional, dan pelaku usaha yang ingin lebih dikenal dan dipercaya di bidangnya.",
            'benefits' => "Memahami apa itu personal branding dan kenapa penting\nMenemukan nilai unik (unique value) dirimu\nMenyusun pesan dan positioning yang konsisten\nMembangun personal branding di media sosial\nMenyusun rencana konten 30 hari",
        ], [
            'Bab 1: Mengenal Personal Branding' => [
                ['video', 'Cara Membangun Personal Branding dari Nol', 'u695RkzIH58', true],
                ['text', 'Apa Itu Personal Branding?', $this->html('personal-branding')],
                ['video', 'Bangun Personal Brand Mulai dari Mana?', 'ElL0IdQOe_M'],
            ],
            'Bab 2: Menemukan Nilai Unik Dirimu' => [
                ['video', 'Cara Mulai Personal Branding #DariNol', 'yjw_9GNZn54'],
                ['text', 'Latihan: Menyusun Personal Brand Statement', $this->html('statement')],
            ],
            'Bab 3: Personal Branding di Media Sosial' => [
                ['video', 'Cara Bangun Personal Branding di Social Media', 'w3y54_STyVA'],
                ['video', 'Tips Personal Branding untuk Pemula', '5hLTG_ToX_M'],
                ['text', 'Rencana Konten 30 Hari', $this->html('content-plan')],
            ],
        ], [
            'Bab 1: Mengenal Personal Branding' => [
                'pre' => [
                    ['single', 'Apa pengertian personal branding yang paling tepat?', [
                        'Logo dan warna yang dipakai di akun media sosial' => false,
                        'Persepsi orang lain tentang dirimu yang kamu bangun secara sadar dan konsisten' => true,
                        'Jumlah followers yang kamu miliki' => false,
                        'Iklan berbayar untuk mempromosikan diri' => false,
                    ]],
                    ['true_false', 'Personal branding hanya penting untuk selebriti dan influencer.', ['Benar' => false, 'Salah' => true]],
                ],
                'post' => [
                    ['multiple', 'Mana saja yang termasuk tiga pilar personal branding? (pilih semua yang benar)', [
                        'Nilai' => true,
                        'Konsistensi' => true,
                        'Jumlah followers' => false,
                        'Visibilitas' => true,
                    ]],
                    ['true_false', 'Personal branding yang kuat dibangun dari siapa kamu sebenarnya, bukan pencitraan palsu.', ['Benar' => true, 'Salah' => false]],
                    ['essay', 'Jelaskan dengan kata-katamu sendiri apa itu personal branding.', 'Personal branding adalah persepsi orang lain tentang diri kita yang dibangun secara sadar dan konsisten, berdasarkan nilai dan keahlian yang kita tawarkan.'],
                ],
            ],
            'Bab 2: Menemukan Nilai Unik Dirimu' => [
                'pre' => [
                    ['single', 'Dalam rumus personal brand statement, bagian "untuk siapa" disebut...', [
                        'Target audiens' => true,
                        'Kompetitor' => false,
                        'Nilai jual produk' => false,
                    ]],
                    ['true_false', 'Personal brand statement sebaiknya panjang, minimal satu halaman.', ['Benar' => false, 'Salah' => true]],
                ],
                'post' => [
                    ['single', 'Personal brand statement idealnya terdiri dari...', [
                        'Satu atau dua kalimat' => true,
                        'Satu halaman penuh' => false,
                        'Daftar semua pengalaman kerja' => false,
                    ]],
                    ['essay', 'Sebutkan rumus sederhana personal brand statement.', 'Saya membantu [target audiens] untuk [hasil yang mereka inginkan] melalui [keahlian atau cara unik saya].'],
                ],
            ],
            'Bab 3: Personal Branding di Media Sosial' => [
                'pre' => [
                    ['true_false', 'Konsistensi lebih penting daripada sesekali membuat konten yang viral.', ['Benar' => true, 'Salah' => false]],
                    ['single', 'Dalam pola konten 3-2-1, berapa konten edukasi yang dibuat setiap minggu?', ['1' => false, '2' => false, '3' => true]],
                ],
                'post' => [
                    ['multiple', 'Mana saja yang ada di checklist sebelum posting? (pilih semua yang benar)', [
                        'Sesuai personal brand statement' => true,
                        'Bermanfaat untuk audiens' => true,
                        'Pasti viral' => false,
                        'Gaya visual dan bahasanya konsisten' => true,
                    ]],
                    ['essay', 'Jelaskan isi pola konten 3-2-1 untuk satu minggu.', 'Tiga konten edukasi berisi tips atau insight, dua konten cerita tentang pengalaman atau proses, dan satu konten interaksi seperti pertanyaan atau polling.'],
                ],
            ],
        ]);

        $this->course([
            'slug' => 'demo-dasar-digital-marketing',
            'title' => 'Dasar-Dasar Digital Marketing',
            'category' => 'Digital Marketing',
            'instructor' => 'Tim Ray Academy',
            'course_type' => 'free',
            'price' => 0,
            'level' => 'Beginner',
            'trailer_video_id' => 'UcXrCRwt4mY',
            'post_test_pass_score' => 60,
            'description' => "Kelas gratis untuk memahami konsep dasar digital marketing: apa itu, bagaimana cara kerjanya, dan dari mana sebaiknya mulai belajar.\n\nDi akhir kelas kamu akan paham peta besar digital marketing dan siap melanjutkan ke materi yang lebih spesifik.",
            'benefits' => "Memahami konsep dasar digital marketing\nMengenal channel: media sosial, SEO, iklan, dan email\nMengetahui urutan belajar yang tepat untuk pemula",
        ], [
            'Bab 1: Konsep Dasar' => [
                ['video', 'Digital Marketing untuk Pemula: 10 Menit Paham Konsepnya', 'UcXrCRwt4mY', true],
                ['text', 'Rangkuman: Channel Digital Marketing', $this->html('channels')],
            ],
            'Bab 2: Mulai Belajar' => [
                ['video', 'Belajar Digital Marketing Mulai dari Mana?', 'hAPEvFJs1Ag'],
                ['video', 'Kerangka Ideal Belajar Digital Marketing', 'gGAA5gxzRoE'],
            ],
        ], [
            'Bab 1: Konsep Dasar' => [
                'pre' => [
                    ['single', 'Apa yang dimaksud dengan digital marketing?', [
                        'Pemasaran produk atau jasa melalui channel digital' => true,
                        'Menjual barang hanya di toko fisik' => false,
                        'Membuat website tanpa tujuan bisnis' => false,
                    ]],
                    ['true_false', 'SEO bertujuan agar website muncul di hasil pencarian Google.', ['Benar' => true, 'Salah' => false]],
                ],
                'post' => [
                    ['multiple', 'Mana saja yang termasuk channel digital marketing?', [
                        'Media sosial' => true,
                        'SEO (mesin pencari)' => true,
                        'Brosur cetak' => false,
                        'Email marketing' => true,
                    ]],
                    ['essay', 'Apa tujuan email marketing menurut materi?', 'Menjaga hubungan dengan pelanggan yang sudah mengenal brand.'],
                ],
            ],
            'Bab 2: Mulai Belajar' => [
                'pre' => [
                    ['true_false', 'Pemula sebaiknya langsung mempelajari semua channel digital marketing sekaligus.', ['Benar' => false, 'Salah' => true]],
                ],
                'post' => [
                    ['single', 'Saran untuk pemula yang baru belajar digital marketing adalah...', [
                        'Pilih satu channel dan pelajari sampai paham' => true,
                        'Langsung pasang iklan di semua platform' => false,
                        'Fokus mengejar jumlah followers' => false,
                    ]],
                    ['essay', 'Mengapa pemula sebaiknya fokus pada satu channel dulu?', 'Supaya bisa memahami satu channel sampai benar-benar paham sebelum menambah channel lain, karena mempelajari semuanya sekaligus membuat tidak fokus.'],
                ],
            ],
        ]);

        // The seeded student gets the paid course so the whole flow can be tried without paying.
        if ($student = User::where('email', 'student@example.com')->first()) {
            Enrollment::updateOrCreate(
                ['user_id' => $student->id, 'course_id' => $branding->id],
                ['status' => 'active', 'enrolled_at' => now(), 'payment_status' => 'completed', 'payment_method' => 'free'],
            );
        }
    }

    private function course(array $data, array $sections, array $questions): Course
    {
        Course::where('slug', $data['slug'])->first()?->delete();

        $category = CourseCategory::firstOrCreate(
            ['name' => $data['category']],
            ['slug' => \Illuminate\Support\Str::slug($data['category']), 'description' => $data['category']],
        );

        $course = new Course(collect($data)->except(['category', 'slug'])->all() + [
            'thumbnail' => 'default-course.jpg',
            'course_category_id' => $category->id,
        ]);
        $course->slug = $data['slug'];
        $course->save();

        $sectionOrder = 0;
        foreach ($sections as $title => $lessons) {
            $section = $course->sections()->create(['title' => $title, 'order' => ++$sectionOrder]);

            foreach (array_values($lessons) as $i => $lesson) {
                [$type, $lessonTitle, $body, $preview] = $lesson + [3 => false];
                $section->lessons()->create([
                    'course_id' => $course->id,
                    'title' => $lessonTitle,
                    'type' => $type,
                    'youtube_video_id' => $type === 'video' ? $body : null,
                    'content' => $type === 'text' ? $body : null,
                    'order' => $i + 1,
                    'is_preview' => $preview,
                ]);
            }
        }

        // Each section gets its own pre-test (choice questions) and post-test (may include essays,
        // whose third value is the answer key the AI grader compares against).
        foreach ($questions as $sectionTitle => $tests) {
            $section = $course->sections()->where('title', $sectionTitle)->firstOrFail();

            foreach ($tests as $kind => $items) {
                foreach ($items as $i => [$type, $text, $extra]) {
                    $question = $section->questions()->create([
                        'course_id' => $course->id,
                        'kind' => $kind,
                        'type' => $type,
                        'question' => $text,
                        'answer_key' => $type === 'essay' ? $extra : null,
                        'order' => $i + 1,
                    ]);

                    foreach ($type === 'essay' ? [] : $extra as $optionText => $correct) {
                        $question->options()->create(['text' => (string) $optionText, 'is_correct' => $correct]);
                    }
                }
            }
        }

        return $course;
    }

    private function html(string $key): string
    {
        return match ($key) {
            'personal-branding' => '<h2>Apa Itu Personal Branding?</h2>
<p>Personal branding adalah <strong>persepsi orang lain tentang dirimu</strong>, yang kamu bangun secara sadar dan konsisten. Setiap orang sebenarnya sudah punya "brand", yang membedakan hanyalah apakah brand itu dibentuk dengan sengaja atau dibiarkan terbentuk sendiri.</p>
<h3>Kenapa penting?</h3>
<ul><li><strong>Lebih dipercaya.</strong> Orang lebih mudah percaya pada sosok yang jelas keahliannya.</li><li><strong>Peluang datang sendiri.</strong> Tawaran kerja, klien, atau kolaborasi lebih mudah menemukanmu.</li><li><strong>Pembeda.</strong> Di antara banyak orang dengan keahlian sama, brand yang jelas membuatmu diingat.</li></ul>
<h3>Tiga pilar personal branding</h3>
<ol><li><strong>Nilai:</strong> keahlian dan manfaat yang kamu tawarkan.</li><li><strong>Konsistensi:</strong> pesan, gaya, dan kualitas yang sama di semua tempat.</li><li><strong>Visibilitas:</strong> hadir di tempat audiensmu berada.</li></ol>
<blockquote>Personal branding bukan pencitraan palsu. Brand yang kuat dibangun dari siapa kamu sebenarnya.</blockquote>',
            'statement' => '<h2>Menyusun Personal Brand Statement</h2>
<p>Personal brand statement adalah satu atau dua kalimat yang menjelaskan <strong>siapa kamu, membantu siapa, dan dengan cara apa</strong>.</p>
<h3>Rumus sederhana</h3>
<p><em>Saya membantu <strong>[target audiens]</strong> untuk <strong>[hasil yang mereka inginkan]</strong> melalui <strong>[keahlian/cara unikmu]</strong>.</em></p>
<h3>Contoh</h3>
<ul><li>"Saya membantu UMKM kuliner meningkatkan penjualan online melalui foto produk dan konten Instagram yang menggugah selera."</li><li>"Saya membantu mahasiswa tingkat akhir lolos interview kerja pertama melalui latihan CV dan simulasi wawancara."</li></ul>
<h3>Latihan</h3>
<ol><li>Tulis 3 keahlian terkuatmu.</li><li>Tentukan satu kelompok orang yang paling ingin kamu bantu.</li><li>Gabungkan dengan rumus di atas.</li></ol>
<p>Simpan hasilnya, karena akan kamu pakai lagi di post-test.</p>',
            'content-plan' => '<h2>Rencana Konten 30 Hari</h2>
<p>Konsistensi lebih penting daripada viral sesekali. Gunakan pola <strong>3-2-1</strong> setiap minggu:</p>
<ul><li><strong>3 konten edukasi:</strong> tips, cara, atau insight dari keahlianmu.</li><li><strong>2 konten cerita:</strong> pengalaman, proses, atau kegagalan yang kamu pelajari.</li><li><strong>1 konten interaksi:</strong> pertanyaan, polling, atau sesi tanya jawab.</li></ul>
<h3>Checklist sebelum posting</h3>
<ol><li>Apakah konten ini sesuai dengan personal brand statement-ku?</li><li>Apakah audiensku mendapat manfaat dari konten ini?</li><li>Apakah gaya visual dan bahasanya konsisten dengan konten sebelumnya?</li></ol>
<p>Selamat! Kamu sudah menyelesaikan semua materi. Lanjutkan ke <strong>post-test</strong> untuk menyelesaikan kelas.</p>',
            'channels' => '<h2>Rangkuman: Channel Digital Marketing</h2>
<p>Digital marketing adalah pemasaran produk atau jasa melalui <strong>channel digital</strong>. Channel yang paling umum:</p>
<ul><li><strong>Media sosial:</strong> Instagram, TikTok, LinkedIn, Facebook, untuk membangun awareness dan komunitas.</li><li><strong>SEO:</strong> membuat website muncul di hasil pencarian Google secara organik.</li><li><strong>Iklan berbayar:</strong> Meta Ads, Google Ads, untuk menjangkau audiens lebih cepat.</li><li><strong>Email marketing:</strong> menjaga hubungan dengan pelanggan yang sudah kenal brand-mu.</li></ul>
<blockquote>Tidak perlu menguasai semua channel sekaligus. Pilih satu, pelajari sampai paham, baru tambah channel berikutnya.</blockquote>',
        };
    }
}
