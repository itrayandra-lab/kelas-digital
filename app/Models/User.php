<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use CausesActivity, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'last_login',
        'origin_type',
        'institution',
    ];

    /** Where a learner comes from; the institution name is asked for everyone except the general public. */
    public const ORIGINS = [
        'mahasiswa' => 'Mahasiswa / Universitas',
        'karyawan' => 'Karyawan / Perusahaan',
        'umum' => 'Umum / Pribadi',
    ];

    /** Shared by sign-up (required) and the profile page (optional, for accounts made before it was asked). */
    public static function originRules(bool $required): array
    {
        return [
            'origin_type' => [$required ? 'required' : 'nullable', 'in:'.implode(',', array_keys(self::ORIGINS))],
            'institution' => ['nullable', 'required_if:origin_type,mahasiswa,karyawan', 'string', 'max:150'],
        ];
    }

    /** "Universitas Padjadjaran", "RayCorp", "Umum", or null when the learner never said. */
    public function origin(): ?string
    {
        return $this->origin_type === 'umum' ? 'Umum' : $this->institution;
    }

    /** Suggestions for the sign-up form; only names several people share, so a one-off entry is never shown to others. */
    public static function knownInstitutions(): array
    {
        return self::whereNotNull('institution')->groupBy('institution')->havingRaw('count(*) >= 3')
            ->orderBy('institution')->limit(300)->pluck('institution')->all();
    }

    protected function setInstitutionAttribute(?string $value): void
    {
        $this->attributes['institution'] = $value === null ? null : (\Illuminate\Support\Str::squish($value) ?: null);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
        ];
    }

    /**
     * Get user's enrolled courses
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function completedLessons()
    {
        // lesson_progress also holds unfinished rows (video watch position), so only finished ones count.
        return $this->belongsToMany(Lesson::class, 'lesson_progress')->withPivot('completed_at')->withTimestamps()
            ->wherePivotNotNull('completed_at');
    }

    /**
     * Courses the user studies: paid or free ones, plus partly free ones started with "Mulai gratis".
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->where(fn ($q) => $q->where('enrollments.payment_status', 'completed')->orWhere('enrollments.payment_method', 'trial'));
    }

    /**
     * Check if user is an admin (has admin role)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a super admin (has Super-Admin role)
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super-Admin');
    }

    /**
     * Check if user is an instructor
     */
    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    /**
     * Check if user is a content manager
     */
    public function isContentManager(): bool
    {
        return $this->hasRole('content-manager');
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Check if user can manage courses
     */
    public function canManageCourses(): bool
    {
        return $this->can(['create courses', 'edit courses', 'delete courses']);
    }

    /**
     * Check if user can manage articles
     */
    public function canManageArticles(): bool
    {
        return $this->can(['create articles', 'edit articles', 'delete articles']);
    }

    /**
     * Find user by username or email
     */
    public static function findByUsernameOrEmail($login)
    {
        return static::where('username', $login)
            ->orWhere('email', $login)
            ->first();
    }

    /**
     * Get validation rules for username
     */
    public static function getUsernameRules()
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:20', 'unique:users', 'regex:/^[a-zA-Z0-9_-]+$/'],
        ];
    }
}
