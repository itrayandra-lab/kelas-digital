{{-- RAYA, the AI learning advisor. Same banner layout as Corporate Training (home/_corporate). --}}
@push('styles')
<style>
.ry{background:#0c47b0;--hb-shade:rgba(12,62,160,.88);}
.ry-tag{display:inline-block;font-family:'DM Sans',sans-serif;font-size:clamp(.72rem,1.1vw,.85rem);font-weight:700;letter-spacing:.06em;text-transform:uppercase;
    color:#fff;border:2px solid #ff5fb0;border-radius:999px;padding:.45rem 1.1rem;margin-bottom:1.1rem;box-shadow:0 0 18px rgba(255,95,176,.45);}
.ry-tag span,.ry .hs-title span{color:#ff6fb8;}
.ry .hb-banner-text{max-width:620px;}
.ry .hs-title{font-size:clamp(1.9rem,3.6vw,3.1rem);}
.ry .hb-white{font-size:1rem;font-weight:700;padding:.8rem 2.4rem;}
</style>
@endpush
<section class="hb-banner ry" id="raya" aria-labelledby="ry-title">
    <div class="hb-banner-photo"><img src="{{ asset('img/home/raya-m.webp') }}" srcset="{{ asset('img/home/raya-m.webp') }} 1000w, {{ asset('img/home/raya.webp') }} 1920w" sizes="100vw" width="1920" height="640" alt="Ilustrasi RAYA, AI Learning Advisor RayAcademy" loading="lazy" decoding="async"></div>
    <div class="wrap">
        <div class="hb-banner-text rv">
            <p class="ry-tag">Meet RAYA · <span>Your AI Learning Advisor</span></p>
            <h2 class="hs-title" id="ry-title">Not sure which <span>class to take?</span></h2>
            <p class="hs-lead">Hai, saya RAYA, AI Learning Advisor dari RayAcademy. Masih bingung memilih kelas? Ceritakan apa yang ingin Anda pelajari, pengalaman Anda, atau keterampilan yang ingin dikembangkan. Saya siap membantu menemukan kursus RayAcademy yang relevan dengan tujuan Anda.</p>
            <a href="{{ $links['raya'] }}" class="hb hb-white">Tanya RAYA</a>
        </div>
    </div>
</section>
