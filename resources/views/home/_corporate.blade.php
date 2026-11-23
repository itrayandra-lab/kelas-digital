{{-- Corporate Training banner. Wide screens lay the text over the navy half of the photo;
     narrower screens show the photo first and the text below it. --}}
@push('styles')
<style>
.hb-banner{position:relative;overflow:hidden;color:#fff;}
.hb-banner-photo img{display:block;width:100%;height:100%;object-fit:cover;object-position:right center;}
.hb-banner .wrap{position:relative;z-index:1;}
.hb-banner-text{max-width:560px;padding:clamp(2.5rem,5vw,4rem) 0;}
.hb-banner-text .hs-title{color:#fff;font-size:clamp(2rem,4vw,3.4rem);}
.hb-banner-text .hs-lead{color:rgba(255,255,255,.92);margin-bottom:1.9rem;}
@media (min-width:1100px){
    .hb-banner{display:flex;align-items:center;min-height:max(440px,33.4vw);}
    .hb-banner-photo{position:absolute;inset:0;}
    .hb-banner-photo::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,var(--hb-shade) 0%,var(--hb-shade) 22%,transparent 50%);}
    .hb-banner .wrap{width:100%;}
}
@media (max-width:1099px){
    .hb-banner-photo{aspect-ratio:16/8;}
    .hb-banner-photo img{object-position:85% center;}
}

.ct{background:#0b1f45;--hb-shade:rgba(11,31,69,.9);}
.ct .hs-eyebrow{color:#e9b98a;letter-spacing:.14em;}
.ct .hs-title span{display:block;color:#e9b98a;}
.hb-gold{background:linear-gradient(180deg,#f0c89c,#d99d66);color:#0b1f4d;font-size:clamp(.95rem,1.3vw,1.1rem);font-weight:700;padding:.85rem 2rem;box-shadow:0 10px 26px rgba(217,157,102,.3);}
.hb-gold:hover{background:linear-gradient(180deg,#f5d4ae,#e3a978);color:#0b1f4d;}
</style>
@endpush
<section class="hb-banner ct" id="corporate" aria-labelledby="ct-title">
    <div class="hb-banner-photo"><img src="{{ asset('img/home/corporate-m.webp') }}" srcset="{{ asset('img/home/corporate-m.webp') }} 1000w, {{ asset('img/home/corporate.webp') }} 1920w" sizes="100vw" width="1920" height="640" alt="Tim profesional berdiskusi di laboratorium kosmetik" loading="lazy" decoding="async"></div>
    <div class="wrap">
        <div class="hb-banner-text rv">
            <p class="hs-eyebrow">Corporate Training · RayAcademy</p>
            <h2 class="hs-title" id="ct-title">Build a more <span>capable team.</span></h2>
            <p class="hs-lead">Setiap perusahaan memiliki tantangan dan kebutuhan kompetensi yang berbeda. RayAcademy membantu organisasi mengembangkan kemampuan tim melalui program pelatihan yang relevan dengan pekerjaan, selaras dengan tujuan bisnis, dan dapat disesuaikan dengan kebutuhan perusahaan.</p>
            <a href="{{ $links['corporate'] }}" class="hb hb-gold" @if(str_starts_with($links['corporate'], 'http')) target="_blank" rel="noopener" @endif>Konsultasikan Kebutuhan Training</a>
        </div>
    </div>
</section>
