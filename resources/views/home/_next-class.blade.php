{{-- Discover Your Next Class: four classes from the database (featured first), each labelled with its Learning Family. --}}
@push('styles')
<style>
.nc{background:#eef5fd;}
.nc-intro{text-align:center;max-width:760px;margin:0 auto clamp(1.75rem,3.5vw,2.5rem);}
.nc-intro .hs-lead{margin:0 auto;}
.nc-cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:clamp(1rem,1.8vw,1.4rem);}
.nc-more{text-align:center;margin-top:clamp(1.75rem,3vw,2.5rem);}
.nc-card{--nc-accent:#1f6fd1;position:relative;display:flex;flex-direction:column;overflow:hidden;border-radius:20px;background:rgba(255,255,255,.92);
    border:1px solid #fff;box-shadow:0 16px 36px rgba(31,77,140,.12);transition:transform .25s ease,box-shadow .25s ease;}
.nc-card:hover{transform:translateY(-4px);box-shadow:0 22px 44px rgba(31,77,140,.18);}
.nc-card:focus-within{outline:3px solid #7cb7ff;outline-offset:3px;}
.nc-card.peach{--nc-accent:#e58a3a;}
.nc-card.teal{--nc-accent:#2a8f86;}
.nc-card.navy{--nc-accent:#d9a441;}
.nc-thumb{aspect-ratio:16/7;background:linear-gradient(135deg,#dbe9fa,#b9d3f3);display:flex;align-items:center;justify-content:center;color:#7fa3d6;font-size:2rem;overflow:hidden;}
.nc-thumb img{width:100%;height:100%;object-fit:cover;display:block;}
.nc-body{display:flex;flex-direction:column;flex:1;padding:1rem 1.25rem 1.25rem;}
.nc-family{align-self:flex-start;font-size:.8rem;font-weight:700;letter-spacing:.03em;color:#1f4f9c;background:#dceaf9;border-radius:999px;padding:.25rem .7rem;margin-bottom:.6rem;}
.nc-card h3{font-family:'Sora',sans-serif;font-size:1.05rem;font-weight:700;line-height:1.3;color:#0b1f4d;border-left:3px solid var(--nc-accent);padding-left:.6rem;margin:0 0 .45rem;}
.nc-card p{font-size:.85rem;line-height:1.55;color:#4a5878;margin:0 0 .9rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.nc-link{align-self:flex-start;margin-top:auto;display:inline-flex;align-items:center;gap:.45rem;font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:#0b2a63;
    text-decoration:none;border-bottom:2px solid #0b2a63;padding-bottom:2px;}
.nc-link::after{content:'';position:absolute;inset:0;}
.nc-link:focus-visible{outline:none;}
.nc-link i{transition:transform .2s ease;}
.nc-card:hover .nc-link i{transform:translateX(3px);}
@media (max-width:1024px){ .nc-cards{grid-template-columns:repeat(2,minmax(0,1fr));} }
@media (max-width:600px){ .nc-cards{grid-template-columns:1fr;} }
</style>
@endpush
<section class="hs nc" aria-labelledby="nc-title">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-light-m.webp') }}" srcset="{{ asset('img/home/bg-light-m.webp') }} 900w, {{ asset('img/home/bg-light.webp') }} 1672w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap">
        <div class="nc-intro rv">
            <span class="hs-pill">Curated Learning Experiences</span>
            <h2 class="hs-title" id="nc-title">Discover your <span>next class.</span></h2>
            <p class="hs-lead">Temukan kursus pilihan RayAcademy dari berbagai bidang pembelajaran untuk membantu mengembangkan pengetahuan dan keterampilan yang relevan dengan kebutuhan Anda.</p>
        </div>
        @if($nextClasses->isNotEmpty())
            <div class="nc-cards">
                @foreach($nextClasses as $course)
                    <article class="nc-card {{ $course->family_theme }} rv">
                        <div class="nc-thumb">
                            @if($course->thumbnail_url)
                                <img src="{{ $course->thumbnail_url }}" alt="" loading="lazy" decoding="async" onerror="this.remove()">
                            @else
                                <i class="fas fa-book-open" aria-hidden="true"></i>
                            @endif
                        </div>
                        <div class="nc-body">
                            @if($course->family_name)<span class="nc-family">{{ $course->family_name }}</span>@endif
                            <h3>{{ $course->title }}</h3>
                            <p>{{ Str::limit(strip_tags((string) $course->description), 140) }}</p>
                            <a href="{{ route('course.show', $course->slug) }}" class="nc-link" aria-label="Lihat detail kelas {{ $course->title }}">Lihat Detail Kelas <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
        <div class="nc-more"><a href="{{ route('course.index') }}" class="hb hb-navy">Jelajahi Semua Kelas <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
    </div>
</section>
