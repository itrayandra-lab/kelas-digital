<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'payment_status',
        'payment_method',
        'payment_proof',
        'snap_token',
        'transaction_id',
        'midtrans_response',
        'enrolled_at',
        'voucher_id',
        'paid_amount',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'midtrans_response' => 'array',
    ];

    /**
     * Get the user that owns the enrollment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that the enrollment belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Opens the course. Midtrans may confirm the same payment twice (browser callback and webhook),
     * so the voucher is counted only on the first switch to completed.
     */
    public function markPaid(array $attributes = []): void
    {
        DB::transaction(function () use ($attributes) {
            $wasPaid = static::whereKey($this->id)->lockForUpdate()->value('payment_status') === 'completed';

            $this->update($attributes + ['payment_status' => 'completed', 'status' => 'active']);

            if (! $wasPaid && $this->voucher_id) {
                Voucher::whereKey($this->voucher_id)->increment('used_count');
            }
        });
    }
}
