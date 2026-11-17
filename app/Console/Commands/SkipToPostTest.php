<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Support\CourseProgress;
use Illuminate\Console\Command;

/**
 * For trying a course out: takes a test account straight to a module's post-test. Earlier modules are
 * marked finished (post-test passed); the chosen module gets its pre-test and lessons done. Only adds
 * progress, never removes any.
 *
 * php artisan course:skip-to-post-test student@example.com marketing-strategy-in-the-ai-era --module=1
 */
class SkipToPostTest extends Command
{
    protected $signature = 'course:skip-to-post-test {email} {course : course slug} {--module=1 : module number, in study order}';

    protected $description = 'Bawa akun uji sampai post-test sebuah modul (untuk mencoba kelas)';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        $course = Course::where('slug', $this->argument('course'))->first();
        if (! $user || ! $course) {
            $this->error($user ? 'Kelas tidak ditemukan.' : 'Akun tidak ditemukan.');

            return self::FAILURE;
        }

        $progress = new CourseProgress($course, $user);
        $target = $progress->sections->values()->get((int) $this->option('module') - 1);
        if (! $target) {
            $this->error('Modul ke-'.$this->option('module').' tidak ada (kelas ini punya '.$progress->sections->count().' modul berisi).');

            return self::FAILURE;
        }

        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (! $enrollment) {
            if (! $course->isPartlyFree() || ! $progress->sections->takeUntil(fn ($s) => $s->is($target))->push($target)->every('is_free')) {
                $this->error('Akun ini belum terdaftar di kelas, dan modulnya berbayar. Daftarkan atau bayar dulu lewat website.');

                return self::FAILURE;
            }
            Enrollment::create(['user_id' => $user->id, 'course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now(), 'payment_status' => 'pending', 'payment_method' => 'trial']);
            $this->line('Didaftarkan ke modul gratis.');
        }

        foreach ($progress->sections as $section) {
            $isTarget = $section->is($target);

            if ($section->pre_count && ! $progress->preTestDone($section)) {
                $this->attempt($user, $course, $section->id, 'pre', 50, false);
            }
            foreach ($section->lessons as $lesson) {
                LessonProgress::firstOrNew(['user_id' => $user->id, 'lesson_id' => $lesson->id])
                    ->fill(['completed_at' => now()])->save();
            }
            if (! $isTarget && $section->post_count && ! $progress->postTestPassed($section)) {
                $this->attempt($user, $course, $section->id, 'post', 100, true);
            }
            $this->line('✓ '.$section->title.($isTarget ? ': pre-test dan semua materi selesai' : ': selesai'));

            if ($isTarget) {
                break;
            }
        }

        $this->info('Langkah berikutnya: '.(new CourseProgress($course, $user))->nextStepLabel());

        return self::SUCCESS;
    }

    private function attempt(User $user, Course $course, int $sectionId, string $kind, int $score, bool $passed): void
    {
        QuizAttempt::create([
            'user_id' => $user->id, 'course_id' => $course->id, 'section_id' => $sectionId, 'kind' => $kind,
            'status' => 'graded', 'score' => $score, 'passed' => $passed, 'answers' => [],
        ]);
    }
}
