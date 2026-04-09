@extends('layouts.app')

@section('title', 'Hubungi Kami - Ray Academy')

@section('content')
<style>
.contact-hero {
    background: linear-gradient(135deg, var(--ink) 0%, #1e3a5f 100%);
    padding: 5rem 1.5rem 4rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(20,116,188,.25) 0%, transparent 60%);
    pointer-events: none;
}

.contact-hero-content {
    position: relative;
    z-index: 1;
    max-width: 700px;
    margin: 0 auto;
}

.contact-hero h1 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 1.25rem;
    line-height: 1.2;
}

.contact-hero p {
    font-size: 1.15rem;
    color: rgba(255,255,255,.75);
    line-height: 1.8;
}

.contact-section {
    padding: 5rem 1.5rem;
    max-width: 1100px;
    margin: 0 auto;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 4rem;
    align-items: start;
}

@media (max-width: 960px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
}

.contact-info-card {
    background: #fff;
    border: 2px solid var(--border);
    border-radius: 18px;
    padding: 2.5rem;
}

.contact-info-item {
    display: flex;
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.contact-info-item:last-child {
    margin-bottom: 0;
}

.contact-info-icon {
    width: 56px;
    height: 56px;
    background: var(--blue-xl);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--blue);
    font-size: 1.5rem;
    flex-shrink: 0;
}

.contact-info-content h3 {
    font-family: 'Sora', sans-serif;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: .4rem;
}

.contact-info-content p {
    color: var(--muted);
    font-size: .95rem;
    line-height: 1.6;
}

.contact-info-content a {
    color: var(--blue);
    text-decoration: none;
    font-weight: 600;
}

.contact-info-content a:hover {
    text-decoration: underline;
}

.contact-form-card {
    background: #fff;
    border: 2px solid var(--border);
    border-radius: 18px;
    padding: 2.5rem;
}

.form-group {
    margin-bottom: 1.75rem;
}

.form-group label {
    display: block;
    font-family: 'DM Sans', sans-serif;
    font-size: .9rem;
    font-weight: 600;
    color: var(--ink-2);
    margin-bottom: .6rem;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: .9rem 1.1rem;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: .95rem;
    color: var(--ink);
    transition: all .2s;
    background: var(--surf);
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(20,116,188,.12);
    background: #fff;
}

.form-group textarea {
    min-height: 140px;
    resize: vertical;
}

.btn-submit {
    width: 100%;
    padding: 1rem 2rem;
    background: var(--blue);
    color: #fff;
    font-family: 'DM Sans', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .6rem;
}

.btn-submit:hover {
    background: var(--blue-d);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(20,116,188,.25);
}

.social-links {
    display: flex;
    gap: .85rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border);
}

.social-link {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--surf);
    border: 2px solid var(--border);
    border-radius: 10px;
    color: var(--muted);
    text-decoration: none;
    transition: all .2s;
}

.social-link:hover {
    background: var(--blue);
    border-color: var(--blue);
    color: #fff;
    transform: translateY(-3px);
}

.faq-quick {
    background: var(--blue-xl);
    border: 2px dashed var(--blue);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    margin-top: 3rem;
}

.faq-quick h3 {
    font-family: 'Sora', sans-serif;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: .75rem;
}

.faq-quick p {
    color: var(--muted);
    margin-bottom: 1.5rem;
}

.faq-quick a {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .75rem 1.5rem;
    background: var(--blue);
    color: #fff;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    transition: all .2s;
}

.faq-quick a:hover {
    background: var(--blue-d);
    transform: translateY(-2px);
}
</style>

{{-- Hero Section --}}
<section class="contact-hero">
    <div class="contact-hero-content">
        <h1>Hubungi Kami</h1>
        <p>Punya pertanyaan? Kami siap membantu Anda 24/7. Jangan ragu untuk menghubungi kami melalui form di bawah atau kontak langsung.</p>
    </div>
</section>

{{-- Contact Section --}}
<section class="contact-section">
    <div class="contact-grid">
        {{-- Left: Contact Info --}}
        <div>
            <div class="contact-info-card">
                <h2 style="font-family: 'Sora', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--ink); margin-bottom: 1.75rem;">
                    Informasi Kontak
                </h2>

                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-info-content">
                        <h3>Email</h3>
                        <p>
                            <a href="mailto:info@rayacademy.com">info@rayacademy.com</a><br>
                            <a href="mailto:support@rayacademy.com">support@rayacademy.com</a>
                        </p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="contact-info-content">
                        <h3>Telepon</h3>
                        <p>
                            <a href="tel:+6281234567890">+62 812-3456-7890</a><br>
                            Senin - Jumat: 09:00 - 18:00 WIB
                        </p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-info-content">
                        <h3>Alamat</h3>
                        <p>
                            Jl. Cihampelas No. 123<br>
                            Bandung, Jawa Barat 40131<br>
                            Indonesia
                        </p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="contact-info-content">
                        <h3>WhatsApp</h3>
                        <p>
                            <a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890</a><br>
                            Chat langsung dengan tim kami
                        </p>
                    </div>
                </div>

                {{-- Social Media --}}
                <div class="social-links">
                    <a href="https://facebook.com/rayacademy" target="_blank" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://instagram.com/rayacademy" target="_blank" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com/rayacademy" target="_blank" class="social-link" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://tiktok.com/@rayacademy" target="_blank" class="social-link" aria-label="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://linkedin.com/company/rayacademy" target="_blank" class="social-link" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Right: Contact Form --}}
        <div>
            <div class="contact-form-card">
                <h2 style="font-family: 'Sora', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--ink); margin-bottom: .75rem;">
                    Kirim Pesan
                </h2>
                <p style="color: var(--muted); margin-bottom: 2rem; font-size: .95rem;">
                    Isi form di bawah ini dan kami akan merespon dalam 24 jam
                </p>

                <form action="#" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Lengkap *</label>
                        <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap Anda" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="nama@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <input type="tel" id="phone" name="phone" placeholder="+62 812-3456-7890">
                    </div>

                    <div class="form-group">
                        <label for="subject">Subjek *</label>
                        <select id="subject" name="subject" required>
                            <option value="">Pilih Subjek</option>
                            <option value="general">Pertanyaan Umum</option>
                            <option value="course">Informasi Kursus</option>
                            <option value="payment">Pembayaran</option>
                            <option value="technical">Bantuan Teknis</option>
                            <option value="partnership">Kerjasama</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Pesan *</label>
                        <textarea id="message" name="message" placeholder="Tulis pesan Anda di sini..." required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Pesan
                    </button>
                </form>
            </div>

            {{-- FAQ Shortcut --}}
            <div class="faq-quick">
                <h3>Punya Pertanyaan Umum?</h3>
                <p>Cek FAQ kami untuk jawaban cepat atas pertanyaan yang sering diajukan</p>
                <a href="{{ route('home') }}#faq">
                    <i class="fas fa-question-circle"></i>
                    Lihat FAQ
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Map Section (Optional) --}}
<section style="padding: 0 0 5rem;">
    <div style="max-width: 1100px; margin: 0 auto; padding: 0 1.5rem;">
        <div style="background: var(--surf); border: 2px solid var(--border); border-radius: 18px; overflow: hidden; height: 400px;">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.9004631418!2d107.60362!3d-6.914744!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e64c0e762c2d%3A0x9e2c5b1f9c5e9f1d!2sBandung%2C%20Jawa%20Barat!5e0!3m2!1sen!2sid!4v1234567890" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

@endsection