{{-- Closing call to action. The site footer from layouts/app follows right after it. --}}
@push('styles')
<style>
.cl{background:#071a42;text-align:center;color:#fff;padding:clamp(4rem,8vw,6.5rem) 0;}
.cl .wrap{max-width:980px;}
.cl .hs-bar{margin-inline:auto;}
.cl .hs-eyebrow{color:#e0ae62;letter-spacing:.28em;}
.cl h2{font-family:'Sora',sans-serif;font-size:clamp(1.7rem,4.4vw,3.1rem);font-weight:800;text-transform:uppercase;letter-spacing:-.01em;line-height:1.12;color:#fff;margin:0 0 1rem;text-wrap:balance;}
.cl p.cl-text{font-size:clamp(.95rem,1.4vw,1.1rem);line-height:1.7;color:rgba(255,255,255,.9);margin:0 auto 2rem;max-width:900px;}
.cl-actions{display:flex;flex-wrap:wrap;justify-content:center;gap:1rem;}
.cl .hb{min-width:220px;font-weight:700;}
@media (max-width:480px){ .cl .hb{width:100%;} }
</style>
@endpush
<section class="hs cl" aria-labelledby="cl-title">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-navy-m.webp') }}" srcset="{{ asset('img/home/bg-navy-m.webp') }} 900w, {{ asset('img/home/bg-navy.webp') }} 1672w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap rv">
        <span class="hs-bar"></span>
        <p class="hs-eyebrow">Your learning journey</p>
        <h2 id="cl-title">Your next step starts here.</h2>
        <p class="cl-text">Pengetahuan membuka peluang. Kemampuan membantu Anda melangkah lebih jauh. Temukan pembelajaran yang relevan dengan tujuan pribadi, profesi, maupun perkembangan bisnis Anda.</p>
        <div class="cl-actions">
            <a href="{{ route('course.index') }}" class="hb hb-white">Jelajahi Kelas <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            <a href="#corporate" class="hb hb-ghost">Corporate Training <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>
