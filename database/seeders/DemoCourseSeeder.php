<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Two complete demo courses (sections, video + text lessons, pre/post-test questions of every type)
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
            ['single', 'Apa pengertian personal branding yang paling tepat?', [
                'Logo dan warna yang dipakai di akun media sosial' => false,
                'Persepsi orang lain tentang dirimu yang kamu bangun secara sadar dan konsisten' => true,
                'Jumlah followers yang kamu miliki' => false,
                'Iklan berbayar untuk mempromosikan diri' => false,
            ]],
            ['multiple', 'Mana saja yang termasuk elemen personal branding? (pilih semua yang benar)', [
                'Nilai dan keahlian yang kamu tawarkan' => true,
                'Konsistensi pesan dan gaya komunikasi' => true,
                'Membeli followers agar terlihat populer' => false,
                'Cerita (storytelling) tentang perjalananmu' => true,
            ]],
            ['true_false', 'Personal branding hanya penting untuk selebriti dan influencer.', ['Benar' => false, 'Salah' => true]],
            ['single', 'Dalam rumus personal brand statement, bagian "untuk siapa" disebut...', [
                'Target audiens' => true,
                'Kompetitor' => false,
                'Nilai jual produk' => false,
            ]],
            ['true_false', 'Konsistensi lebih penting daripada sesekali membuat konten yang viral.', ['Benar' => true, 'Salah' => false]],
            ['essay', 'Tuliskan personal brand statement versimu sendiri (1-2 kalimat).', []],
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
            ['single', 'Apa yang dimaksud dengan digital marketing?', [
                'Pemasaran produk atau jasa melalui channel digital' => true,
                'Menjual barang hanya di toko fisik' => false,
                'Membuat website tanpa tujuan bisnis' => false,
            ]],
            ['multiple', 'Mana saja yang termasuk channel digital marketing?', [
                'Media sosial' => true,
                'SEO (mesin pencari)' => true,
                'Brosur cetak' => false,
                'Email marketing' => true,
            ]],
            ['true_false', 'SEO bertujuan agar website muncul di hasil pencarian Google.', ['Benar' => true, 'Salah' => false]],
            ['essay', 'Channel digital marketing mana yang paling ingin kamu pelajari, dan kenapa?', []],
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

        foreach ($questions as $i => [$type, $text, $options]) {
            $question = $course->questions()->create(['type' => $type, 'question' => $text, 'order' => $i + 1]);

            foreach ($options as $optionText => $correct) {
                $question->options()->create(['text' => $optionText, 'is_correct' => $correct]);
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
