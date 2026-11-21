@php
    $event = $registration->event;
    $session = $registration->session;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tiket {{ $registration->code }}</title>
<style>
    @page { margin: 26px; }
    body { font-family: 'DejaVu Sans', sans-serif; color: #0a1628; font-size: 11px; margin: 0; }
    .ticket { border: 1px solid #dbe3ee; border-radius: 14px; overflow: hidden; }
    .head { background: #1474bc; color: #fff; padding: 16px 20px; }
    .head .brand { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #cfe6f8; }
    .head .title { font-size: 19px; font-weight: bold; margin-top: 4px; }
    .head .sub { font-size: 11px; color: #e6f2fb; margin-top: 3px; }
    .qr { text-align: center; padding: 18px 20px 12px; border-bottom: 2px dashed #cbd5e1; }
    .qr img { width: 190px; height: 190px; }
    .code { font-size: 22px; font-weight: bold; letter-spacing: 4px; margin-top: 6px; }
    .hint { color: #6b7a92; font-size: 10px; margin-top: 2px; }
    table.rows { width: 100%; border-collapse: collapse; margin: 6px 0; }
    table.rows td { padding: 7px 20px; vertical-align: top; }
    table.rows td.label { width: 32%; color: #6b7a92; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding-top: 9px; }
    table.rows td.value { font-size: 12px; font-weight: bold; }
    table.rows td.value span { display: block; font-weight: normal; color: #6b7a92; font-size: 10px; margin-top: 2px; }
    .foot { background: #f7f9fc; padding: 12px 20px; font-size: 10px; color: #2d3a4e; line-height: 1.5; }
    .foot b { color: #0a1628; }
    .link { color: #1474bc; word-break: break-all; }
</style>
</head>
<body>
<div class="ticket">
    <div class="head">
        <div class="brand">Ray Academy · Tiket event · Gratis</div>
        <div class="title">{{ $event->title }}</div>
        @if($event->subtitle)<div class="sub">{{ $event->subtitle }}</div>@endif
    </div>
    <div class="qr">
        <img src="{{ $qr }}" alt="QR tiket">
        <div class="code">{{ $registration->code }}</div>
        <div class="hint">Tunjukkan QR ini ke panitia saat datang</div>
    </div>
    <table class="rows">
        <tr><td class="label">Nama</td><td class="value">{{ $registration->name }}</td></tr>
        <tr><td class="label">Tanggal</td><td class="value">{{ $event->dateLabel() }}</td></tr>
        <tr><td class="label">Sesi</td><td class="value">{{ $session->label }}, pukul {{ $session->timeLabel() }} {{ $event->timezone }}</td></tr>
        <tr>
            <td class="label">{{ $event->isOnline() ? 'Tempat' : 'Lokasi' }}</td>
            <td class="value">
                {{ $event->isOnline() ? 'Online' : $event->venue }}
                @if($event->isOnline() && $event->meeting_url)<span>{{ $event->meeting_url }}</span>@elseif(! $event->isOnline() && $event->address)<span>{{ $event->address }}</span>@endif
            </td>
        </tr>
    </table>
    <div class="foot">
        <b>Cek keaslian tiket:</b> pindai QR atau buka <span class="link">{{ route('tickets.show', $registration) }}</span><br>
        Berhalangan hadir? Batalkan pendaftaran dari halaman tiket supaya kursinya bisa dipakai orang lain.
    </div>
</div>
</body>
</html>
