{{-- The RayAcademy Learning Experience: three cards, each with its photo cut from the design. --}}
@php
    $pillars = [
        ['image' => 'exp-human', 'icon' => 'fa-users', 'label' => 'Human Expertise', 'title' => 'Belajar Bersama Akademisi, Praktisi, dan Pakar.',
         'text' => 'Menghubungkan landasan keilmuan, pengalaman lapangan, dan keahlian khusus untuk memperkaya perspektif pembelajaran.'],
        ['image' => 'exp-ai', 'icon' => 'fa-laptop', 'label' => 'AI-Enhanced Learning', 'title' => 'Teknologi AI untuk Mendukung Pembelajaran.',
         'text' => 'AI membantu peserta mengeksplorasi materi, mengembangkan pertanyaan, dan memperdalam pemahaman terhadap konsep yang dipelajari.'],
        ['image' => 'exp-smartbook', 'icon' => 'fa-book-open', 'label' => 'Baleide AI Smart Book', 'title' => 'Ketika Buku Menjadi Pengalaman Belajar Interaktif.',
         'text' => 'AI Smart Book dari Baleide memungkinkan pembaca berinteraksi dengan isi buku, mengeksplorasi konsep, dan memperluas pemahaman melalui bantuan AI.'],
    ];
@endphp
@push('styles')
<style>
.lx{background:#edf4fc;}
.lx .hs-center{max-width:1000px;}
.lx .hs-title{font-size:clamp(1.9rem,4vw,3.2rem);}
.lx .hs-title span{display:block;}
.lx-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:clamp(1rem,2vw,1.5rem);}
.lx-card{display:flex;flex-direction:column;overflow:hidden;border-radius:26px;background:rgba(255,255,255,.72);border:1px solid rgba(255,255,255,.95);
    box-shadow:0 18px 40px rgba(31,77,140,.12);}
.lx-card img{display:block;width:100%;aspect-ratio:480/300;object-fit:cover;}
.lx-body{position:relative;padding:0 1.4rem 1.5rem;}
.lx-head{display:flex;align-items:flex-end;gap:.8rem;margin:-1.6rem 0 .8rem;}
.lx-icon{flex-shrink:0;width:3.2rem;height:3.2rem;border-radius:50%;background:#fff;border:2px solid #cfe0f6;display:flex;align-items:center;justify-content:center;
    color:#1f5fc0;font-size:1.15rem;box-shadow:0 6px 16px rgba(31,77,140,.15);}
.lx-label{margin:0 0 .3rem;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:#1f5fc0;
    border-left:3px solid #d9a441;padding-left:.6rem;}
.lx-card h3{font-family:'Sora',sans-serif;font-size:1.02rem;font-weight:700;line-height:1.35;color:#0b1f4d;margin:0 0 .5rem;}
.lx-card p{font-size:.88rem;line-height:1.6;color:#4a5878;margin:0;}
.lx-actions{display:flex;flex-wrap:wrap;justify-content:center;gap:1rem;margin-top:clamp(1.75rem,3vw,2.5rem);}
@media (max-width:900px){ .lx-grid{grid-template-columns:1fr;max-width:520px;margin-inline:auto;} }
</style>
@endpush
<section class="hs lx" id="pengalaman" aria-labelledby="lx-title">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-soft-m.webp') }}" srcset="{{ asset('img/home/bg-soft-m.webp') }} 900w, {{ asset('img/home/bg-soft.webp') }} 1672w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap">
        <header class="hs-center rv">
            <span class="hs-pill">The RayAcademy Learning Experience</span>
            <h2 class="hs-title" id="lx-title">Human expertise. <span>AI-enhanced learning.</span></h2>
            <p class="hs-lead">Di RayAcademy, pembelajaran didukung oleh pengetahuan akademisi, pengalaman praktisi, dan keahlian para pakar. Teknologi Artificial Intelligence (AI) membantu peserta mengeksplorasi materi dan memperdalam pemahaman, sementara sumber pengetahuan digital seperti AI Smart Book dari Baleide membuka cara baru untuk belajar dan menggali pengetahuan.</p>
        </header>
        <div class="lx-grid">
            @foreach($pillars as $pillar)
                <article class="lx-card rv">
                    <img src="{{ asset('img/home/'.$pillar['image'].'.webp') }}" width="480" height="315" alt="" loading="lazy" decoding="async">
                    <div class="lx-body">
                        <div class="lx-head">
                            <span class="lx-icon"><i class="fas {{ $pillar['icon'] }}" aria-hidden="true"></i></span>
                            <p class="lx-label">{{ $pillar['label'] }}</p>
                        </div>
                        <h3>{{ $pillar['title'] }}</h3>
                        <p>{{ $pillar['text'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="lx-actions rv">
            <a href="#praktik" class="hb hb-navy">Jelajahi Pengalaman Belajar <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            <a href="{{ $links['smart_book'] }}" class="hb hb-outline" target="_blank" rel="noopener">Kenali AI Smart Book di Baleide <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>
