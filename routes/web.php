<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ArticleCategoryController as AdminArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CourseCategoryController as AdminCourseCategoryController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\HeroSliderController as AdminHeroSliderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShareDomainController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/courses', [CourseController::class, 'index'])->name('course.index');
Route::get('/course/{slug}', [CourseController::class, 'show'])->name('course.show');
Route::get('/course/{slug}/lesson/{lesson}', [LessonController::class, 'show'])->name('course.lesson');
Route::middleware('auth')->group(function () {
    Route::get('/course/{slug}/learn', [LessonController::class, 'learn'])->name('course.learn');
    Route::get('/course/{slug}/pengenalan', [LessonController::class, 'intro'])->name('course.intro');
    Route::post('/course/{slug}/lesson/{lesson}/complete', [LessonController::class, 'complete'])->name('course.lesson.complete');
    Route::get('/course/{slug}/lesson/{lesson}/pdf', [LessonController::class, 'pdf'])->middleware('throttle:30,1')->name('course.lesson.pdf');
    Route::post('/course/{slug}/lesson/{lesson}/progress', [LessonController::class, 'ping'])->middleware('throttle:30,1')->name('course.lesson.progress');
    // Per-section pre-test and post-test, one question at a time with a server-side timer
    Route::controller(TestController::class)->prefix('/course/{slug}/bab/{section}/{kind}')->whereIn('kind', ['pre', 'post'])->group(function () {
        Route::get('/', 'show')->name('course.test');
        Route::post('/start', 'start')->name('course.test.start');
        Route::post('/answer', 'answer')->name('course.test.answer');
    });
    Route::get('/course/{slug}/completed', [LessonController::class, 'completed'])->name('course.completed');
});
Route::post('/course/{slug}/enroll', [CourseController::class, 'enroll'])->middleware('throttle:10,1')->name('course.enroll');
// Events: anyone can look; signing up needs an account so name and email come from the profile.
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/daftar', [EventController::class, 'join'])->name('events.join');
Route::middleware('auth')->controller(EventController::class)->group(function () {
    Route::post('/events/{event}/daftar', 'register')->middleware('throttle:10,1')->name('events.register');
    Route::delete('/events/{event}/daftar', 'cancel')->name('events.cancel');
    Route::get('/events/{event}/berhasil', 'success')->name('events.success');
});
// Tickets: the QR opens the ticket page; event staff check people in from it.
Route::get('/tiket/{registration}', [TicketController::class, 'show'])->middleware('throttle:60,1')->name('tickets.show');
Route::middleware('auth')->controller(TicketController::class)->group(function () {
    Route::get('/tiket/{registration}/unduh', 'download')->name('tickets.download');
    Route::middleware('can:manage events')->group(function () {
        Route::post('/tiket/{registration}/hadir', 'checkIn')->name('tickets.checkin');
        Route::get('/cek-tiket', 'lookup')->name('tickets.lookup');
    });
});
Route::get('/article/{slug}', [HomeController::class, 'showArticle'])->name('article.show');
Route::get('/articles', [ArticleController::class, 'index'])->name('article.index');
Route::get('/articles/category/{slug}', [ArticleController::class, 'showByCategory'])->name('article.category');
Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Midtrans payment routes
// Each checkout calls Midtrans, so one person can't hammer it.
Route::post('/payment/checkout/{course}', [PaymentController::class, 'checkout'])->middleware('throttle:10,1')->name('payment.checkout');
Route::post('/payment/voucher/{course}', [PaymentController::class, 'voucher'])->middleware(['auth', 'throttle:20,1'])->name('payment.voucher');
Route::post('/payment/complete/{enrollment}', [PaymentController::class, 'complete'])->name('payment.complete')->middleware('auth');
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');
Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');

// Tag browsing routes
// Tags are listed in the article sidebar; the old topics page sends visitors there.
Route::redirect('/articles/tags', '/articles', 301)->name('tag.index');
Route::get('/articles/tag/{tag:slug}', [ArticleController::class, 'showByTag'])->name('tag.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile management routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/origin', [ProfileController::class, 'updateOrigin'])->name('profile.origin');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    Route::post('/attachments', function (Request $request) {
        $request->validate([
            // No SVG: it can carry scripts that would run on this domain.
            'attachment' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        $path = $request->file('attachment')->store('trix-attachments', 'public');

        return [
            'image_url' => '/storage/'.$path,
        ];
    })->middleware('admin')->name('attachments.store');
});

