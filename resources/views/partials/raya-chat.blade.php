{{-- RAYA chat badge. The panel is built by JS on first open, so no chat copy ends up in the page HTML.
     Any link to #tanya-raya opens it. --}}
@php
    $rayaWa = (string) \App\Models\Setting::get('social_whatsapp');
    $rayaWaNumber = preg_replace('/\D/', '', $rayaWa);
@endphp
<div id="rayaChatRoot"
     data-endpoint="{{ route('raya.chat') }}"
     data-csrf="{{ csrf_token() }}"
     data-avatar="{{ asset('img/raya/raya-avatar.webp') }}"
     data-contact="{{ route('course.index') }}"
     data-email="{{ \App\Models\Setting::get('contact_email') }}"
     data-wa="{{ $rayaWaNumber }}"
     data-wa-display="{{ $rayaWa }}">
    <button type="button" class="rc-fab" id="rayaFab" aria-label="Buka chat RAYA, AI Learning Advisor" aria-haspopup="dialog" aria-expanded="false" aria-controls="rayaPanel">
        <span class="rc-fab-ring" aria-hidden="true"></span>
        <img src="{{ asset('img/raya/raya-avatar.webp') }}" alt="" width="192" height="192" loading="lazy" decoding="async">
        <span class="rc-fab-dot" aria-hidden="true"></span>
        <span class="rc-fab-label" aria-hidden="true">Tanya RAYA</span>
    </button>
</div>
<link rel="stylesheet" href="{{ asset('css/raya-chat.css') }}?v={{ @filemtime(public_path('css/raya-chat.css')) }}">
<script src="{{ asset('js/raya-chat.js') }}?v={{ @filemtime(public_path('js/raya-chat.js')) }}" defer></script>
