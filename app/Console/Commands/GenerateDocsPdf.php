<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class GenerateDocsPdf extends Command
{
    protected $signature = 'docs:generate';

    protected $description = 'Generate PDF dokumentasi fitur admin Ray Academy';

    public function handle()
    {
        $this->info('Generating PDF dokumentasi...');

        $data = [
            'generated_at' => now()->isoFormat('D MMMM YYYY HH:mm'),
            'features' => $this->getFeatures(),
        ];

        $html = view('pdf.docs', $data)->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Helvetica',
            'defaultPaperSize' => 'A4',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 25,
            'margin_bottom' => 30,
        ]);

        $path = storage_path('app/public/docs-sistem-rayacademy.pdf');
        $pdf->save($path);

        $this->info("PDF berhasil dibuat: $path");
    }

    private function getFeatures(): array
    {
        return [
            [
                'section' => 'Dashboard Admin',
                'items' => [[
                    'name' => 'Ringkasan Statistik',
                    'desc' => 'Halaman utama setelah login admin. Menampilkan kartu statistik: total kursus, total user, total enrollment, pembayaran pending, statistik artikel (total artikel, terjadwal, terbit hari ini, siap terbit).',
                    'howto' => 'Login dengan role Admin/Super Admin. Setelah redirect ke /admin, dashboard otomatis menampilkan semua ringkasan.',
                    'fields' => [],
                    'notes' => 'Konten dashboard berbeda per role: Admin/Super Admin melihat semua statistik. Instruktur hanya melihat kursus miliknya. Content Manager hanya melihat statistik artikel.',
                ]],
            ],
            [
                'section' => 'Manajemen Kursus',
                'items' => [
                    [
                        'name' => 'Daftar Kursus',
                        'desc' => 'Menampilkan semua kursus dalam tabel dengan pagination 10 data per halaman. Setiap baris menampilkan judul, kategori, tipe, harga, level, dan tombol aksi (Lihat, Edit, Hapus).',
                        'howto' => 'Buka menu Course Management > Manage Courses. Gunakan pagination di bawah tabel untuk navigasi halaman.',
                        'fields' => [],
                        'notes' => 'Permission required: view courses',
                    ],
                    [
                        'name' => 'Tambah Kursus Baru',
                        'desc' => 'Form untuk menambahkan kursus baru ke sistem.',
                        'howto' => 'Klik tombol "Tambah Kursus" di halaman daftar kursus. Isi semua field wajib lalu klik Simpan.',
                        'fields' => [
                            ['label' => 'Judul Kursus', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama kursus yang akan ditampilkan ke user'],
                            ['label' => 'Instruktur', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama pengajar/instruktur kursus'],
                            ['label' => 'Deskripsi', 'type' => 'textarea', 'required' => true, 'desc' => 'Penjelasan lengkap tentang kursus'],
                            ['label' => 'Tipe Kursus', 'type' => 'select', 'required' => true, 'options' => ['paid (Berbayar)', 'free (Gratis)'], 'desc' => 'Jika free, harga otomatis menjadi 0'],
                            ['label' => 'Harga', 'type' => 'number', 'required' => false, 'desc' => 'Harga dalam Rupiah. Hanya diisi jika tipe paid'],
                            ['label' => 'Thumbnail', 'type' => 'file', 'required' => false, 'options' => ['Format: jpeg, png, jpg, gif, webp', 'Max: 2MB'], 'desc' => 'Gambar sampul kursus'],
                            ['label' => 'Trailer Video ID', 'type' => 'text', 'required' => false, 'desc' => 'ID YouTube video trailer kursus'],
                            ['label' => 'Kategori', 'type' => 'select', 'required' => true, 'desc' => 'Pilih kategori kursus yang tersedia'],
                            ['label' => 'Level', 'type' => 'select', 'required' => true, 'options' => ['Beginner', 'Intermediate', 'Advanced'], 'desc' => 'Tingkat kesulitan kursus'],
                            ['label' => 'Benefit', 'type' => 'textarea', 'required' => false, 'desc' => 'Keuntungan yang didapat peserta setelah mengikuti kursus'],
                            ['label' => 'Topik Pratinjau', 'type' => 'textarea', 'required' => false, 'desc' => 'Preview topik-topik yang akan dipelajari'],
                            ['label' => 'Jadwal Mulai', 'type' => 'date', 'required' => false, 'desc' => 'Tanggal mulai kursus'],
                            ['label' => 'Jadwal Selesai', 'type' => 'date', 'required' => false, 'desc' => 'Tanggal selesai kursus (harus setelah atau sama dengan jadwal mulai)'],
                            ['label' => 'Platform Meeting', 'type' => 'text', 'required' => false, 'max' => 100, 'desc' => 'Platform yang digunakan (Zoom, Google Meet, dll)'],
                        ],
                        'notes' => 'Permission required: create courses. Slug dibuat otomatis dari judul.',
                    ],
                    [
                        'name' => 'Detail Kursus',
                        'desc' => 'Halaman detail menampilkan semua informasi kursus termasuk thumbnail, deskripsi, benefit, dan daftar pelajaran.',
                        'howto' => 'Klik tombol "Lihat" pada baris kursus yang diinginkan di halaman daftar.',
                        'fields' => [],
                        'notes' => '',
                    ],
                    [
                        'name' => 'Edit Kursus',
                        'desc' => 'Mengubah data kursus yang sudah ada. Field sama seperti tambah kursus.',
                        'howto' => 'Klik tombol "Edit" pada kursus yang ingin diubah. Ubah field yang diperlukan lalu klik Simpan. Jika tidak upload thumbnail baru, thumbnail lama tetap dipakai.',
                        'fields' => [],
                        'notes' => 'Permission required: edit courses. Slug tidak berubah meskipun judul diubah (onUpdate: false).',
                    ],
                    [
                        'name' => 'Hapus Kursus',
                        'desc' => 'Menghapus kursus dari sistem.',
                        'howto' => 'Klik tombol "Hapus" pada kursus yang ingin dihapus. Konfirmasi penghapusan.',
                        'fields' => [],
                        'notes' => 'Permission required: delete courses',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Kategori Kursus',
                'items' => [[
                    'name' => 'CRUD Kategori Kursus',
                    'desc' => 'Mengelola kategori untuk mengelompokkan kursus. Menampilkan daftar kategori dengan jumlah kursus di masing-masing kategori.',
                    'howto' => 'Buka menu Course Management > Course Categories. Klik Tambah untuk membuat kategori baru, atau klik Edit untuk mengubah kategori yang ada.',
                    'fields' => [
                        ['label' => 'Nama Kategori', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama kategori (harus unik)'],
                        ['label' => 'Deskripsi', 'type' => 'textarea', 'required' => false, 'desc' => 'Penjelasan singkat tentang kategori'],
                    ],
                    'notes' => 'Slug dibuat otomatis dari nama kategori.',
                ]],
            ],
            [
                'section' => 'Manajemen Pelajaran (Lessons)',
                'items' => [
                    [
                        'name' => 'Daftar Pelajaran',
                        'desc' => 'Menampilkan semua pelajaran dalam tabel. Bisa difilter berdasarkan kursus tertentu menggunakan parameter ?course_id=.',
                        'howto' => 'Buka menu Course Management > Manage Lessons. Gunakan dropdown filter kursus untuk menyaring pelajaran per kursus.',
                        'fields' => [],
                        'notes' => 'Permission required: view lessons',
                    ],
                    [
                        'name' => 'Tambah Pelajaran Baru',
                        'desc' => 'Menambahkan pelajaran/video baru ke dalam kursus.',
                        'howto' => 'Klik "Tambah Pelajaran". Pilih kursus tujuan, isi judul dan YouTube video ID, tentukan modul dan urutan, lalu Simpan.',
                        'fields' => [
                            ['label' => 'Kursus', 'type' => 'select', 'required' => true, 'desc' => 'Pilih kursus tempat pelajaran ini berada'],
                            ['label' => 'Judul Pelajaran', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama pelajaran'],
                            ['label' => 'YouTube Video ID', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'ID video YouTube (contoh: dQw4w9WgXcQ)'],
                            ['label' => 'Modul', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama modul/bab pengelompokan pelajaran'],
                            ['label' => 'Urutan', 'type' => 'number', 'required' => true, 'desc' => 'Nomor urut pelajaran dalam kursus'],
                            ['label' => 'Durasi', 'type' => 'text', 'required' => false, 'max' => 255, 'desc' => 'Durasi video (contoh: 15:30)'],
                            ['label' => 'Preview Gratis', 'type' => 'checkbox', 'required' => false, 'desc' => 'Centang jika pelajaran ini bisa ditonton tanpa login'],
                        ],
                        'notes' => 'Pelajaran diurutkan berdasarkan field order (ASC).',
                    ],
                    [
                        'name' => 'Detail, Edit & Hapus Pelajaran',
                        'desc' => 'Lihat detail pelajaran, edit semua field, atau hapus pelajaran.',
                        'howto' => 'Klik Lihat untuk detail. Klik Edit untuk mengubah data. Klik Hapus untuk menghapus pelajaran.',
                        'fields' => [],
                        'notes' => '',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Artikel',
                'items' => [
                    [
                        'name' => 'Daftar Artikel',
                        'desc' => 'Tabel semua artikel diurutkan berdasarkan status, jadwal terbit, dan tanggal dibuat. Pagination 10 per halaman. Eager load kategori dan tags.',
                        'howto' => 'Buka menu Content Management > Manage Articles. Gunakan pagination atau filter untuk navigasi.',
                        'fields' => [],
                        'notes' => 'Permission required: view articles',
                    ],
                    [
                        'name' => 'Tambah Artikel Baru',
                        'desc' => 'Form lengkap untuk membuat artikel dengan dua format konten: WordPress (block editor) atau Rich Text (Trix editor).',
                        'howto' => 'Klik "Tambah Artikel". Pilih format konten terlebih dahulu (WordPress atau Rich Text). Isi semua field wajib, pilih minimal 1 kategori, lalu Simpan.',
                        'fields' => [
                            ['label' => 'Judul', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Judul artikel'],
                            ['label' => 'Slug', 'type' => 'text', 'required' => false, 'max' => 255, 'desc' => 'URL slug. Jika dikosongkan, dibuat otomatis dari judul (hanya huruf kecil dan tanda strip)'],
                            ['label' => 'Format Konten', 'type' => 'select', 'required' => true, 'options' => ['wordpress (WordPress)', 'rich_text (Rich Text / Trix)'], 'desc' => 'Pilih editor konten'],
                            ['label' => 'Konten (WordPress)', 'type' => 'textarea', 'required' => true, 'if' => 'content_format = wordpress', 'desc' => 'Konten dalam format WordPress blocks'],
                            ['label' => 'Body (Rich Text)', 'type' => 'textarea', 'required' => true, 'if' => 'content_format = rich_text', 'desc' => 'Konten menggunakan Trix editor (WYSIWYG)'],
                            ['label' => 'Thumbnail', 'type' => 'file', 'required' => false, 'options' => ['Format: jpeg, png, jpg, gif, webp', 'Max: 2MB'], 'desc' => 'Gambar sampul artikel'],
                            ['label' => 'Penulis', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama penulis. Jika dikosongkan, otomatis pakai nama user yang login'],
                            ['label' => 'Excerpt', 'type' => 'textarea', 'required' => false, 'desc' => 'Cuplikan/ringkasan singkat artikel'],
                            ['label' => 'Tipe Post', 'type' => 'text', 'required' => false, 'max' => 255, 'desc' => 'Tipe post (default: post)'],
                            ['label' => 'Kategori', 'type' => 'multi-select', 'required' => true, 'desc' => 'Pilih minimal 1 kategori artikel'],
                            ['label' => 'Tags', 'type' => 'multi-select', 'required' => false, 'desc' => 'Tags artikel. Bisa mengetik tag baru yang akan otomatis dibuat'],
                            ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['draft', 'published', 'scheduled'], 'desc' => 'draft = simpan konsep, published = terbitkan sekarang, scheduled = jadwalkan terbit'],
                            ['label' => 'Jadwal Terbit', 'type' => 'datetime', 'required' => true, 'if' => 'status = scheduled', 'desc' => 'Tanggal dan jam artikel akan otomatis terbit'],
                            ['label' => 'Rekomendasikan', 'type' => 'checkbox', 'required' => false, 'desc' => 'Centang untuk menandai artikel sebagai rekomendasi'],
                            ['label' => 'Hero Slider Order', 'type' => 'number', 'required' => false, 'options' => ['Min: 1', 'Max: 5'], 'desc' => 'Posisi di hero slider homepage (1-5, harus unik)'],
                        ],
                        'notes' => 'Permission required: create articles. Slug auto-generated dari judul. Tags bisa dibuat langsung saat input (create on the fly).',
                    ],
                    [
                        'name' => 'Detail Artikel',
                        'desc' => 'Menampilkan artikel lengkap dengan rich text content, kategori, dan tags.',
                        'howto' => 'Klik "Lihat" pada artikel yang diinginkan.',
                        'fields' => [],
                        'notes' => '',
                    ],
                    [
                        'name' => 'Edit Artikel',
                        'desc' => 'Mengubah artikel yang sudah ada. Field sama seperti tambah artikel. Jadwal terbit hanya divalidasi jika diubah (tidak akan error jika menyimpan tanpa mengubah jadwal yang sudah lewat).',
                        'howto' => 'Klik "Edit" pada artikel. Ubah data yang diperlukan. Status bisa diubah dari draft ke published/scheduled atau sebaliknya.',
                        'fields' => [],
                        'notes' => 'Jika status diubah ke published, published_at otomatis diisi. Jika status diubah dari published, published_at dikosongkan. Jika artikel tidak lagi direkomendasikan, recommended_at dihapus.',
                    ],
                    [
                        'name' => 'Publikasi & Jadwal',
                        'desc' => 'Tombol aksi cepat untuk artikel dengan status scheduled:',
                        'howto' => 'Publish: Terbitkan artikel terjadwal segera (tombol Publish). Unschedule: Kembalikan artikel terjadwal ke draft (tombol Unschedule).',
                        'fields' => [],
                        'notes' => '',
                    ],
                    [
                        'name' => 'Hapus Artikel',
                        'desc' => 'Menghapus artikel beserta relasi kategori dan tags.',
                        'howto' => 'Klik "Hapus" pada artikel yang ingin dihapus. Konfirmasi penghapusan.',
                        'fields' => [],
                        'notes' => 'Permission required: delete articles',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Kategori Artikel',
                'items' => [[
                    'name' => 'CRUD Kategori Artikel',
                    'desc' => 'Mengelola kategori artikel. Tersedia 6 kategori default: Am-AI-Zing (AI), Do Better Class (pengembangan diri), Psikologi Bisnis, Marketing & Sales, Sekolah Kosmetik Indonesia, Sobat Anak (parenting).',
                    'howto' => 'Buka menu Content Management > Article Categories. Klik Tambah untuk kategori baru. Klik Edit untuk mengubah.',
                    'fields' => [
                        ['label' => 'Nama Kategori', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama kategori (harus unik)'],
                        ['label' => 'Deskripsi', 'type' => 'textarea', 'required' => false, 'desc' => 'Penjelasan kategori'],
                    ],
                    'notes' => 'Slug dibuat otomatis dari nama. Setiap kategori menampilkan jumlah artikel di dalamnya.',
                ]],
            ],
            [
                'section' => 'Manajemen Tags',
                'items' => [[
                    'name' => 'CRUD Tags',
                    'desc' => 'Mengelola tags untuk artikel. Setiap tag menampilkan jumlah artikel yang menggunakan tag tersebut.',
                    'howto' => 'Buka menu Content Management > Tags. Klik Tambah untuk tag baru. Klik Edit untuk mengubah nama tag.',
                    'fields' => [
                        ['label' => 'Nama Tag', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama tag (harus unik)'],
                    ],
                    'notes' => 'Slug dibuat otomatis dari nama.',
                ]],
            ],
            [
                'section' => 'Hero Slider',
                'items' => [[
                    'name' => 'Atur Hero Slider',
                    'desc' => 'Memilih hingga 5 artikel untuk ditampilkan di hero slider pada halaman utama (homepage). Slider menampilkan artikel unggulan secara bergilir.',
                    'howto' => 'Buka menu Content Management > Hero Slider. Pilih artikel dan tentukan urutan (1-5) untuk masing-masing artikel. Klik Simpan. Untuk menghapus artikel dari slider, klik tombol Hapus pada kartu artikel.',
                    'fields' => [
                        ['label' => 'Artikel', 'type' => 'multi-select', 'required' => true, 'desc' => 'Pilih artikel (maksimal 5)'],
                        ['label' => 'Urutan', 'type' => 'number', 'required' => true, 'options' => ['Min: 1', 'Max: 5'], 'desc' => 'Posisi tampil di slider (harus unik, tidak boleh ada urutan yang sama)'],
                    ],
                    'notes' => 'Proses update berjalan dalam transaksi database. Semua urutan lama dihapus lalu diatur ulang. Menampilkan hari sejak terakhir update slider.',
                ]],
            ],
            [
                'section' => 'Manajemen Pembayaran (Enrollments)',
                'items' => [
                    [
                        'name' => 'Daftar Enrollment',
                        'desc' => 'Menampilkan dua bagian: (1) Enrollment pending dengan payment_status = pending yang membutuhkan persetujuan, (2) Semua enrollment diurutkan dari terbaru.',
                        'howto' => 'Buka menu System Management > Manage Payments. Lihat daftar enrollment pending di bagian atas. Jika ada pembayaran yang perlu disetujui, klik tombol Approve.',
                        'fields' => [],
                        'notes' => 'Permission required: manage enrollments. Setiap baris menampilkan nama user, kursus, status pembayaran, dan tanggal enrollment.',
                    ],
                    [
                        'name' => 'Setujui Pembayaran',
                        'desc' => 'Mengkonfirmasi pembayaran manual untuk enrollment yang masih pending.',
                        'howto' => 'Pada baris enrollment pending, klik tombol "Approve". Sistem akan mengubah payment_status menjadi "completed" dan status enrollment menjadi "active". User akan mendapat akses penuh ke kursus.',
                        'fields' => [],
                        'notes' => 'Setelah di-approve, user bisa mengakses semua materi kursus. Untuk pembayaran via Midtrans (Snap), status otomatis terupdate tanpa perlu approve manual.',
                    ],
                    [
                        'name' => 'Alur Pembayaran Midtrans (Snap)',
                        'desc' => 'Pembayaran otomatis melalui Midtrans Snap popup. User tidak perlu menunggu approve admin.',
                        'howto' => 'User klik "Mulai Kelas" atau "Beli Kelas" di halaman detail kursus. System membuat enrollment dengan snap_token dan menampilkan popup Midtrans. User memilih metode pembayaran (kartu kredit, transfer bank, QRIS, dll). Setelah bayar sukses, system langsung mengupdate status enrollment menjadi active.',
                        'fields' => [],
                        'notes' => 'Saat ini menggunakan mode sandbox untuk testing. Midtrans mendukung: kartu kredit, mandiri billpay, BCA KlikPay, BRI Epay, CIMB Clicks, Danamon Online, Akulaku, Indomaret, Alfamart, QRIS.',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Pengguna (Users)',
                'items' => [
                    [
                        'name' => 'Daftar Pengguna',
                        'desc' => 'Menampilkan semua user (kecuali Super Admin) dengan eager load roles. Pagination 20 per halaman.',
                        'howto' => 'Buka menu System Management > Manage Users. Cari user menggunakan pagination.',
                        'fields' => [],
                        'notes' => 'Permission required: view users. Super Admin tidak tampil di daftar ini.',
                    ],
                    [
                        'name' => 'Tambah Pengguna Baru',
                        'desc' => 'Membuat akun user baru dengan role tertentu.',
                        'howto' => 'Klik "Tambah User". Isi semua field wajib, pilih role, lalu Simpan.',
                        'fields' => [
                            ['label' => 'Nama', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama lengkap user'],
                            ['label' => 'Username', 'type' => 'text', 'required' => true, 'max' => 20, 'desc' => 'Username (hanya huruf, angka, strip, underscore. Harus unik)'],
                            ['label' => 'Email', 'type' => 'email', 'required' => true, 'max' => 255, 'desc' => 'Alamat email (harus unik)'],
                            ['label' => 'Password', 'type' => 'password', 'required' => true, 'min' => 8, 'desc' => 'Password minimal 8 karakter'],
                            ['label' => 'Konfirmasi Password', 'type' => 'password', 'required' => true, 'desc' => 'Ketik ulang password yang sama'],
                            ['label' => 'Role', 'type' => 'select', 'required' => true, 'options' => ['student', 'instructor', 'content-manager', 'admin'], 'desc' => 'Role yang menentukan akses fitur'],
                        ],
                        'notes' => 'Password di-hash otomatis sebelum disimpan.',
                    ],
                    [
                        'name' => 'Detail, Edit & Hapus Pengguna',
                        'desc' => 'Lihat detail user. Edit data user atau hapus user.',
                        'howto' => 'Edit: Ubah field yang diperlukan. Password hanya diisi jika ingin mengganti. Ganti role untuk mengubah akses. Hapus: User biasa bisa dihapus, Super Admin dan diri sendiri tidak bisa.',
                        'fields' => [
                            ['label' => 'Nama', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama lengkap user'],
                            ['label' => 'Username', 'type' => 'text', 'required' => true, 'max' => 20, 'desc' => 'Username (hanya huruf, angka, strip, underscore)'],
                            ['label' => 'Email', 'type' => 'email', 'required' => true, 'max' => 255, 'desc' => 'Alamat email'],
                            ['label' => 'Password', 'type' => 'password', 'required' => false, 'min' => 8, 'desc' => 'Kosongkan jika tidak ingin ganti password'],
                            ['label' => 'Role', 'type' => 'select', 'required' => true, 'desc' => 'Role yang menentukan akses fitur'],
                        ],
                        'notes' => '',
                    ],
                ],
            ],
            [
                'section' => 'Role & Permission',
                'items' => [
                    [
                        'name' => 'Daftar Role',
                        'desc' => 'Menampilkan semua role dengan jumlah user di masing-masing role. Bisa menampilkan role yang sudah dihapus (soft delete) dengan parameter ?show_deleted=1.',
                        'howto' => 'Buka menu System Management > Roles & Permissions. Lihat daftar role. Role system (super-admin, student) tidak bisa dihapus.',
                        'fields' => [],
                        'notes' => 'Permission required: manage roles and permissions',
                    ],
                    [
                        'name' => 'Tambah Role Baru',
                        'desc' => 'Membuat role kustom dengan permission yang bisa diatur.',
                        'howto' => 'Klik "Tambah Role". Isi nama dan deskripsi. Setelah simpan, edit role untuk mengatur permission matrix.',
                        'fields' => [
                            ['label' => 'Nama Role', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama role (harus unik)'],
                            ['label' => 'Deskripsi', 'type' => 'textarea', 'required' => false, 'max' => 1000, 'desc' => 'Penjelasan role'],
                        ],
                        'notes' => '',
                    ],
                    [
                        'name' => 'Edit Role & Permission Matrix',
                        'desc' => 'Mengatur permission untuk role melalui permission matrix yang dikelompokkan berdasarkan fitur.',
                        'howto' => 'Klik "Edit" pada role. Atur permission dengan mencentang kotak pada matrix. Permission dikelompokkan: Course Management, Lesson Management, Article Management, User Management, Enrollment Management, Category Management, Tag Management, Admin Panel, System Management, Student Features.',
                        'fields' => [
                            ['label' => 'Nama Role', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nama role'],
                            ['label' => 'Deskripsi', 'type' => 'textarea', 'required' => false, 'max' => 1000, 'desc' => 'Penjelasan role'],
                            ['label' => 'Permissions', 'type' => 'checkbox-matrix', 'required' => false, 'desc' => 'Centang permission yang ingin diberikan ke role ini'],
                        ],
                        'notes' => 'Protected role (super-admin, student) tidak bisa dihapus. Critical permissions pada role tertentu tidak bisa dicabut. Aktivitas role dicatat di Activity Log.',
                    ],
                    [
                        'name' => 'Hapus Role',
                        'desc' => 'Menghapus role (soft delete) dari sistem.',
                        'howto' => 'Klik "Hapus" pada role. Role dengan user aktif tidak bisa dihapus. Role yang dilindungi system tidak bisa dihapus.',
                        'fields' => [],
                        'notes' => '',
                    ],
                ],
            ],
            [
                'section' => 'Pengaturan Situs',
                'items' => [[
                    'name' => 'Pengaturan Umum',
                    'desc' => 'Mengelola informasi kontak dan tautan media sosial yang ditampilkan di website.',
                    'howto' => 'Buka menu Content Management > Site Settings. Ubah field yang diperlukan lalu klik Simpan. Cache website otomatis dibersihkan setiap kali settings diupdate.',
                    'fields' => [
                        ['label' => 'Email Kontak', 'type' => 'email', 'required' => true, 'desc' => 'Alamat email yang ditampilkan untuk kontak'],
                        ['label' => 'Telepon', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Nomor telepon kontak'],
                        ['label' => 'Alamat', 'type' => 'text', 'required' => true, 'max' => 500, 'desc' => 'Alamat fisik perusahaan'],
                        ['label' => 'Facebook', 'type' => 'url', 'required' => false, 'desc' => 'URL halaman Facebook'],
                        ['label' => 'Twitter / X', 'type' => 'url', 'required' => false, 'desc' => 'URL halaman Twitter'],
                        ['label' => 'Instagram', 'type' => 'url', 'required' => false, 'desc' => 'URL halaman Instagram'],
                        ['label' => 'YouTube', 'type' => 'url', 'required' => false, 'desc' => 'URL channel YouTube'],
                        ['label' => 'TikTok', 'type' => 'url', 'required' => false, 'desc' => 'URL halaman TikTok'],
                        ['label' => 'WhatsApp', 'type' => 'url', 'required' => false, 'desc' => 'URL/nomor WhatsApp'],
                        ['label' => 'LinkedIn', 'type' => 'url', 'required' => false, 'desc' => 'URL halaman LinkedIn'],
                    ],
                    'notes' => 'Data disimpan di tabel settings sebagai key-value pairs.',
                ]],
            ],
            [
                'section' => 'Share Domains',
                'items' => [[
                    'name' => 'CRUD Share Domains',
                    'desc' => 'Mengelola domain afiliasi/share yang terintegrasi dengan sistem. Setiap domain memiliki API key untuk autentikasi.',
                    'howto' => 'Buka menu System Management > Share Domains. Klik Tambah untuk domain baru. Gunakan tombol Aktif/Nonaktif untuk mengubah status. Klik Regenerate API Key untuk membuat key baru.',
                    'fields' => [
                        ['label' => 'Nama Domain', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'Domain name (harus unik)'],
                        ['label' => 'Webhook URL', 'type' => 'url', 'required' => true, 'max' => 255, 'desc' => 'URL webhook untuk integrasi'],
                        ['label' => 'API Key', 'type' => 'text', 'required' => true, 'max' => 255, 'desc' => 'API key untuk autentikasi'],
                        ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['active', 'inactive'], 'desc' => 'Status domain: active = berjalan, inactive = nonaktif'],
                    ],
                    'notes' => 'Diurutkan berdasarkan status lalu tanggal dibuat. Bisa regenerate API key tanpa perlu hapus domain.',
                ]],
            ],
            [
                'section' => 'Activity Log',
                'items' => [[
                    'name' => 'Log Aktivitas',
                    'desc' => 'Mencatat semua aktivitas yang dilakukan oleh admin di sistem. Menggunakan Spatie Activitylog untuk logging yang komprehensif.',
                    'howto' => 'Buka menu System Management > Activity Log. Gunakan filter untuk mencari: pilih user (causer), atur rentang tanggal (from/to), atau ketik kata kunci deskripsi. Pagination 20 per halaman.',
                    'fields' => [
                        ['label' => 'Filter User', 'type' => 'select', 'required' => false, 'desc' => 'Saring berdasarkan pengguna yang melakukan aksi'],
                        ['label' => 'Dari Tanggal', 'type' => 'date', 'required' => false, 'desc' => 'Awal rentang tanggal'],
                        ['label' => 'Sampai Tanggal', 'type' => 'date', 'required' => false, 'desc' => 'Akhir rentang tanggal'],
                        ['label' => 'Kata Kunci', 'type' => 'text', 'required' => false, 'desc' => 'Cari berdasarkan deskripsi aktivitas'],
                    ],
                    'notes' => 'Setiap log menampilkan: user yang melakukan aksi, deskripsi aktivitas, subject (data yang diubah), dan timestamp. Eager load causer (user) dan subject.',
                ]],
            ],
        ];
    }
}
