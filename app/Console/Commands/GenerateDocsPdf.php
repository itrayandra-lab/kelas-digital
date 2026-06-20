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
            'defaultFont' => 'sans-serif',
            'defaultPaperSize' => 'A4',
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
                'icon' => '📊',
                'items' => [
                    [
                        'name' => 'Ringkasan Statistik',
                        'desc' => 'Menampilkan total kursus, user, enrollment, pembayaran pending, statistik artikel (total, terjadwal, terbit hari ini, siap terbit). Konten berbeda berdasarkan role (Admin, Super Admin, Instruktur, Content Manager).',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Kursus',
                'icon' => '🎓',
                'items' => [
                    [
                        'name' => 'Daftar Kursus',
                        'desc' => 'Menampilkan semua kursus dengan pagination (10 per halaman), dilengkapi relasi kategori.',
                    ],
                    [
                        'name' => 'Tambah Kursus',
                        'desc' => 'Form lengkap: judul, instruktur, deskripsi, tipe (berbayar/gratis), harga, thumbnail, trailer video ID, kategori, level (Beginner/Intermediate/Advanced), benefit, topik pratinjau, jadwal mulai-selesai, platform meeting.',
                    ],
                    [
                        'name' => 'Detail & Edit Kursus',
                        'desc' => 'Lihat detail lengkap kursus. Edit semua field termasuk upload ulang thumbnail.',
                    ],
                    [
                        'name' => 'Hapus Kursus',
                        'desc' => 'Hapus kursus dari sistem (memerlukan permission delete courses).',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Kategori Kursus',
                'icon' => '📂',
                'items' => [
                    [
                        'name' => 'CRUD Kategori Kursus',
                        'desc' => 'Tambah, lihat, edit, dan hapus kategori kursus. Setiap kategori menampilkan jumlah kursus di dalamnya.',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Pelajaran (Lessons)',
                'icon' => '📹',
                'items' => [
                    [
                        'name' => 'Daftar Pelajaran',
                        'desc' => 'Daftar semua pelajaran, bisa difilter per kursus (?course_id=), dengan pagination.',
                    ],
                    [
                        'name' => 'Tambah Pelajaran',
                        'desc' => 'Form: pilih kursus, judul, YouTube video ID, modul, urutan, durasi, dan opsi is_preview (bisa ditonton tanpa login).',
                    ],
                    [
                        'name' => 'Detail, Edit & Hapus',
                        'desc' => 'Lihat detail pelajaran, edit semua field, dan hapus pelajaran.',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Artikel',
                'icon' => '📝',
                'items' => [
                    [
                        'name' => 'Daftar Artikel',
                        'desc' => 'Daftar artikel diurutkan berdasarkan status, jadwal, dan tanggal buat. Pagination 10 per halaman. Eager load kategori dan tags.',
                    ],
                    [
                        'name' => 'Tambah Artikel',
                        'desc' => 'Form lengkap: judul, slug (auto), format konten (WordPress / Rich Text), konten, thumbnail, penulis, excerpt, tipe post, kategori (multi-select), tags (create on the fly via Select2), status (draft/published/scheduled), jadwal terbit, is_recommended, hero_slider_order.',
                    ],
                    [
                        'name' => 'Detail & Edit Artikel',
                        'desc' => 'Lihat artikel lengkap dengan rich text. Edit semua field dengan validasi cerdas (scheduled_at hanya divalidasi jika diubah).',
                    ],
                    [
                        'name' => 'Publikasi & Jadwal',
                        'desc' => 'Tombol Publish untuk langsung menerbitkan artikel terjadwal. Tombol Unschedule untuk mengembalikan ke draft.',
                    ],
                    [
                        'name' => 'Hapus Artikel',
                        'desc' => 'Hapus artikel beserta relasi kategori dan tags.',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Kategori Artikel',
                'icon' => '🏷️',
                'items' => [
                    [
                        'name' => 'CRUD Kategori Artikel',
                        'desc' => 'Tambah, lihat, edit, dan hapus kategori artikel. Menampilkan jumlah artikel per kategori. Tersedia 6 kategori: Am-AI-Zing, Do Better Class, Psikologi Bisnis, Marketing & Sales, Sekolah Kosmetik Indonesia, Sobat Anak.',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Tags',
                'icon' => '#️⃣',
                'items' => [
                    [
                        'name' => 'CRUD Tags',
                        'desc' => 'Tambah, lihat, edit, dan hapus tags. Validasi unique name. Menampilkan jumlah artikel per tag.',
                    ],
                ],
            ],
            [
                'section' => 'Hero Slider',
                'icon' => '🖼️',
                'items' => [
                    [
                        'name' => 'Atur Hero Slider',
                        'desc' => 'Pilih hingga 5 artikel untuk ditampilkan di hero slider homepage. Atur urutan (1-5). Tampilkan/hapus artikel dari slider.',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Pembayaran (Enrollments)',
                'icon' => '💳',
                'items' => [
                    [
                        'name' => 'Daftar Enrollment',
                        'desc' => 'Menampilkan enrollment pending (payment_status = pending) terpisah dari semua enrollment. Dilengkapi relasi user dan kursus.',
                    ],
                    [
                        'name' => 'Setujui Pembayaran',
                        'desc' => 'Tombol Approve untuk mengkonfirmasi pembayaran manual. Mengubah status menjadi completed dan enrollment menjadi active.',
                    ],
                ],
            ],
            [
                'section' => 'Manajemen Pengguna (Users)',
                'icon' => '👥',
                'items' => [
                    [
                        'name' => 'Daftar Pengguna',
                        'desc' => 'Daftar semua user (kecuali Super Admin), eager load roles, pagination 20 per halaman.',
                    ],
                    [
                        'name' => 'Tambah Pengguna',
                        'desc' => 'Form: nama, username, email, password, konfirmasi, role (student, instructor, content-manager, admin).',
                    ],
                    [
                        'name' => 'Detail, Edit & Hapus',
                        'desc' => 'Lihat detail user. Edit nama, username, email, role, password opsional. Hapus user (tidak bisa hapus Super Admin atau diri sendiri).',
                    ],
                ],
            ],
            [
                'section' => 'Role & Permission',
                'icon' => '🔐',
                'items' => [
                    [
                        'name' => 'Daftar Role',
                        'desc' => 'Daftar semua role dengan jumlah user. Bisa menampilkan role yang sudah di-soft-delete (?show_deleted=1).',
                    ],
                    [
                        'name' => 'Tambah & Edit Role',
                        'desc' => 'Buat role baru dengan nama dan deskripsi. Edit role dengan permission matrix yang lengkap (Course, Lesson, Article, User, Enrollment, Category, Tag, Admin Panel, System, Student).',
                    ],
                    [
                        'name' => 'Hapus Role',
                        'desc' => 'Soft delete role. Role yang dilindungi (super-admin, student) tidak bisa dihapus. Role dengan user aktif tidak bisa dihapus.',
                    ],
                ],
            ],
            [
                'section' => 'Pengaturan Situs',
                'icon' => '⚙️',
                'items' => [
                    [
                        'name' => 'Pengaturan Umum',
                        'desc' => 'Kelola: email kontak, telepon, alamat, sosial media (Facebook, Twitter, Instagram, YouTube, TikTok, WhatsApp, LinkedIn). Cache otomatis dibersihkan setelah update.',
                    ],
                ],
            ],
            [
                'section' => 'Share Domains',
                'icon' => '🔗',
                'items' => [
                    [
                        'name' => 'CRUD Share Domains',
                        'desc' => 'Kelola domain afiliasi/share: nama domain, webhook URL, API key, status aktif/nonaktif. Fitur: aktivasi/deaktivasi, regenerate API key.',
                    ],
                ],
            ],
            [
                'section' => 'Activity Log',
                'icon' => '📋',
                'items' => [
                    [
                        'name' => 'Log Aktivitas',
                        'desc' => 'Lihat semua aktivitas admin. Filter: pencari (user), rentang tanggal, kata kunci deskripsi. Pagination 20 per halaman. Menggunakan Spatie Activitylog.',
                    ],
                ],
            ],
        ];
    }
}
