<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'jp_value',
        'bidang_kompetensi',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jp_value' => 'integer',
        ];
    }

    /**
     * Get the modules for the course.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    /**
     * Get the user enrollments for the course.
     */
    public function userEnrollments(): HasMany
    {
        return $this->hasMany(UserEnrollment::class);
    }

    /**
     * Owner (instructor) of the course.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the certificates for the course.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the JP records for the course.
     */
    public function jpRecords(): HasMany
    {
        return $this->hasMany(JpRecord::class);
    }

    /**
     * Get the sub modules for the course through modules.
     */
    public function subModules(): HasManyThrough
    {
        return $this->hasManyThrough(SubModule::class, Module::class);
    }

    /**
     * Get the users who have wishlisted this course.
     */
    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_wishlists');
    }

    /**
     * Get the enrollments for the course (alias for userEnrollments).
     */
    public function enrollments(): HasMany
    {
        return $this->userEnrollments();
    }
} 