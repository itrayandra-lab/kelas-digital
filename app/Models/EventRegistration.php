<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class EventRegistration extends Model
{
    /** Readable on a phone screen and over the phone: no 0/O, 1/I/L. */
    private const CODE_CHARS = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    private const TIMEZONES = ['WIB' => 'Asia/Jakarta', 'WITA' => 'Asia/Makassar', 'WIT' => 'Asia/Jayapura'];

    protected $fillable = ['event_id', 'event_session_id', 'user_id', 'name', 'email', 'whatsapp', 'age', 'checked_in_at'];

    protected $casts = ['checked_in_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $r) => $r->code ??= self::newCode());
    }

    public static function newCode(): string
    {
        do {
            $code = 'RA-'.collect(range(1, 6))->map(fn () => self::CODE_CHARS[random_int(0, strlen(self::CODE_CHARS) - 1)])->implode('');
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** wa.me wants the number in international form: 0812… becomes 62812…. */
    public function whatsappLink(): string
    {
        return 'https://wa.me/'.preg_replace('/^0/', '62', ltrim($this->whatsapp, '+'));
    }

    /** "Sari Uji Coba" becomes "S*** U** C***", for people checking a ticket they don't own. */
    public function maskedName(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))
            ->map(fn ($w) => mb_substr($w, 0, 1).str_repeat('*', max(2, mb_strlen($w) - 1)))
            ->implode(' ');
    }

    public function startsAt(): Carbon
    {
        return Carbon::parse($this->event->event_date->format('Y-m-d').' '.$this->session->starts_at, self::TIMEZONES[$this->event->timezone] ?? 'Asia/Jakarta');
    }

    /** Opens Google Calendar with the session filled in; sessions without an end time get 90 minutes. */
    public function calendarUrl(): string
    {
        $start = $this->startsAt();
        $end = $this->session->ends_at
            ? Carbon::parse($this->event->event_date->format('Y-m-d').' '.$this->session->ends_at, $start->timezone)
            : $start->copy()->addMinutes(90);
        $event = $this->event;

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $event->title.' ('.$this->session->label.')',
            'dates' => $start->utc()->format('Ymd\THis\Z').'/'.$end->utc()->format('Ymd\THis\Z'),
            'details' => 'Tiket: '.$this->code."\n".route('tickets.show', $this),
            'location' => $event->isOnline() ? ($event->meeting_url ?: 'Online') : trim($event->venue.', '.$event->address, ', '),
        ]);
    }
}
