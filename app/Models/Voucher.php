<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'course_id', 'max_uses', 'used_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }

    public static function findByCode(?string $code): ?self
    {
        $code = strtoupper(trim((string) $code));

        return $code === '' ? null : static::where('code', $code)->first();
    }

    /** Why this voucher can't be used for the course right now, or null when it can. */
    public function problemFor(Course $course): ?string
    {
        return match (true) {
            ! $this->is_active => 'Voucher ini sudah tidak aktif.',
            $this->starts_at && $this->starts_at->isFuture() => 'Voucher ini belum bisa dipakai.',
            $this->expires_at && $this->expires_at->isPast() => 'Voucher ini sudah kedaluwarsa.',
            $this->max_uses !== null && $this->used_count >= $this->max_uses => 'Kuota voucher ini sudah habis.',
            $this->course_id !== null && $this->course_id !== $course->id => 'Voucher ini tidak berlaku untuk kelas ini.',
            default => null,
        };
    }

    public function discountFor(int $price): int
    {
        $discount = $this->discount_type === 'percent'
            ? intdiv($price * min($this->discount_value, 100), 100)
            : $this->discount_value;

        return min($discount, $price);
    }

    public function label(): string
    {
        return $this->discount_type === 'percent'
            ? $this->discount_value.'%'
            : 'Rp'.number_format($this->discount_value, 0, ',', '.');
    }
}
