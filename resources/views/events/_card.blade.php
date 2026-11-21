{{-- Expects $event with sessions (registrations_count loaded) unless it is past. --}}
@php
    $past = $event->isPast();
    $left = $past ? 0 : $event->seatsLeft();
    $seatClass = $past ? 'past' : ($left === 0 ? 'full' : ($left <= 5 ? 'low' : 'ok'));
    $seatText = $past ? 'Sudah berlangsung' : ($left === 0 ? 'Kursi penuh' : ($event->registration_open ? 'Sisa '.$left.' kursi' : 'Pendaftaran ditutup'));
@endphp
<a href="{{ route('events.show', $event) }}" class="ev-card">
    <div class="ev-poster">
        @if($event->cover_url)<img src="{{ $event->cover_url }}" alt="Poster {{ $event->title }}" loading="lazy">@endif
        <div class="ev-tags">
            <span class="ev-tag free">Gratis</span>
            <span class="ev-tag {{ $event->isOnline() ? 'online' : '' }}"><i class="fas {{ $event->isOnline() ? 'fa-video' : 'fa-location-dot' }}" aria-hidden="true"></i> {{ \App\Models\Event::FORMATS[$event->format] }}</span>
        </div>
        <div class="ev-date"><b>{{ $event->event_date->format('j') }}</b><span>{{ $event->event_date->locale('id')->translatedFormat('M Y') }}</span></div>
    </div>
    <div class="ev-body">
        <h3 class="ev-title">{{ $event->title }}</h3>
        @if($event->subtitle)<p class="ev-sub">{{ $event->subtitle }}</p>@endif
        <div class="ev-meta">
            @unless($past)<span><i class="far fa-clock" aria-hidden="true"></i> {{ $event->timesLabel() }}</span>@endunless
            <span><i class="fas {{ $event->isOnline() ? 'fa-video' : 'fa-location-dot' }}" aria-hidden="true"></i> {{ $event->isOnline() ? 'Acara online' : $event->venue }}</span>
        </div>
        <div class="ev-seats">
            <span class="{{ $seatClass }}">{{ $seatText }}</span>
            @unless($past)<span class="go">Lihat detail <i class="fas fa-arrow-right" aria-hidden="true"></i></span>@endunless
        </div>
    </div>
</a>
