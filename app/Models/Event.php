<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A free offline or online event; learners sign up for one of its sessions with their Ray Academy account. */
class Event extends Model
{
    use Sluggable;

    protected static function booted(): void
    {
        static::saved(function (Event $event) {
            if ($event->is_published) {
                \App\Services\IndexNow::ping([route('events.show', $event), route('events.index')]);
            }
        });
    }

    public const FORMATS = ['offline' => 'Offline', 'online' => 'Online'];

    public const TIMEZONES = ['WIB', 'WITA', 'WIT'];

    protected $fillable = [
        'title', 'subtitle', 'badge', 'cover', 'description', 'format', 'event_date', 'timezone',
        'venue', 'address', 'maps_url', 'meeting_url', 'whatsapp_template', 'is_published', 'registration_open',
    ];

    /** Used until the admin writes their own; the {...} parts are filled in per participant. */
    public const DEFAULT_WHATSAPP_TEMPLATE = <<<'TXT'
        Halo {nama}, terima kasih sudah mendaftar *{event}* di Ray Academy.

        Tanggal: {tanggal}
        Sesi: {sesi}
        Tempat: {lokasi}
        Kode tiket: {kode}

        Tiketmu bisa dibuka di {link_tiket}
        Tunjukkan QR di tiket itu ke panitia saat datang.

        Mohon balas *YA* untuk konfirmasi kehadiran. Sampai jumpa!
        TXT;

    /** Placeholders the admin can use, with what each becomes. */
    public const WHATSAPP_PLACEHOLDERS = [
        '{nama}' => 'nama peserta',
        '{event}' => 'judul event',
        '{tanggal}' => 'tanggal event',
        '{sesi}' => 'sesi dan jamnya',
        '{lokasi}' => 'tempat, atau "Online"',
        '{kode}' => 'kode tiket',
        '{link_tiket}' => 'link tiket berisi QR',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_published' => 'boolean',
        'registration_open' => 'boolean',
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'title', 'onUpdate' => false, 'unique' => true, 'maxLength' => 100, 'maxLengthKeepWords' => true]];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(EventSession::class)->orderBy('order')->orderBy('starts_at');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('event_date', '>=', today())->orderBy('event_date');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover ? asset('storage/'.$this->cover) : null;
    }

    public function isOnline(): bool
    {
        return $this->format === 'online';
    }

    public function isPast(): bool
    {
        return $this->event_date->lt(today());
    }

    /** Published, not over yet, and the admin hasn't closed sign-ups. Seats are checked per session. */
    public function acceptsRegistrations(): bool
    {
        return $this->is_published && $this->registration_open && ! $this->isPast();
    }

    public function dateLabel(): string
    {
        return $this->event_date->locale('id')->translatedFormat('l, j F Y');
    }

    /** "14.00 · 15.30 WITA" */
    public function timesLabel(): string
    {
        return $this->sessions->map->timeLabel()->implode(' · ').' '.$this->timezone;
    }

    /**
     * The admin writes plain text: blank lines split paragraphs, "## " starts a heading and "- " a bullet.
     * Everything is escaped, so no HTML from the form reaches the page.
     */
    public function descriptionHtml(): string
    {
        $html = '';
        $list = [];
        $paragraph = [];
        $flush = function () use (&$html, &$list, &$paragraph) {
            if ($paragraph) {
                $html .= '<p>'.implode('<br>', array_map('e', $paragraph)).'</p>';
            }
            if ($list) {
                $html .= '<ul>'.implode('', array_map(fn ($item) => '<li>'.e($item).'</li>', $list)).'</ul>';
            }
            $list = $paragraph = [];
        };

        foreach (preg_split('/\R/', trim((string) $this->description)) as $line) {
            $line = trim($line);
            if ($line === '') {
                $flush();
            } elseif (str_starts_with($line, '## ')) {
                $flush();
                $html .= '<h3>'.e(substr($line, 3)).'</h3>';
            } elseif (preg_match('/^[-•*]\s+(.+)$/u', $line, $m)) {
                if ($paragraph) {
                    $flush();
                }
                $list[] = $m[1];
            } else {
                if ($list) {
                    $flush();
                }
                $paragraph[] = $line;
            }
        }
        $flush();

        return $html;
    }

    /** Plain-text summary for meta descriptions, without the "## " and "- " markers. */
    public function plainDescription(): string
    {
        return trim(preg_replace(['/^##\s+/m', '/^[-•*]\s+/m', '/\s+/'], ['', '', ' '], (string) $this->description));
    }

    public function seatsLeft(): int
    {
        return $this->sessions->sum(fn (EventSession $s) => $s->seatsLeft());
    }
}
