<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
