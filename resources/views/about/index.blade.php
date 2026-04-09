@extends('layouts.app')

@section('title', 'Tentang Kami - Ray Academy')

@section('content')
<style>
.about-hero {
    background: linear-gradient(135deg, var(--ink) 0%, #1e3a5f 100%);
    padding: 5rem 1.5rem 4rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(20,116,188,.25) 0%, transparent 60%);
    pointer-events: none;
}

.about-hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
}

.about-hero h1 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 1.25rem;
    line-height: 1.2;
}

.about-hero p {
    font-size: 1.15rem;
    color: rgba(255,255,255,.75);
    line-height: 1.8;
}

.about-section {
    padding: 5rem 1.5rem;
    max-width: 1280px;
    margin: 0 auto;
}

.about-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2.5rem;
    margin-top: 3rem;
}

.about-card {
    background: #fff;
    border: 2px solid var(--border);
    border-radius: 16px;
    padding: 2.5rem;
    transition: all .3s;
}

.about-card:hover {
    border-color: var(--blue);
    box-shadow: 0 12px 32px rgba(20,116,188,.15);
    transform: translateY(-4px);
}

.about-card-icon {
    width: 64px;
    height: 64px;
    background: var(--blue-xl);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--blue);
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
}

.about-card h3 {
    font-family: 'Sora', sans-serif;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: .75rem;
}

.about-card p {
    color: var(--muted);
    line-height: 1.7;
    font-size: .95rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.stat-item {
    text-align: center;
    padding: 2rem;
    background: var(--surf);
    border-radius: 14px;
}

.stat-number {
    font-family: 'Sora', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--blue);
    display: block;
    margin-bottom: .5rem;
}

.stat-label {
    color: var(--muted);
    font-size: .9rem;
    font-weight: 600;
}
</style>

{{-- Hero Section --}}
<section class="about-hero">
    <div class="about-hero-content">
        <h1>Tentang Ray Academy</h1>
        <p>Platform belajar online terpercaya yang menghadirkan pendidikan berkualitas untuk semua orang, kapan saja, di mana saja.</p>
    </div>
</section>

{{-- Visi & Misi --}}
<section class="about-section">
    <div style="text-align: center; max-width: 700px; margin: 0 auto 4rem;">
        <h2 style="font-family: 'Sora', sans-serif; font-size: 2.25rem; font-weight: 800; color: var(--ink); margin-bottom: 1rem;">
            Visi & Misi Kami
        </h2>
        <p style="color: var(--muted); font-size: 1.05rem; line-height: 1.8;">
            Kami percaya bahwa pendidikan berkualitas harus dapat diakses oleh semua orang. Ray Academy hadir untuk mewujudkan visi tersebut.
        </p>
    </div>

    <div class="about-grid">
        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <h3>Visi</h3>
            <p>Menjadi platform pembelajaran online terdepan di Indonesia yang menghadirkan transformasi karir melalui pendidikan berkualitas tinggi dan terjangkau.</p>
        </div>

        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h3>Misi</h3>
            <p>Memberikan akses pendidikan berkualitas kepada jutaan orang Indonesia dengan kursus yang relevan, instruktur berpengalaman, dan harga terjangkau.</p>
        </div>

        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h3>Nilai Kami</h3>
            <p>Integritas, inovasi, dan inklusivitas adalah nilai inti yang kami pegang dalam setiap langkah perjalanan Ray Academy.</p>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="about-section" style="background: var(--surf);">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="font-family: 'Sora', sans-serif; font-size: 2.25rem; font-weight: 800; color: var(--ink);">
            Ray Academy dalam Angka
        </h2>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-number">50K+</span>
            <span class="stat-label">Siswa Aktif</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">200+</span>
            <span class="stat-label">Kursus Tersedia</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">100+</span>
            <span class="stat-label">Instruktur Ahli</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">4.8/5</span>
            <span class="stat-label">Rating Pengguna</span>
        </div>
    </div>
</section>

{{-- Why Choose Us --}}
<section class="about-section">
    <div style="text-align: center; max-width: 700px; margin: 0 auto 4rem;">
        <h2 style="font-family: 'Sora', sans-serif; font-size: 2.25rem; font-weight: 800; color: var(--ink); margin-bottom: 1rem;">
            Mengapa Memilih Ray Academy?
        </h2>
        <p style="color: var(--muted); font-size: 1.05rem; line-height: 1.8;">
            Kami berkomitmen memberikan pengalaman belajar terbaik dengan berbagai keunggulan
        </p>
    </div>

    <div class="about-grid">
        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h3>Instruktur Berpengalaman</h3>
            <p>Belajar langsung dari praktisi dan ahli industri dengan pengalaman puluhan tahun di bidangnya.</p>
        </div>

        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <h3>Sertifikat Terverifikasi</h3>
            <p>Dapatkan sertifikat resmi yang diakui industri setelah menyelesaikan setiap kursus.</p>
        </div>

        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-infinity"></i>
            </div>
            <h3>Akses Selamanya</h3>
            <p>Belajar dengan tempo Anda sendiri dengan akses lifetime ke semua materi kursus yang Anda beli.</p>
        </div>

        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h3>Komunitas Aktif</h3>
            <p>Bergabung dengan ribuan learner lainnya, diskusi, berbagi pengalaman, dan berkembang bersama.</p>
        </div>

        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h3>Belajar Kapan Saja</h3>
            <p>Platform 100% mobile-friendly. Akses dari smartphone, tablet, atau laptop Anda.</p>
        </div>

        <div class="about-card">
            <div class="about-card-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>Support 24/7</h3>
            <p>Tim support kami siap membantu Anda kapanpun melalui email, chat, atau WhatsApp.</p>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section style="background: linear-gradient(135deg, var(--blue) 0%, var(--blue-d) 100%); padding: 5rem 1.5rem; text-align: center;">
    <div style="max-width: 700px; margin: 0 auto;">
        <h2 style="font-family: 'Sora', sans-serif; font-size: 2.25rem; font-weight: 800; color: #fff; margin-bottom: 1rem;">
            Siap Memulai Perjalanan Belajar Anda?
        </h2>
        <p style="color: rgba(255,255,255,.85); font-size: 1.1rem; line-height: 1.8; margin-bottom: 2.5rem;">
            Bergabunglah dengan ribuan learner yang telah mengubah karir mereka bersama Ray Academy
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('course.index') }}" style="display: inline-flex; align-items: center; gap: .5rem; padding: 1rem 2rem; background: #fff; color: var(--blue); font-weight: 700; border-radius: 12px; text-decoration: none; transition: all .2s;">
                <i class="fas fa-graduation-cap"></i>
                Lihat Semua Kursus
            </a>
            <a href="{{ route('register') }}" style="display: inline-flex; align-items: center; gap: .5rem; padding: 1rem 2rem; background: transparent; color: #fff; font-weight: 700; border-radius: 12px; text-decoration: none; border: 2px solid rgba(255,255,255,.3); transition: all .2s;">
                Daftar Gratis
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

@endsection