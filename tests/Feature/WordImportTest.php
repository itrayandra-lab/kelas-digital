<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseQuestion;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class WordImportTest extends TestCase
{
    use RefreshDatabase;

    private CourseSection $section;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $category = CourseCategory::create(['name' => 'Marketing', 'description' => '-']);
        $course = Course::create([
            'title' => 'Kelas Word', 'instructor' => 'Tim', 'description' => '-', 'course_type' => 'paid', 'price' => 50000,
            'thumbnail' => 'default-course.jpg', 'trailer_video_id' => '', 'course_category_id' => $category->id, 'level' => 'Beginner',
        ]);
        $this->section = CourseSection::create(['course_id' => $course->id, 'title' => 'Modul 02', 'order' => 1]);
        $this->actingAs(User::factory()->create(['username' => 'instruktur'])->assignRole('instructor'));
    }

    public function test_a_word_file_becomes_a_lesson_and_editing_without_a_new_file_keeps_it(): void
    {
        $docx = $this->docx(
            $this->p('Riset Pasar', 'Judul1').$this->p('Pertanyaan riset', 'Heading2')
            .$this->p('Data primer', 'ListBullet').$this->p('Data sekunder', 'ListBullet')
            .'<w:p><w:r><w:rPr><w:b/></w:rPr><w:t>Penting</w:t></w:r><w:r><w:t xml:space="preserve"> sekali &lt;script&gt;</w:t></w:r></w:p>'
            .'<w:tbl><w:tr>'.$this->cell('Jenis').$this->cell('Fokus').'</w:tr><w:tr>'.$this->cell('Eksploratori').$this->cell('Apa masalahnya?').'</w:tr></w:tbl>'
        );

        $this->post(route('admin.curriculum.lessons.store', $this->section), ['title' => 'Lesson 02.1', 'type' => 'text', 'docx' => $docx])
            ->assertSessionHasNoErrors();

        $lesson = Lesson::firstOrFail();
        $this->assertSame(
            '<h2>Riset Pasar</h2><h3>Pertanyaan riset</h3><ul><li>Data primer</li><li>Data sekunder</li></ul>'
            .'<p><strong>Penting</strong> sekali &lt;script&gt;</p>'
            .'<table><tr><th><p>Jenis</p></th><th><p>Fokus</p></th></tr><tr><td><p>Eksploratori</p></td><td><p>Apa masalahnya?</p></td></tr></table>',
            $lesson->content,
        );
        $this->assertTrue($lesson->hasRichLayout());

        // The admin form hides the text editor for Word lessons, so a save without content or file keeps the text.
        $this->put(route('admin.curriculum.lessons.update', $lesson), ['title' => 'Lesson 02.1 baru', 'type' => 'text'])->assertSessionHasNoErrors();
        $this->assertSame($lesson->content, $lesson->fresh()->content);

        $this->post(route('admin.curriculum.lessons.store', $this->section), ['title' => 'Rusak', 'type' => 'text', 'docx' => UploadedFile::fake()->create('rusak.docx', 5)])
            ->assertSessionHasErrors('docx');
    }

    public function test_test_questions_import_from_the_rayacademy_template(): void
    {
        $post = $this->docx(
            $this->p("PETUNJUK:\n1. Kerjakan semua soal.")
            .$this->p('1. Apa itu riset?').$this->p('A. Promosi').$this->p('B. Mencari bukti')
            .$this->p('2. Harga per mL dihitung dari?').$this->p('A. Harga dibagi isi').$this->p('B. Isi dibagi harga')
            .$this->p('SOAL ESSAY 1: STUDI KASUS (BOBOT: 50 POIN)').$this->p('1. Rumuskan job statement.')
            .$this->p('KUNCI JAWABAN')
            .'<w:tbl><w:tr>'.$this->cell('No').$this->cell('Kunci').'</w:tr><w:tr>'.$this->cell('1').$this->cell('B').'</w:tr><w:tr>'.$this->cell('2').$this->cell('A').'</w:tr></w:tbl>'
            .$this->p('PANDUAN JAWABAN IDEAL ESSAY 1 (KASUS): Ketika saya ...')
        );

        $this->post(route('admin.questions.import', [$this->section, 'post']), ['docx' => $post])->assertSessionHasNoErrors();

        $questions = CourseQuestion::with('options')->orderBy('order')->get();
        $this->assertSame(['single', 'single', 'essay'], $questions->pluck('type')->all());
        $this->assertSame('Apa itu riset?', $questions[0]->question);
        $this->assertSame('Mencari bukti', $questions[0]->options->firstWhere('is_correct', true)->text);
        $this->assertSame('Harga dibagi isi', $questions[1]->options->firstWhere('is_correct', true)->text);
        $this->assertSame("SOAL ESSAY 1: STUDI KASUS (BOBOT: 50 POIN)\n1. Rumuskan job statement.", $questions[2]->question);
        $this->assertSame('PANDUAN JAWABAN IDEAL ESSAY 1 (KASUS): Ketika saya ...', $questions[2]->answer_key);

        // A second import into a test that already has questions is refused, so nothing gets duplicated.
        $this->post(route('admin.questions.import', [$this->section, 'post']), ['docx' => $post])->assertSessionHasErrors('docx');
        $this->assertSame(3, CourseQuestion::count());

        // A question without a key stops the whole import and names the question.
        $broken = $this->docx($this->p('1. Tanpa kunci?').$this->p('A. Ya').$this->p('B. Tidak'));
        $this->post(route('admin.questions.import', [$this->section, 'pre']), ['docx' => $broken])
            ->assertSessionHasErrors(['docx' => 'Soal no. 1: kunci jawaban tidak ditemukan atau tidak cocok dengan opsinya.']);
        $this->assertSame(0, CourseQuestion::where('kind', 'pre')->count());
    }

    public function test_test_questions_import_from_the_assessment_package(): void
    {
        $post = $this->docx(
            $this->p('PETUNJUK PENGERJAAN BAGI PESERTA:').$this->p('1. Alokasi Waktu Total: 45 Menit.')
            .$this->p('SOAL Q01 · NILAI (Alokasi: 60 Detik)').$this->p('Apa itu nilai?').$this->p('[ A ]  Harga murah').$this->p('[ B ]  Manfaat dikurangi pengorbanan')
            .$this->p('Soal Q02: Apa itu POP?').$this->p('[ A ]  Syarat kategori').$this->p('[ B ]  Pembeda')
            .$this->p('SOAL ESAI E01 (Bobot 50%) — Segmentasi').$this->p('KASUS BISNIS: Krim untuk semua orang.')
            .$this->p('BAGIAN II: PAKET EVALUATOR INDEPENDEN')
            .$this->p('EVALUASI Q01 · KUNCI: [ B ] · Nilai')
            .$this->p('• Q02 (Kunci: A · Modul: M03.4): POP adalah syarat minimum.')
            .$this->p('PANDUAN EVALUASI ESAI E01 — SEGMENTASI').$this->p('Target terlalu luas.')
            .$this->p('Rubrik Baku Penilaian Esai').$this->p('Ketepatan Pengetahuan')
            .$this->p('Skema Kontrak Output Evaluasi AI').$this->p('{ "score": 88 }')
        );

        $this->post(route('admin.questions.import', [$this->section, 'post']), ['docx' => $post])->assertSessionHasNoErrors();

        $questions = CourseQuestion::with('options')->orderBy('order')->get();
        $this->assertSame(['single', 'single', 'essay'], $questions->pluck('type')->all());
        $this->assertSame('Apa itu nilai?', $questions[0]->question);
        $this->assertSame('Manfaat dikurangi pengorbanan', $questions[0]->options->firstWhere('is_correct', true)->text);
        $this->assertSame('Syarat kategori', $questions[1]->options->firstWhere('is_correct', true)->text);
        $this->assertSame("SOAL ESAI E01 (Bobot 50%) — Segmentasi\nKASUS BISNIS: Krim untuk semua orang.", $questions[2]->question);
        $this->assertSame("PANDUAN EVALUASI ESAI E01 — SEGMENTASI\nTarget terlalu luas.\nRubrik Baku Penilaian Esai\nKetepatan Pengetahuan", $questions[2]->answer_key);
    }

    public function test_test_questions_import_from_the_lesson_tagged_package(): void
    {
        $post = $this->docx(
            $this->p('BAGIAN I: 15 SOAL PILIHAN GANDA')
            .$this->p("SOAL 01  [Lesson 04.1 · Brand Meaning]\nMengapa produk B lebih mahal?").$this->p('A.  Iklan suap').$this->p('B.  Makna merek')
            .$this->p('BAGIAN II: KOMPAS FILOSOFIS').$this->p('Fokus Pembelajaran: Lesson 04.2')
            .$this->p('SOAL 02  [Lesson 04.2 · Purpose]').$this->p('Apa itu purpose?').$this->p('A.  Alasan ada').$this->p('B.  Target omzet')
            .$this->p('SOAL 01: STUDI KASUS ESAI 1: JANJI FORMULA').$this->p('a. Mengapa janji 48 jam keliru?')
            .$this->p('PEDOMAN PENILAIAN, KUNCI JAWABAN & RUBRIK EVALUASI RESMI')
            .$this->p('Soal 01, 02').$this->p('1. B   |   2. A')
            .$this->p('Model Jawaban Sempurna Esai 1 (Skor Maksimal 20 Poin):').$this->p('Siklus kulit 28 hari.')
            .$this->p('RUBRIK PENILAIAN ANALITIS SOAL STUDI KASUS ESAI').$this->p('Akurasi Pemahaman Konseptual')
            .$this->p('STANDAR KONVERSI NILAI AKHIR').$this->p('Skor 86 – 100 Sangat Memuaskan')
        );

        $this->post(route('admin.questions.import', [$this->section, 'post']), ['docx' => $post])->assertSessionHasNoErrors();

        $questions = CourseQuestion::with('options')->orderBy('order')->get();
        $this->assertSame(['single', 'single', 'essay'], $questions->pluck('type')->all());
        $this->assertSame('Mengapa produk B lebih mahal?', $questions[0]->question);
        $this->assertSame('Makna merek', $questions[0]->options->firstWhere('is_correct', true)->text);
        $this->assertSame('Apa itu purpose?', $questions[1]->question);
        $this->assertSame('Alasan ada', $questions[1]->options->firstWhere('is_correct', true)->text);
        $this->assertSame("SOAL 01: STUDI KASUS ESAI 1: JANJI FORMULA\na. Mengapa janji 48 jam keliru?", $questions[2]->question);
        $this->assertSame("Model Jawaban Sempurna Esai 1 (Skor Maksimal 20 Poin):\nSiklus kulit 28 hari.\nRUBRIK PENILAIAN ANALITIS SOAL STUDI KASUS ESAI\nAkurasi Pemahaman Konseptual", $questions[2]->answer_key);
    }

    private function p(string $text, ?string $style = null): string
    {
        $runs = collect(explode("\n", $text))->map(fn ($line) => '<w:r><w:t xml:space="preserve">'.e($line).'</w:t></w:r>')->implode('<w:r><w:br/></w:r>');

        return '<w:p>'.($style ? "<w:pPr><w:pStyle w:val=\"{$style}\"/></w:pPr>" : '').$runs.'</w:p>';
    }

    private function cell(string $text): string
    {
        return '<w:tc>'.$this->p($text).'</w:tc>';
    }

    /** A minimal .docx; "Judul1" is how Indonesian Word names its Heading 1 style id. */
    private function docx(string $body): UploadedFile
    {
        $w = 'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"';
        $path = tempnam(sys_get_temp_dir(), 'docx');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('word/document.xml', "<w:document {$w}><w:body>{$body}</w:body></w:document>");
        $zip->addFromString('word/styles.xml', "<w:styles {$w}>"
            .'<w:style w:styleId="Judul1"><w:name w:val="heading 1"/></w:style>'
            .'<w:style w:styleId="Heading2"><w:name w:val="heading 2"/></w:style>'
            .'<w:style w:styleId="ListBullet"><w:name w:val="List Bullet"/></w:style></w:styles>');
        $zip->close();

        return new UploadedFile($path, 'materi.docx', null, null, true);
    }
}
