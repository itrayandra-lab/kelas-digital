{{-- Read-only list of sessions with a seat bar; expects $event with sessions (registrations_count loaded). --}}
<div class="evs">
    @foreach($event->sessions as $s)
        @php
            $left = $s->seatsLeft();
            $state = $left === 0 ? 'full' : ($left <= max(3, (int) ceil($s->capacity * .2)) ? 'low' : '');
        @endphp
        <div class="evs-row">
            <span class="evs-time">{{ str_replace(':', '.', substr($s->starts_at, 0, 5)) }}</span>
            <div class="evs-info">
                <b>{{ $s->label }}{{ $s->ends_at ? ' · sampai '.str_replace(':', '.', substr($s->ends_at, 0, 5)) : '' }} {{ $event->timezone }}</b>
                <div class="evs-bar {{ $state }}" role="img" aria-label="{{ $s->capacity - $left }} dari {{ $s->capacity }} kursi terisi"><div style="width:{{ min(100, round(($s->capacity - $left) / max(1, $s->capacity) * 100)) }}%"></div></div>
            </div>
            <span class="evs-left {{ $state }}">{{ $left === 0 ? 'Penuh' : 'Sisa '.$left }}</span>
        </div>
    @endforeach
</div>
