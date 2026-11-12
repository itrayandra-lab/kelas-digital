<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Six complete sample courses (sections, video + text lessons, per-section pre/post-tests with every question type)
 * so the learning flow can be tried end to end. Re-running replaces only these courses (matched by slug).
 */
class DemoCourseSeeder extends Seeder
{
    public function run(): void
    {
        $branding = $this->course([
            'slug' => 'personal-branding-untuk-pemula',
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
            'slug' => 'dasar-dasar-digital-marketing',
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

        $basic = $this->course([
            'slug' => 'basic-programming',
            'title' => 'Basic Programming',
            'category' => 'Technology & AI',
            'instructor' => 'Muhammad Taufan Akbar S.Kom',
            'course_type' => 'paid',
            'price' => 50000,
            'level' => 'Beginner',
            'trailer_video_id' => '6Z-0f_mcyiY',
            'post_test_pass_score' => 70,
            'description' => "Kelas ini mengenalkan cara berpikir seorang programmer: memecah masalah menjadi langkah-langkah (algoritma), menyimpan data di variabel, lalu mengatur alur program dengan percabangan dan perulangan.\n\nCocok untuk kamu yang belum pernah menulis kode sama sekali dan ingin punya dasar yang kuat sebelum belajar bahasa pemrograman tertentu.",
            'benefits' => "Memahami apa itu algoritma dan pemrograman\nMengenal variabel dan tipe data\nMemakai percabangan untuk mengambil keputusan\nMemakai perulangan untuk pekerjaan berulang",
        ], [
            'Bab 1: Mengenal Pemrograman' => [
                ['video', 'Apa itu Algoritma dan Pemrograman?', '6Z-0f_mcyiY', true],
                ['text', 'Apa Itu Pemrograman?', $this->html('pemrograman')],
                ['video', 'Pengantar Algoritma untuk Pemula', 'wIzNyT8h5gU'],
            ],
            'Bab 2: Variabel dan Tipe Data' => [
                ['text', 'Variabel dan Tipe Data', $this->html('variabel')],
            ],
            'Bab 3: Percabangan dan Perulangan' => [
                ['video', 'Percabangan dengan Satu Kondisi', 'ORWq8BEnFqc'],
                ['video', 'Konsep Perulangan For', 'BNq_KslsKEo'],
                ['text', 'Rangkuman Percabangan dan Perulangan', $this->html('kontrol')],
            ],
        ], [
            'Bab 1: Mengenal Pemrograman' => [
                'pre' => [
                    ['single', 'Algoritma adalah...', [
                        'Urutan langkah yang jelas untuk menyelesaikan masalah' => true,
                        'Nama lain dari komputer' => false,
                        'Bahasa pemrograman tertentu' => false,
                    ]],
                    ['true_false', 'Komputer bisa menjalankan perintah yang ambigu dengan benar.', ['Benar' => false, 'Salah' => true]],
                ],
                'post' => [
                    ['multiple', 'Mana saja ciri algoritma yang baik? (pilih semua yang benar)', [
                        'Langkahnya berurutan' => true,
                        'Jelas dan tidak ambigu' => true,
                        'Punya akhir' => true,
                        'Boleh ditafsirkan berbeda-beda' => false,
                    ]],
                    ['essay', 'Jelaskan perbedaan algoritma dan program.', 'Algoritma adalah urutan langkah logis untuk menyelesaikan masalah, sedangkan program adalah algoritma yang sudah ditulis dalam bahasa pemrograman sehingga bisa dijalankan komputer.'],
                ],
            ],
            'Bab 2: Variabel dan Tipe Data' => [
                'pre' => [
                    ['single', 'Tipe data yang cocok untuk menyimpan nama seseorang adalah...', ['String' => true, 'Integer' => false, 'Boolean' => false]],
                    ['true_false', 'Nilai sebuah variabel bisa diubah ketika program berjalan.', ['Benar' => true, 'Salah' => false]],
                ],
                'post' => [
                    ['single', 'Jika x = 5, lalu x = x + 2, berapa nilai x sekarang?', ['5' => false, '7' => true, '10' => false]],
                    ['essay', 'Apa itu variabel dalam pemrograman?', 'Variabel adalah tempat atau wadah bernama untuk menyimpan data atau nilai yang bisa dipakai dan diubah selama program berjalan.'],
                ],
            ],
            'Bab 3: Percabangan dan Perulangan' => [
                'pre' => [
                    ['true_false', 'Perulangan dipakai untuk menjalankan perintah yang sama berkali-kali.', ['Benar' => true, 'Salah' => false]],
                    ['single', 'Struktur untuk memilih tindakan berdasarkan kondisi disebut...', ['Percabangan' => true, 'Perulangan' => false, 'Variabel' => false]],
                ],
                'post' => [
                    ['multiple', 'Mana saja yang termasuk perulangan? (pilih semua yang benar)', ['for' => true, 'while' => true, 'if' => false, 'else' => false]],
                    ['essay', 'Kapan kita memakai percabangan dalam program?', 'Percabangan dipakai ketika program harus memilih tindakan yang berbeda tergantung pada sebuah kondisi, misalnya jika nilai lebih dari 70 maka lulus, jika tidak maka mengulang.'],
                ],
            ],
        ]);

        $coding = $this->course([
            'slug' => 'tutorial-coding',
            'title' => 'Tutorial Coding: PHP, JavaScript, Python, dan Laravel',
            'category' => 'Technology & AI',
            'instructor' => 'Muhammad Taufan Akbar S.Kom',
            'course_type' => 'paid',
            'price' => 100000,
            'level' => 'Intermediate',
            'trailer_video_id' => 'I-k-iTUMQAY',
            'post_test_pass_score' => 70,
            'description' => "Kelas ini mengenalkan tiga bahasa pemrograman yang paling banyak dipakai, yaitu PHP, JavaScript, dan Python, lalu berlanjut ke framework Laravel untuk membangun aplikasi web.\n\nCocok untuk kamu yang sudah paham dasar pemrograman dan ingin memilih bahasa yang tepat untuk mulai membuat aplikasi.",
            'benefits' => "Mengenal PHP, JavaScript, dan Python beserta kegunaannya\nMemahami peran framework dalam pengembangan aplikasi\nMengenal Laravel sebagai framework PHP",
        ], [
            'Bab 1: Dasar-Dasar Programming' => [
                ['video', 'Pengenalan PHP', 'QJMTprXtPa0', true],
                ['text', 'Pengenalan JavaScript', $this->html('javascript')],
                ['video', 'Pengenalan Python', 'EHc4ZcT2sTk'],
            ],
            'Bab 2: Belajar Framework' => [
                ['text', 'Apa Itu Framework?', $this->html('framework')],
                ['video', 'Pengenalan Laravel', 'gH0e_wAm_nI'],
            ],
        ], [
            'Bab 1: Dasar-Dasar Programming' => [
                'pre' => [
                    ['single', 'Apa itu programming?', [
                        'Proses menulis instruksi agar komputer menjalankan tugas' => true,
                        'Proses merakit perangkat keras komputer' => false,
                        'Proses mendesain tampilan dengan aplikasi grafis' => false,
                    ]],
                    ['multiple', 'Mana saja yang merupakan bahasa pemrograman? (pilih semua yang benar)', ['PHP' => true, 'Python' => true, 'JavaScript' => true, 'HTML' => false]],
                ],
                'post' => [
                    ['single', 'PHP paling banyak dipakai untuk...', [
                        'Membuat sisi server (backend) aplikasi web' => true,
                        'Mengedit foto' => false,
                        'Membuat sistem operasi' => false,
                    ]],
                    ['true_false', 'JavaScript hanya bisa berjalan di browser.', ['Benar' => false, 'Salah' => true]],
                    ['essay', 'Apa itu JavaScript?', 'JavaScript adalah bahasa pemrograman yang dipakai untuk membuat halaman web menjadi interaktif, dan sekarang juga bisa dijalankan di server melalui Node.js.'],
                ],
            ],
            'Bab 2: Belajar Framework' => [
                'pre' => [
                    ['true_false', 'Framework membantu developer supaya tidak menulis semuanya dari nol.', ['Benar' => true, 'Salah' => false]],
                    ['single', 'Laravel adalah framework untuk bahasa...', ['PHP' => true, 'Python' => false, 'JavaScript' => false]],
                ],
                'post' => [
                    ['multiple', 'Mana saja yang merupakan framework PHP? (pilih semua yang benar)', ['Laravel' => true, 'CodeIgniter' => true, 'React' => false, 'Tailwind CSS' => false]],
                    ['essay', 'Sebutkan satu keuntungan memakai framework dan jelaskan alasannya.', 'Framework mempercepat pengembangan karena struktur dan fitur umum seperti routing, database, dan keamanan sudah disediakan, sehingga developer tidak perlu menulis semuanya dari nol.'],
                ],
            ],
        ]);

        // Short course for demos: ~30-second Shorts and brief readings, so the whole flow takes minutes.
        // Video durations are left empty on purpose: a filled duration is a minimum watch length.
        $this->course([
            'slug' => 'kelas-kilat-siap-kerja',
            'title' => 'Kelas Kilat: Siap Kerja dalam 5 Menit',
            'category' => 'Business & Strategy',
            'instructor' => 'Tim Ray Academy',
            'course_type' => 'free',
            'price' => 0,
            'level' => 'Beginner',
            'trailer_video_id' => 'PjfzPm9GAYY',
            'post_test_pass_score' => 50,
            'description' => "Kelas singkat berisi dua keterampilan dasar dunia kerja: menyelesaikan masalah dan berbicara dengan percaya diri.\n\nSetiap materi berupa video pendek di bawah satu menit dan bacaan singkat, jadi seluruh kelas bisa diselesaikan dalam beberapa menit.",
            'benefits' => "Melatih cara menyelesaikan masalah di tempat kerja\nTampil lebih percaya diri saat berbicara di depan orang",
        ], [
            'Bab 1: Problem Solving' => [
                ['video', '5 Tips Problem Solving di Dunia Kerja', 'GrmL05VGWVQ', true],
                ['text', 'Rangkuman: Langkah Menyelesaikan Masalah', $this->html('problem-solving')],
            ],
            'Bab 2: Percaya Diri Berbicara' => [
                ['video', 'Power Pose Sebelum Berbicara', 'PjfzPm9GAYY'],
                ['text', 'Rangkuman: Tampil Percaya Diri', $this->html('percaya-diri')],
            ],
        ], [
            'Bab 1: Problem Solving' => [
                'pre' => [
                    ['true_false', 'Kemampuan menyelesaikan masalah bisa dilatih.', ['Benar' => true, 'Salah' => false]],
                    ['single', 'Langkah pertama menyelesaikan masalah adalah...', [
                        'Memahami masalahnya dengan jelas' => true,
                        'Langsung mencari siapa yang salah' => false,
                        'Menunggu masalahnya hilang sendiri' => false,
                    ]],
                ],
                'post' => [
                    ['multiple', 'Mana saja yang termasuk langkah menyelesaikan masalah? (pilih semua yang benar)', [
                        'Pahami masalahnya' => true,
                        'Cari beberapa alternatif solusi' => true,
                        'Abaikan masukan orang lain' => false,
                    ]],
                    ['essay', 'Menurutmu, apa yang pertama kali kamu lakukan saat menghadapi masalah di tempat kerja?', 'Memahami dan mendefinisikan masalahnya dengan jelas terlebih dahulu sebelum mencari solusi.'],
                ],
            ],
            'Bab 2: Percaya Diri Berbicara' => [
                'pre' => [
                    ['true_false', 'Rasa gugup sebelum berbicara di depan umum adalah hal yang wajar.', ['Benar' => true, 'Salah' => false]],
                    ['single', 'Power pose dilakukan...', [
                        'Sebelum mulai berbicara' => true,
                        'Setelah acara selesai' => false,
                        'Hanya saat ditanya audiens' => false,
                    ]],
                ],
                'post' => [
                    ['single', 'Tujuan melakukan power pose adalah...', [
                        'Membuat diri merasa lebih percaya diri' => true,
                        'Supaya terlihat sombong' => false,
                        'Mengulur waktu presentasi' => false,
                    ]],
                    ['essay', 'Sebutkan satu cara supaya lebih percaya diri saat berbicara di depan orang.', 'Melakukan power pose sebelum tampil, berlatih lebih dulu, atau menjaga postur tegak dan kontak mata dengan audiens.'],
                ],
            ],
        ]);

        $this->paidShortCourse();

        // The seeded student gets the paid courses so the whole flow can be tried without paying.
        if ($student = User::where('email', 'student@example.com')->first()) {
            foreach ([$branding, $basic, $coding] as $paid) {
                Enrollment::updateOrCreate(
                    ['user_id' => $student->id, 'course_id' => $paid->id],
                    ['status' => 'active', 'enrolled_at' => now(), 'payment_status' => 'completed', 'payment_method' => 'free'],
                );
            }
        }
    }

    /**
     * Short paid course for demoing the purchase flow. Nobody is enrolled, so the "Beli" button opens Midtrans.
     * Public so it can be seeded alone without recreating the other sample courses.
     */
    public function paidShortCourse(): Course
    {
        return $this->course([
            'slug' => 'manajemen-waktu-kilat',
            'title' => 'Manajemen Waktu Kilat untuk Pekerja & Mahasiswa',
            'category' => 'Komunikasi & Soft Skills',
            'instructor' => 'Tim Ray Academy',
            'course_type' => 'paid',
            'price' => 49000,
            'level' => 'Beginner',
            'trailer_video_id' => 'scQiC0c8Mno',
            'post_test_pass_score' => 60,
            'description' => "Kelas singkat tentang cara mengatur waktu supaya pekerjaan dan kuliah sama-sama beres.\n\nSetiap bab berisi satu video pendek di bawah satu menit dan satu bacaan singkat, lalu ditutup dengan post-test.",
            'benefits' => "Menentukan prioritas dengan matriks penting-mendesak\nMenyusun jadwal harian yang realistis\nMembagi waktu antara kerja dan kuliah",
        ], [
            'Bab 1: Mengatur Prioritas' => [
                ['video', 'Cara Mengatur Waktu Agar Lebih Produktif', 'scQiC0c8Mno', true],
                ['text', 'Rangkuman: Matriks Prioritas', $this->html('prioritas')],
            ],
            'Bab 2: Jadwal Kerja dan Kuliah' => [
                ['video', 'Mengatur Waktu Kuliah Sambil Kerja', 'SLoFzNhIpNw'],
                ['text', 'Rangkuman: Menyusun Jadwal Harian', $this->html('jadwal')],
            ],
        ], [
            'Bab 1: Mengatur Prioritas' => [
                'pre' => [
                    ['true_false', 'Semua tugas sebaiknya dikerjakan sesuai urutan datangnya.', ['Benar' => false, 'Salah' => true]],
                    ['single', 'Tugas yang penting dan mendesak sebaiknya...', [
                        'Dikerjakan sekarang' => true,
                        'Dijadwalkan minggu depan' => false,
                        'Diabaikan' => false,
                    ]],
                ],
                'post' => [
                    ['multiple', 'Mana saja yang membantu mengatur prioritas? (pilih semua yang benar)', [
                        'Membuat daftar tugas' => true,
                        'Memisahkan tugas penting dan mendesak' => true,
                        'Mengerjakan semua tugas sekaligus' => false,
                    ]],
                    ['essay', 'Apa yang kamu lakukan dengan tugas yang mendesak tetapi tidak penting?', 'Didelegasikan atau diserahkan ke orang lain, atau dikerjakan secepatnya tanpa menghabiskan banyak waktu.'],
                ],
            ],
            'Bab 2: Jadwal Kerja dan Kuliah' => [
                'pre' => [
                    ['true_false', 'Jadwal harian yang terlalu padat justru sering gagal dijalankan.', ['Benar' => true, 'Salah' => false]],
                    ['single', 'Kapan waktu terbaik menyusun jadwal untuk besok?', [
                        'Malam sebelumnya' => true,
                        'Saat sudah terlambat' => false,
                        'Tidak perlu disusun' => false,
                    ]],
                ],
                'post' => [
                    ['single', 'Kenapa perlu menyisakan waktu kosong di jadwal?', [
                        'Untuk cadangan kalau ada tugas yang molor atau mendadak' => true,
                        'Supaya bisa menunda pekerjaan' => false,
                        'Karena jadwal harus selalu kosong' => false,
                    ]],
                    ['essay', 'Sebutkan satu cara membagi waktu antara kerja dan kuliah.', 'Menyusun jadwal tetap untuk kerja, kuliah, dan belajar, lalu mengerjakan tugas sesuai prioritas dan memanfaatkan waktu luang seperti perjalanan untuk mengulang materi.'],
                ],
            ],
        ]);
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
            'problem-solving' => '<h2>Langkah Menyelesaikan Masalah</h2>
<ol><li><strong>Pahami masalahnya.</strong> Tulis dengan jelas apa yang sebenarnya terjadi.</li><li><strong>Cari penyebabnya.</strong> Tanyakan "kenapa" sampai menemukan akar masalah.</li><li><strong>Kumpulkan beberapa solusi.</strong> Jangan berhenti di ide pertama.</li><li><strong>Pilih dan jalankan.</strong> Ambil solusi terbaik, lalu evaluasi hasilnya.</li></ol>
<blockquote>Masalah yang dipahami dengan jelas sudah setengah terselesaikan.</blockquote>',
            'percaya-diri' => '<h2>Tampil Percaya Diri</h2>
<ul><li><strong>Power pose:</strong> berdiri tegak dengan tangan di pinggang selama beberapa saat sebelum tampil.</li><li><strong>Berlatih dulu:</strong> latihan membuat kamu lebih siap dan tenang.</li><li><strong>Jaga kontak mata:</strong> lihat audiens, bukan lantai atau layar.</li></ul>
<p>Rasa gugup itu wajar. Yang penting, gugupnya tidak menghentikanmu untuk mulai berbicara.</p>',
            'pemrograman' => '<h2>Apa Itu Pemrograman?</h2>
<p>Pemrograman adalah proses menulis <strong>instruksi untuk komputer</strong> supaya komputer mengerjakan tugas tertentu. Instruksi itu ditulis dalam <strong>bahasa pemrograman</strong> seperti PHP, JavaScript, atau Python.</p>
<h3>Algoritma dulu, kode kemudian</h3>
<p>Sebelum menulis kode, programmer menyusun <strong>algoritma</strong>: urutan langkah yang jelas untuk menyelesaikan masalah. Contoh algoritma membuat teh:</p>
<ol><li>Rebus air sampai mendidih.</li><li>Masukkan teh celup ke gelas.</li><li>Tuang air panas ke gelas.</li><li>Tambahkan gula, lalu aduk.</li></ol>
<p>Komputer tidak bisa menebak maksud kita, jadi setiap langkah harus <strong>berurutan, jelas, dan punya akhir</strong>.</p>
<blockquote>Algoritma adalah rencananya, program adalah rencana itu yang ditulis dalam bahasa yang dimengerti komputer.</blockquote>',
            'variabel' => '<h2>Variabel dan Tipe Data</h2>
<p><strong>Variabel</strong> adalah wadah bernama untuk menyimpan data. Bayangkan kotak berlabel: labelnya nama variabel, isinya nilai.</p>
<pre>nama = "Budi"
umur = 20
umur = umur + 1   // sekarang umur bernilai 21</pre>
<p>Nilai variabel bisa diubah selama program berjalan, seperti <code>umur</code> pada contoh di atas.</p>
<h3>Tipe data yang paling sering dipakai</h3>
<ul><li><strong>String</strong>: teks, misalnya "Budi" atau "Halo".</li><li><strong>Integer</strong>: bilangan bulat, misalnya 20 atau -5.</li><li><strong>Float</strong>: bilangan desimal, misalnya 3.14.</li><li><strong>Boolean</strong>: hanya true (benar) atau false (salah).</li></ul>
<p>Memilih tipe data yang tepat membuat program lebih mudah dibaca dan tidak salah hitung.</p>',
            'kontrol' => '<h2>Rangkuman Percabangan dan Perulangan</h2>
<h3>Percabangan: memilih jalan</h3>
<p>Percabangan dipakai ketika program harus memilih tindakan berdasarkan kondisi.</p>
<pre>if (nilai >= 70) {
    tampilkan("Lulus")
} else {
    tampilkan("Coba lagi")
}</pre>
<h3>Perulangan: mengulang pekerjaan</h3>
<p>Perulangan dipakai untuk menjalankan perintah yang sama berkali-kali tanpa menulis ulang kodenya.</p>
<pre>for (i = 1; i <= 5; i++) {
    tampilkan(i)
}</pre>
<ul><li><strong>for</strong>: dipakai kalau jumlah pengulangannya sudah diketahui.</li><li><strong>while</strong>: dipakai selama sebuah kondisi masih bernilai benar.</li></ul>',
            'javascript' => '<h2>Pengenalan JavaScript</h2>
<p><strong>JavaScript</strong> adalah bahasa pemrograman yang membuat halaman web menjadi <strong>interaktif</strong>: tombol yang bisa diklik, menu yang terbuka, form yang dicek sebelum dikirim, sampai animasi.</p>
<h3>Di mana JavaScript berjalan?</h3>
<ul><li><strong>Di browser</strong>: hampir semua situs web memakai JavaScript.</li><li><strong>Di server</strong>: lewat Node.js, JavaScript juga bisa dipakai untuk backend.</li></ul>
<h3>Contoh sederhana</h3>
<pre>let nama = "Budi";
alert("Halo, " + nama + "!");</pre>
<p>Bersama HTML (struktur) dan CSS (tampilan), JavaScript (perilaku) adalah tiga bahan utama sebuah halaman web.</p>',
            'framework' => '<h2>Apa Itu Framework?</h2>
<p><strong>Framework</strong> adalah kerangka kerja berisi struktur dan fitur siap pakai, supaya developer tidak perlu menulis semuanya dari nol.</p>
<h3>Kenapa memakai framework?</h3>
<ul><li><strong>Lebih cepat</strong>: fitur umum seperti routing, koneksi database, dan login sudah tersedia.</li><li><strong>Lebih rapi</strong>: struktur folder dan cara menulis kode sudah disepakati, sehingga mudah dikerjakan bersama tim.</li><li><strong>Lebih aman</strong>: perlindungan dasar seperti CSRF dan validasi input sudah disediakan.</li></ul>
<h3>Contoh framework</h3>
<ul><li>PHP: <strong>Laravel</strong>, <strong>CodeIgniter</strong></li><li>JavaScript: Express, Next.js</li><li>Python: Django, Flask</li></ul>
<p>Hati-hati: React dan Tailwind CSS sering disebut bersamaan, tetapi React adalah library JavaScript untuk tampilan dan Tailwind adalah framework CSS. Keduanya bukan framework PHP.</p>',
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
            'prioritas' => '<h2>Matriks Prioritas</h2>
<p>Bagi semua tugasmu ke empat kotak berdasarkan <strong>penting</strong> dan <strong>mendesak</strong>:</p>
<ul><li><strong>Penting dan mendesak:</strong> kerjakan sekarang.</li><li><strong>Penting, tidak mendesak:</strong> jadwalkan, misalnya belajar untuk ujian minggu depan.</li><li><strong>Mendesak, tidak penting:</strong> delegasikan atau selesaikan secepatnya.</li><li><strong>Tidak penting, tidak mendesak:</strong> hapus dari daftar.</li></ul>
<blockquote>Sibuk belum tentu produktif. Yang penting dikerjakan lebih dulu.</blockquote>',
            'jadwal' => '<h2>Menyusun Jadwal Harian</h2>
<ol><li><strong>Susun malam sebelumnya.</strong> Pagi hari langsung tahu harus mulai dari mana.</li><li><strong>Kunci jadwal tetap dulu:</strong> jam kerja dan jam kuliah.</li><li><strong>Isi sisanya dengan 3 tugas utama.</strong> Jangan lebih, supaya realistis.</li><li><strong>Sisakan waktu kosong</strong> untuk tugas yang molor atau mendadak.</li></ol>
<p>Waktu perjalanan atau istirahat bisa dipakai untuk mengulang materi kuliah secara singkat.</p>',
            'channels' => '<h2>Rangkuman: Channel Digital Marketing</h2>
<p>Digital marketing adalah pemasaran produk atau jasa melalui <strong>channel digital</strong>. Channel yang paling umum:</p>
<ul><li><strong>Media sosial:</strong> Instagram, TikTok, LinkedIn, Facebook, untuk membangun awareness dan komunitas.</li><li><strong>SEO:</strong> membuat website muncul di hasil pencarian Google secara organik.</li><li><strong>Iklan berbayar:</strong> Meta Ads, Google Ads, untuk menjangkau audiens lebih cepat.</li><li><strong>Email marketing:</strong> menjaga hubungan dengan pelanggan yang sudah kenal brand-mu.</li></ul>
<blockquote>Tidak perlu menguasai semua channel sekaligus. Pilih satu, pelajari sampai paham, baru tambah channel berikutnya.</blockquote>',
        };
    }
}
