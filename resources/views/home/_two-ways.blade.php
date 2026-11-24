{{-- Two Ways to Grow: individual classes and corporate training. Photos are cut from the design; all text is HTML. --}}
@php
    $ways = [
        ['image' => 'two-ways-individual', 'icon' => 'fa-graduation-cap', 'label' => 'Individual & Professional Education', 'title' => 'Grow Your Expertise',
         'text' => 'Kembangkan pengetahuan dan keterampilan melalui kelas kosmetik, AI, branding, marketing, kewirausahaan, dan pengembangan profesional bersama akademisi serta praktisi berpengalaman.',
         'cta' => 'Jelajahi Kelas', 'href' => route('course.index')],
        ['image' => 'two-ways-corporate', 'icon' => 'fa-industry', 'label' => 'Corporate Training', 'title' => 'Empower Your Team',
         'text' => 'Bangun kompetensi tim melalui program pelatihan perusahaan yang disesuaikan dengan kebutuhan organisasi, mulai dari industri kosmetik hingga AI, branding, marketing, leadership, dan pengembangan bisnis.',
         'cta' => 'Jelajahi Corporate Training', 'href' => '#corporate'],
    ];
@endphp
@push('styles')
<style>
.tw{background:#071a42;}
.tw-grid{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,2.2fr);gap:clamp(1.5rem,3vw,2.75rem);align-items:center;}
.tw-intro{padding-right:clamp(1rem,2.5vw,2.5rem);border-right:2px solid rgba(217,164,65,.75);}
.tw .hs-eyebrow{color:#fff;font-size:clamp(.8rem,1.2vw,.95rem);letter-spacing:.16em;line-height:1.5;}
.tw-title{color:#fff;font-size:clamp(2rem,3.2vw,3.1rem);white-space:nowrap;line-height:1.15;}
@media (max-width:380px){.tw-title{white-space:normal;}}
.tw-title span{display:block;color:#4a9bf5;}
.tw-sub{font-family:'Sora',sans-serif;font-size:clamp(1.1rem,1.7vw,1.4rem);font-weight:500;line-height:1.35;color:#fff;margin:0 0 .9rem;}
.tw-text{font-size:.94rem;line-height:1.5;color:rgba(255,255,255,.82);margin:0;}
.tw-cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:clamp(1rem,2vw,1.5rem);}
.tw-card{display:flex;flex-direction:column;background:#fff;border-radius:22px;overflow:hidden;box-shadow:0 22px 50px rgba(0,0,0,.35);}
.tw-card img{display:block;width:100%;aspect-ratio:630/200;object-fit:cover;}
.tw-body{position:relative;display:flex;flex-direction:column;flex:1;padding:0 clamp(1.25rem,2vw,1.9rem) clamp(1.25rem,2vw,1.75rem);}
.tw-badge{align-self:flex-start;display:inline-flex;align-items:center;gap:.55rem;margin:-1.35rem 0 1rem;padding:.5rem 1rem;border-radius:999px;
    background:rgba(238,244,255,.96);box-shadow:0 6px 16px rgba(11,31,77,.12);
    font-family:'Sora',sans-serif;font-size:.8rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#1f5fc0;}
.tw-card h3{font-family:'Sora',sans-serif;font-size:clamp(1.3rem,2vw,1.75rem);font-weight:800;text-transform:uppercase;letter-spacing:-.01em;color:#0b1f4d;line-height:1.15;margin:0 0 .7rem;}
.tw-card p{font-size:.93rem;line-height:1.5;color:#33415c;margin:0 0 1.4rem;flex:1;}
.tw-card .hb{align-self:flex-start;}
@media (max-width:1024px){
    .tw-grid{grid-template-columns:1fr;}
    .tw-intro{border-right:0;padding-right:0;max-width:640px;}
}
@media (max-width:680px){ .tw-cards{grid-template-columns:1fr;} }
</style>
@endpush
<section class="hs tw" id="program" aria-labelledby="tw-title">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-navy-m.webp') }}" srcset="{{ asset('img/home/bg-navy-m.webp') }} 900w, {{ asset('img/home/bg-navy.webp') }} 1672w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap tw-grid">
        <div class="tw-intro rv">
            <span class="hs-bar"></span>
            <p class="hs-eyebrow">Discover your<br>learning path</p>
            <h2 class="hs-title tw-title" id="tw-title">Two ways <span>to grow</span></h2>
            <p class="tw-sub">Satu tujuan, dua cara untuk berkembang.</p>
            <p class="tw-text">RayAcademy menghadirkan program pendidikan dan pengembangan kompetensi untuk individu, profesional, dan perusahaan melalui pembelajaran bersama akademisi serta praktisi dari berbagai bidang keahlian.</p>
        </div>
        <div class="tw-cards">
            @foreach($ways as $way)
                <article class="tw-card rv">
                    <img src="{{ asset('img/home/'.$way['image'].'.webp') }}" width="630" height="295" alt="" loading="lazy" decoding="async">
                    <div class="tw-body">
                        <span class="tw-badge"><i class="fas {{ $way['icon'] }}" aria-hidden="true"></i> {{ $way['label'] }}</span>
                        <h3>{{ $way['title'] }}</h3>
                        <p>{{ $way['text'] }}</p>
                        <a href="{{ $way['href'] }}" class="hb hb-outline">{{ $way['cta'] }} <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
