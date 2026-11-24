{{-- Meet Our Experts: text above the group photo, the names in a navy pill over its lower edge. Names: config/homepage.php. --}}
@push('styles')
<style>
.xp{background:linear-gradient(180deg,#f4f8fd 0%,#e9f2fb 100%);padding-bottom:clamp(2.5rem,5vw,4rem);}
.xp .hs-center{margin-bottom:clamp(.5rem,2vw,1.25rem);}
.xp .hs-eyebrow{color:#0b1f4d;letter-spacing:.24em;}
.xp .hs-title{font-size:clamp(2rem,4.6vw,3.6rem);}
.xp .hs-title span{display:block;}
.xp .hs-lead{color:#1d2f5c;max-width:860px;margin-inline:auto;}
.xp-photo{margin:0 auto;max-width:1040px;}
.xp-photo img{display:block;width:100%;height:auto;-webkit-mask-image:linear-gradient(to bottom,transparent 0,#000 12%),linear-gradient(to right,transparent 0,#000 7%,#000 93%,transparent 100%);
    -webkit-mask-composite:source-in;mask-image:linear-gradient(to bottom,transparent 0,#000 12%),linear-gradient(to right,transparent 0,#000 7%,#000 93%,transparent 100%);mask-composite:intersect;}
.xp-names{position:relative;z-index:1;max-width:1180px;margin:clamp(-2.75rem,-3vw,-1.5rem) auto 0;padding:.9rem clamp(1rem,3vw,2.5rem);border-radius:999px;
    background:linear-gradient(180deg,#1747a6,#0b2a6b);box-shadow:0 14px 32px rgba(11,42,107,.3);border:1px solid rgba(255,255,255,.35);
    color:#fff;font-family:'DM Sans',sans-serif;font-size:clamp(.74rem,1vw,.9rem);font-weight:600;line-height:1.45;text-align:center;}
.xp-names p{margin:0;}
@media (max-width:900px){
    .xp-names{border-radius:22px;margin-top:-1rem;}
    .xp-names p + p{margin-top:.25rem;}
}
</style>
@endpush
<section class="hs xp" id="instruktur" aria-labelledby="xp-title">
    <div class="wrap">
        <header class="hs-center rv">
            <span class="hs-bar"></span>
            <p class="hs-eyebrow">Meet our experts</p>
            <h2 class="hs-title" id="xp-title">Learn from <span>the experts</span></h2>
            <p class="hs-lead">RayAcademy menghadirkan pembelajaran bersama akademisi, peneliti, pakar, dan praktisi berpengalaman dari berbagai bidang keahlian, mulai dari sains dan industri kosmetik, Artificial Intelligence (AI), branding dan marketing, hingga kewirausahaan, pengembangan usaha, dan kepemimpinan.</p>
        </header>
        <figure class="xp-photo">
            <img src="{{ asset('img/home/experts-m.webp') }}" srcset="{{ asset('img/home/experts-m.webp') }} 900w, {{ asset('img/home/experts.webp') }} 1672w" sizes="(min-width:1280px) 1280px, 100vw" width="1672" height="605"
                 alt="Para pakar RayAcademy: {{ collect(config('homepage.experts'))->flatten()->implode(', ') }}" loading="lazy" decoding="async">
        </figure>
        <div class="xp-names rv">
            @foreach(config('homepage.experts') as $line)
                <p>{{ implode(' - ', $line) }}</p>
            @endforeach
        </div>
    </div>
</section>