// ═══════════════════════════════════════════════════════════════
// ADMIN ROUTES - HANYA UNTUK ADMIN & SUPER-ADMIN
// ═══════════════════════════════════════════════════════════════
Route::prefix('admin')
    ->middleware(['auth', 'admin']) // PROTEKSI ADMIN
    ->name('admin.')
    ->group(function () {

        // Admin Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Course management routes
        Route::middleware('can:view courses')->group(function () {
            Route::resource('courses', AdminCourseController::class);
            Route::get('courses/{course}/learners.csv', [AdminCourseController::class, 'exportLearners'])->name('courses.learners.export');
        });

        // Course category management
        Route::middleware('can:view course categories')->group(function () {
            Route::resource('course-categories', AdminCourseCategoryController::class)->except(['show']);
        });


        // Curriculum builder: sections, lessons and pre/post-test questions of one course
        Route::middleware('can:edit courses')->controller(CurriculumController::class)->group(function () {
            Route::get('courses/{course}/curriculum', 'show')->name('curriculum.show');
            Route::put('courses/{course}/curriculum/settings', 'updateSettings')->name('curriculum.settings');
            Route::post('courses/{course}/sections', 'storeSection')->name('sections.store');
            Route::put('sections/{section}', 'updateSection')->name('sections.update');
            Route::post('sections/{section}/free', 'toggleSectionFree')->name('sections.free');
            Route::delete('sections/{section}', 'destroySection')->name('sections.destroy');
            Route::post('sections/{section}/move/{direction}', 'moveSection')->whereIn('direction', ['up', 'down'])->name('sections.move');
            Route::post('sections/{section}/lessons', 'storeLesson')->name('curriculum.lessons.store');
            Route::put('curriculum/lessons/{lesson}', 'updateLesson')->name('curriculum.lessons.update');
            Route::delete('curriculum/lessons/{lesson}', 'destroyLesson')->name('curriculum.lessons.destroy');
            Route::post('curriculum/lessons/{lesson}/move/{direction}', 'moveLesson')->whereIn('direction', ['up', 'down'])->name('curriculum.lessons.move');
            Route::post('sections/{section}/questions/{kind}/import', 'importQuestions')->whereIn('kind', ['pre', 'post'])->name('questions.import');
            Route::post('sections/{section}/questions/{kind}', 'storeQuestion')->whereIn('kind', ['pre', 'post'])->name('questions.store');
            Route::put('questions/{question}', 'updateQuestion')->name('questions.update');
            Route::delete('questions/{question}', 'destroyQuestion')->name('questions.destroy');
            Route::post('questions/{question}/move/{direction}', 'moveQuestion')->whereIn('direction', ['up', 'down'])->name('questions.move');
            Route::put('attempts/{attempt}/review', 'reviewAttempt')->name('attempts.review');
        });

        // Events and their registrations
        Route::middleware('can:manage events')->group(function () {
            Route::resource('events', AdminEventController::class)->except(['show']);
            Route::get('events/{event}/peserta', [AdminEventController::class, 'registrations'])->name('events.registrations');
            Route::post('events/{event}/salin', [AdminEventController::class, 'duplicate'])->name('events.duplicate');
            Route::get('scan-tiket', [AdminEventController::class, 'scanPicker'])->name('events.scan-picker');
            Route::get('events/{event}/scan', [AdminEventController::class, 'scanner'])->name('events.scan');
            Route::post('events/{event}/scan', [AdminEventController::class, 'scanCheckIn'])->middleware('throttle:120,1')->name('events.scan.checkin');
            Route::get('events/{event}/peserta.csv', [AdminEventController::class, 'exportRegistrations'])->name('events.registrations.export');
            Route::delete('event-registrations/{registration}', [AdminEventController::class, 'destroyRegistration'])->name('events.registrations.destroy');
        });

        // User management
        Route::middleware('can:view users')->group(function () {
            Route::resource('users', AdminUserController::class);
        });

        // Payment management
        Route::middleware('can:manage enrollments')->group(function () {
            Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
            Route::post('payments/approve/{id}', [AdminPaymentController::class, 'approve'])->name('payments.approve');
            Route::resource('vouchers', AdminVoucherController::class)->except(['show']);
        });

        // Article management
        Route::middleware('can:view articles')->group(function () {
            Route::resource('articles', AdminArticleController::class);
            Route::post('articles/{id}/publish', [AdminArticleController::class, 'publish'])->name('articles.publish');
            Route::post('articles/{id}/unschedule', [AdminArticleController::class, 'unschedule'])->name('articles.unschedule');
        });

        // Hero slider management
        Route::middleware('can:view articles')->group(function () {
            Route::get('hero-slider', [AdminHeroSliderController::class, 'index'])->name('hero-slider.index');
            Route::post('hero-slider', [AdminHeroSliderController::class, 'update'])->name('hero-slider.update');
            Route::delete('hero-slider/{article}', [AdminHeroSliderController::class, 'remove'])->name('hero-slider.remove');
        });

        // Article category management
        Route::middleware('can:view article categories')->group(function () {
            Route::resource('article-categories', AdminArticleCategoryController::class)->except(['show']);
        });

        // Tag management
        Route::middleware('can:view tags')->group(function () {
            Route::resource('tags', AdminTagController::class)->except(['show']);
        });

        // Site settings management
        Route::middleware('can:manage site settings')->group(function () {
            Route::get('site-settings', [SiteSettingsController::class, 'index'])->name('site-settings.index');
            Route::put('site-settings', [SiteSettingsController::class, 'update'])->name('site-settings.update');
        });

        // Share Domains Management
        Route::middleware('can:view share domains')->group(function () {
            Route::resource('share-domains', ShareDomainController::class);

            Route::patch('share-domains/{id}/activate', [ShareDomainController::class, 'activate'])
                ->name('share-domains.activate');

            Route::patch('share-domains/{id}/deactivate', [ShareDomainController::class, 'deactivate'])
                ->name('share-domains.deactivate');

            Route::patch('share-domains/{id}/regenerate-api-key', [ShareDomainController::class, 'regenerateApiKey'])
                ->name('share-domains.regenerate-api-key');
        });

        // Role and permission management
        Route::middleware('can:manage roles and permissions')->group(function () {
            Route::resource('roles', RoleController::class)->except(['show']);
            Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        });
    });
