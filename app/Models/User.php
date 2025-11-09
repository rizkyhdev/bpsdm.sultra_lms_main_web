<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'jabatan',
        'unit_kerja',
        'role',
        'is_validated',
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
            'is_validated' => 'boolean',
        ];
    }

    /**
     * Get the user enrollments for the user.
     */
    public function userEnrollments(): HasMany
    {
        return $this->hasMany(UserEnrollment::class);
    }

    /**
     * Get the user progress records for the user.
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Get the quiz attempts for the user.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the certificates for the user.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the JP records for the user.
     */
    public function jpRecords(): HasMany
    {
        return $this->hasMany(JpRecord::class);
    }

    /**
     * Get the courses that the user has wishlisted.
     */
    public function wishlists(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'user_wishlists')->withTimestamps();
    }

    /**
     * Get the enrollments for the user (alias for userEnrollments).
     */
    public function enrollments(): HasMany
    {
        return $this->userEnrollments();
    }
}
