{{-- One Learning Family card. Expects $family from config('homepage.families'). --}}
@once
@push('styles')
<style>
.lf-card{--lf-bg:linear-gradient(160deg,#16367a,#0b1f4d);--lf-fg:#fff;--lf-bar:#d9a441;--lf-muted:rgba(255,255,255,.86);
    display:flex;flex-direction:column;overflow:hidden;border-radius:20px;background:var(--lf-bg);color:var(--lf-fg);
    border:1px solid rgba(255,255,255,.7);box-shadow:0 16px 36px rgba(31,77,140,.16);transition:transform .25s ease,box-shadow .25s ease;}
.lf-card:hover{transform:translateY(-4px);box-shadow:0 22px 44px rgba(31,77,140,.22);}
.lf-card.blue{--lf-bg:linear-gradient(160deg,#2f86e0,#1554b8);--lf-bar:#fff;}
.lf-card.teal{--lf-bg:linear-gradient(160deg,#3aa596,#1f7b73);--lf-bar:#fff;}
.lf-card.peach{--lf-bg:linear-gradient(160deg,#fde9d6,#f6d0ad);--lf-fg:#0b1f4d;--lf-bar:#e58a3a;--lf-muted:#3b3b4f;}
.lf-card img{display:block;width:100%;aspect-ratio:430/222;object-fit:cover;}
.lf-card-body{display:flex;flex-direction:column;flex:1;padding:1.3rem 1.4rem 1.5rem;}
.lf-card h3{font-family:'Sora',sans-serif;font-size:1.3rem;font-weight:700;line-height:1.2;margin:0 0 .6rem;border-left:4px solid var(--lf-bar);padding-left:.7rem;color:inherit;}
.lf-card p{font-size:.88rem;line-height:1.6;color:var(--lf-muted);margin:0 0 1.2rem;flex:1;}
.lf-card .hb{align-self:flex-start;min-height:40px;padding:.55rem 1.2rem;font-size:.84rem;background:transparent;color:var(--lf-fg);border-color:currentColor;}
.lf-card .hb:hover{background:rgba(255,255,255,.18);color:var(--lf-fg);}
</style>
@endpush
@endonce
<article class="lf-card {{ $family['theme'] }} rv">
    <img src="{{ asset('img/home/'.$family['image'].'.webp') }}" width="430" height="222" alt="" loading="lazy" decoding="async">
    <div class="lf-card-body">
        <h3>{{ $family['name'] }}</h3>
        <p>{{ $family['description'] }}</p>
        <a href="{{ url($family['href']) }}" class="hb">Jelajahi {{ $family['name'] }} <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</article>
