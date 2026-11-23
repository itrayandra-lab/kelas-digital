{{-- Explore Our Learning Families. Families come from config/homepage.php: the featured one gets the wide card,
     every other active family gets a card in the grid below, however many there are. --}}
@php $shownFamilies = $otherFamilies->take(6); @endphp
@push('styles')
<style>
.lf{background:#eaf2fc;}
.lf-flag{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.05fr);align-items:stretch;overflow:hidden;border-radius:24px;
    background:rgba(255,255,255,.78);border:1px solid rgba(255,255,255,.95);box-shadow:0 18px 44px rgba(31,77,140,.12);margin-bottom:clamp(1rem,2vw,1.5rem);}
.lf-flag-body{padding:clamp(1.5rem,3vw,2.5rem);display:flex;flex-direction:column;align-items:flex-start;justify-content:center;}
.lf-flag-body .hs-pill{background:#d9e9fb;color:#1f5fc0;}
.lf-flag h3{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,2.6vw,2.1rem);font-weight:800;text-transform:uppercase;line-height:1.12;color:#0b1f4d;
    border-left:5px solid #d9a441;padding-left:.9rem;margin:0 0 .9rem;}
.lf-flag p{font-size:.95rem;line-height:1.7;color:#33415c;margin:0 0 1.4rem;max-width:560px;}
.lf-flag-photo{position:relative;min-height:240px;}
.lf-flag-photo img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:70% center;
    -webkit-mask-image:linear-gradient(to right,transparent,#000 28%);mask-image:linear-gradient(to right,transparent,#000 28%);}
.lf-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:clamp(1rem,2vw,1.5rem);}
.lf-more{text-align:center;margin-top:2rem;}
@media (max-width:820px){
    .lf-flag{grid-template-columns:1fr;}
    .lf-flag-photo{order:-1;min-height:0;aspect-ratio:830/290;}
    .lf-flag-photo img{-webkit-mask-image:none;mask-image:none;}
}
</style>
@endpush
<section class="hs lf" id="learning-families" aria-labelledby="lf-title">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-soft-m.webp') }}" srcset="{{ asset('img/home/bg-soft-m.webp') }} 900w, {{ asset('img/home/bg-soft.webp') }} 1672w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap">
        <header class="hs-center rv">
            <span class="hs-bar"></span>
            <p class="hs-eyebrow">Explore our learning families</p>
            <h2 class="hs-title" id="lf-title">Explore what you <span>can learn.</span></h2>
            <p class="hs-lead">{{ config('homepage.families_intro') }}</p>
        </header>

        @if($flagship)
            <article class="lf-flag rv">
                <div class="lf-flag-body">
                    <span class="hs-pill">Flagship Learning Family</span>
                    <h3>{{ $flagship['name'] }}</h3>
                    <p>{{ $flagship['description'] }}</p>
                    <a href="{{ url($flagship['href']) }}" class="hb hb-outline">Jelajahi {{ $flagship['name'] }} <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="lf-flag-photo"><img src="{{ asset('img/home/'.$flagship['image'].'.webp') }}" width="830" height="290" alt="" loading="lazy" decoding="async"></div>
            </article>
        @endif

        <div class="lf-grid">
            @foreach($shownFamilies as $family)
                @include('home._family-card')
            @endforeach
        </div>

        @if($otherFamilies->count() > $shownFamilies->count())
            <div class="lf-more"><a href="{{ route('course.index') }}" class="hb hb-navy">Jelajahi Semua Program RayAcademy <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
        @endif
    </div>
</section>
