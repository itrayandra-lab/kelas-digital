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

    public const FORMATS = ['offline' => 'Offline', 'online' => 'Online'];

    public const TIMEZONES = ['WIB', 'WITA', 'WIT'];

    protected $fillable = [
        'title', 'subtitle', 'badge', 'cover', 'description', 'format', 'event_date', 'timezone',
        'venue', 'address', 'maps_url', 'meeting_url', 'is_published', 'registration_open',
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

    public function seatsLeft(): int
    {
        return $this->sessions->sum(fn (EventSession $s) => $s->seatsLeft());
    }
}
