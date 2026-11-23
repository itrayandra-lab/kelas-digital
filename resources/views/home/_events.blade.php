{{-- Upcoming events, laid out like "Discover Your Next Class": text on the left, event cards on the right. --}}
@push('styles')
<style>
.eh{background:#eaf2fc;}
.eh .hs-bg img{transform:scaleX(-1);}
.eh-grid{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.6fr);gap:clamp(1.5rem,4vw,3.5rem);align-items:center;}
.eh-intro .hs-title{font-size:clamp(1.8rem,3vw,2.7rem);}
.eh-intro .hs-title span{display:block;}
.eh-intro .hs-lead{margin-bottom:1.75rem;max-width:440px;}
.eh-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:clamp(1rem,2vw,1.5rem);}
/* A single event lies sideways (poster left, details right) so the portrait poster does not stretch the section. */
.eh-cards.one{grid-template-columns:minmax(0,720px);}
.eh-cards.one .ev-card{display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1fr);}
.eh-cards.one .ev-body{justify-content:center;padding:1.5rem 1.6rem;gap:.6rem;}
.eh-cards.one .ev-title{font-size:1.35rem;}
.eh-cards.one .ev-seats{margin-top:.9rem;}
.eh-cards .ev-card{border-radius:20px;box-shadow:0 16px 36px rgba(31,77,140,.12);}
@media (max-width:1024px){ .eh-grid{grid-template-columns:1fr;} .eh-cards.one{grid-template-columns:minmax(0,640px);} }
@media (max-width:600px){ .eh-cards.one .ev-card{display:flex;} .eh-cards.one .ev-body{padding:1rem 1.1rem 1.15rem;} }
</style>
@endpush
<section class="hs eh" id="event" aria-labelledby="eh-title">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-light-m.webp') }}" srcset="{{ asset('img/home/bg-light-m.webp') }} 900w, {{ asset('img/home/bg-light.webp') }} 1672w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap eh-grid">
        <div class="eh-intro rv">
            <span class="hs-pill">Events &amp; Workshops</span>
            <h2 class="hs-title" id="eh-title">Join our <span>next event.</span></h2>
            <p class="hs-lead">Kelas praktik dan workshop gratis bersama para pakar RayAcademy, offline maupun online. Kursi tiap sesi terbatas, jadi daftar lebih awal.</p>
            <a href="{{ route('events.index') }}" class="hb hb-navy">Jelajahi Semua Event <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="eh-cards {{ $events->count() === 1 ? 'one' : '' }}">
            @foreach($events as $event)
                <div class="rv">@include('events._card')</div>
            @endforeach
        </div>
    </div>
</section>
