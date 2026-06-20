<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            // For Basic Skincare Routine course
            if ($course->title === 'Basic Skincare Routine: From Zero to Hero') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Perkenalan: Kenapa Skincare Penting?',
                    'youtube_video_id' => 'abc1',
                    'module' => 'Modul 1: Fondasi Skincare',
                    'order' => 1,
                    'duration' => 8,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Mengenal Jenis-jenis Kulit',
                    'youtube_video_id' => 'def2',
                    'module' => 'Modul 1: Fondasi Skincare',
                    'order' => 2,
                    'duration' => 12,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Cleansing: Langkah Awal Rutinitas Skincare',
                    'youtube_video_id' => 'ghi3',
                    'module' => 'Modul 2: Langkah-langkah Dasar',
                    'order' => 1,
                    'duration' => 15,
                    'is_preview' => false,
                ]);
            }
            // For Mengenal Bahan Skincare course
            elseif ($course->title === 'Mengenal Bahan Skincare: Retinol, AHA, BHA') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Pengenalan Bahan Aktif dalam Skincare',
                    'youtube_video_id' => 'jkl4',
                    'module' => 'Modul 1: Dasar-dasar Bahan Aktif',
                    'order' => 1,
                    'duration' => 10,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Retinol: Manfaat dan Cara Penggunaan',
                    'youtube_video_id' => 'mno5',
                    'module' => 'Modul 2: Bahan Aktif Spesifik',
                    'order' => 1,
                    'duration' => 18,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'AHA dan BHA: Perbedaan dan Fungsi',
                    'youtube_video_id' => 'pqr6',
                    'module' => 'Modul 2: Bahan Aktif Spesifik',
                    'order' => 2,
                    'duration' => 16,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Cara Menggunakan Bahan Aktif dengan Aman',
                    'youtube_video_id' => 'stu7',
                    'module' => 'Modul 3: Aplikasi dan Penyusunan Rutinitas',
                    'order' => 1,
                    'duration' => 20,
                    'is_preview' => false,
                ]);
            }
            // For Makeup Artistry course
            elseif ($course->title === 'Makeup Artistry: Dasar-dasar Makeup') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Persiapan Kulit Sebelum Makeup',
                    'youtube_video_id' => 'vwx8',
                    'module' => 'Modul 1: Pre-makeup Routine',
                    'order' => 1,
                    'duration' => 12,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Base Makeup: Foundation dan Concealer',
                    'youtube_video_id' => 'yz9',
                    'module' => 'Modul 2: Base Makeup',
                    'order' => 1,
                    'duration' => 22,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Eye Makeup Dasar',
                    'youtube_video_id' => 'abc10',
                    'module' => 'Modul 3: Eye Makeup',
                    'order' => 1,
                    'duration' => 25,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Blush, Highlight, dan Setting Makeup',
                    'youtube_video_id' => 'def11',
                    'module' => 'Modul 4: Finishing Touches',
                    'order' => 1,
                    'duration' => 18,
                    'is_preview' => false,
                ]);
            }
            // For Hair Care course
            elseif ($course->title === 'Hair Care: Perawatan Rambut Kering dan Rusak') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Mengenal Jenis dan Struktur Rambut',
                    'youtube_video_id' => 'ghi12',
                    'module' => 'Modul 1: Dasar Perawatan Rambut',
                    'order' => 1,
                    'duration' => 14,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Shampoo dan Conditioner yang Tepat',
                    'youtube_video_id' => 'jkl13',
                    'module' => 'Modul 2: Produk Perawatan',
                    'order' => 1,
                    'duration' => 16,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Treatment Rambut di Rumah',
                    'youtube_video_id' => 'mno14',
                    'module' => 'Modul 3: Treatment dan Perawatan',
                    'order' => 1,
                    'duration' => 20,
                    'is_preview' => false,
                ]);
            }
            // For Anti-Aging Skincare course
            elseif ($course->title === 'Anti-Aging Skincare: Lawan Tanda-tanda Penuaan') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Faktor Penyebab Penuaan pada Kulit',
                    'youtube_video_id' => 'pqr15',
                    'module' => 'Modul 1: Penuaan dan Penyebabnya',
                    'order' => 1,
                    'duration' => 12,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Bahan Anti-Aging Terbaik',
                    'youtube_video_id' => 'stu16',
                    'module' => 'Modul 2: Bahan dan Produk Anti-Aging',
                    'order' => 1,
                    'duration' => 18,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Rutinitas Anti-Aging Pagi dan Malam',
                    'youtube_video_id' => 'vwx17',
                    'module' => 'Modul 3: Penyusunan Rutinitas',
                    'order' => 1,
                    'duration' => 22,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Treatment Profesional Anti-Aging',
                    'youtube_video_id' => 'yz18',
                    'module' => 'Modul 4: Treatment Profesional',
                    'order' => 1,
                    'duration' => 15,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Case Study: Transformasi Kulit',
                    'youtube_video_id' => 'abc19',
                    'module' => 'Modul 5: Studi Kasus',
                    'order' => 1,
                    'duration' => 25,
                    'is_preview' => false,
                ]);
            }
            // For Natural Beauty course
            elseif ($course->title === 'Natural Beauty: Makeup dengan Bahan Alami') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Mengenal Bahan Alami untuk Makeup',
                    'youtube_video_id' => 'def20',
                    'module' => 'Modul 1: Bahan Alami dan Manfaatnya',
                    'order' => 1,
                    'duration' => 10,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Membuat Produk Makeup Sendiri',
                    'youtube_video_id' => 'ghi21',
                    'module' => 'Modul 2: DIY Produk Makeup',
                    'order' => 1,
                    'duration' => 20,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Aplikasi Makeup Natural',
                    'youtube_video_id' => 'jkl22',
                    'module' => 'Modul 3: Teknik Aplikasi',
                    'order' => 1,
                    'duration' => 18,
                    'is_preview' => false,
                ]);
            }
            // For Sunscreen course
            elseif ($course->title === 'Sunscreen: Panduan Lengkap Perlindungan UV') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Mengenal Radiasi UV dan Dampaknya',
                    'youtube_video_id' => 'mno23',
                    'module' => 'Modul 1: Radiasi UV',
                    'order' => 1,
                    'duration' => 12,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Jenis-jenis Sunscreen dan SPF',
                    'youtube_video_id' => 'pqr24',
                    'module' => 'Modul 2: Jenis dan Pemilihan Sunscreen',
                    'order' => 1,
                    'duration' => 16,
                    'is_preview' => false,
                ]);
            }
            // For Color Theory course
            elseif ($course->title === 'Color Theory dalam Makeup') {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Dasar-dasar Color Theory',
                    'youtube_video_id' => 'stu25',
                    'module' => 'Modul 1: Teori Warna',
                    'order' => 1,
                    'duration' => 14,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Color Matching untuk Warna Kulit',
                    'youtube_video_id' => 'vwx26',
                    'module' => 'Modul 2: Matching Warna',
                    'order' => 1,
                    'duration' => 20,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Color Correction dalam Makeup',
                    'youtube_video_id' => 'yz27',
                    'module' => 'Modul 3: Color Correction',
                    'order' => 1,
                    'duration' => 18,
                    'is_preview' => false,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Praktek Makeup dengan Color Theory',
                    'youtube_video_id' => 'abc28',
                    'module' => 'Modul 4: Praktek dan Aplikasi',
                    'order' => 1,
                    'duration' => 22,
                    'is_preview' => false,
                ]);
            }
            // For any other courses, add generic lessons
            else {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Pengenalan Materi',
                    'youtube_video_id' => 'dQw4w9WgXcQ',
                    'module' => 'Modul 1: Pengenalan',
                    'order' => 1,
                    'duration' => 10,
                    'is_preview' => true,
                ]);

                Lesson::create([
                    'course_id' => $course->id,
                    'title' => 'Materi Utama',
                    'youtube_video_id' => 'dQw4w9WgXcQ',
                    'module' => 'Modul 2: Pembahasan',
                    'order' => 1,
                    'duration' => 15,
                    'is_preview' => false,
                ]);
            }
        }
    }
}
