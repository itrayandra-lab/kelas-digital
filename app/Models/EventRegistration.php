<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    protected $fillable = ['event_id', 'event_session_id', 'user_id', 'name', 'email', 'whatsapp', 'age'];

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
}
