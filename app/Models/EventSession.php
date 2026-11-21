<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSession extends Model
{
    protected $fillable = ['label', 'starts_at', 'ends_at', 'capacity', 'order'];

    protected $casts = ['capacity' => 'integer'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /** "14.00" or "14.00–15.30" */
    public function timeLabel(): string
    {
        $fmt = fn (?string $t) => $t ? str_replace(':', '.', substr($t, 0, 5)) : null;

        return $fmt($this->starts_at).($this->ends_at ? '–'.$fmt($this->ends_at) : '');
    }

    /** Uses the loaded registrations_count when present, so lists don't query once per session. */
    public function seatsLeft(): int
    {
        return max(0, $this->capacity - ($this->registrations_count ?? $this->registrations()->count()));
    }
}
