<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\CausesActivity;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, CausesActivity;

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
    ];

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
        return $this->hasMany(\App\Models\Enrollment::class);
    }

    /**
     * Get user's enrolled courses that have completed payment
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(\App\Models\Course::class, 'enrollments')
                    ->wherePivot('payment_status', 'completed');
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
            'username' => ['required', 'string', 'min:3', 'max:20', 'unique:users', 'regex:/^[a-zA-Z0-9_-]+$/']
        ];
    }
}
