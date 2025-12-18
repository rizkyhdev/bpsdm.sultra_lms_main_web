<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->judul);
            }
        });

        static::updating(function ($course) {
            if ($course->isDirty('judul') && empty($course->slug)) {
                $course->slug = Str::slug($course->judul);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'jp_value',
        'bidang_kompetensi',
        'user_id',
        'start_date_time',
        'end_date_time',
        'updated_by',
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
            // Rely on Laravel's standard datetime casting; controller provides UTC values
            'start_date_time' => 'datetime',
            'end_date_time'   => 'datetime',
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
     * Get the user who last updated the course schedule.
     */
    public function scheduleUpdater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    /**
     * Get the quizzes for the course.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the title attribute (alias for judul).
     */
    public function getTitleAttribute(): string
    {
        return $this->judul;
    }

    /**
     * Get the instructor name.
     */
    public function getInstructorNameAttribute(): string
    {
        return $this->owner?->name ?? '';
    }

    /**
     * Schedule status constants.
     */
    public const SCHEDULE_STATUS_BEFORE_START = 'BEFORE_START';
    public const SCHEDULE_STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const SCHEDULE_STATUS_AFTER_END = 'AFTER_END';
    public const SCHEDULE_STATUS_ALWAYS_OPEN = 'ALWAYS_OPEN';

    /**
     * Get the schedule status for a given UTC time.
     *
     * @param CarbonImmutable|null $nowUtc
     * @return string
     */
    public function scheduleStatus(?CarbonImmutable $nowUtc = null): string
    {
        $now = $nowUtc ?? CarbonImmutable::now('UTC');

        // If no start and no end, always open
        if (!$this->start_date_time && !$this->end_date_time) {
            return self::SCHEDULE_STATUS_ALWAYS_OPEN;
        }

        $start = $this->start_date_time ? CarbonImmutable::parse($this->start_date_time, 'UTC') : null;
        $end = $this->end_date_time ? CarbonImmutable::parse($this->end_date_time, 'UTC') : null;

        // Before start
        if ($start && $now->lt($start)) {
            return self::SCHEDULE_STATUS_BEFORE_START;
        }

        // After end
        if ($end && $now->gte($end)) {
            return self::SCHEDULE_STATUS_AFTER_END;
        }

        // In progress (between start and end, or after start with no end, or before end with no start)
        return self::SCHEDULE_STATUS_IN_PROGRESS;
    }

    /**
     * Get the next boundary (start or end) in UTC.
     *
     * @param CarbonImmutable|null $nowUtc
     * @return CarbonImmutable|null
     */
    public function nextBoundaryUtc(?CarbonImmutable $nowUtc = null): ?CarbonImmutable
    {
        $now = $nowUtc ?? CarbonImmutable::now('UTC');
        $status = $this->scheduleStatus($now);

        $start = $this->start_date_time ? CarbonImmutable::parse($this->start_date_time, 'UTC') : null;
        $end = $this->end_date_time ? CarbonImmutable::parse($this->end_date_time, 'UTC') : null;

        if ($status === self::SCHEDULE_STATUS_BEFORE_START && $start) {
            return $start;
        }

        if ($status === self::SCHEDULE_STATUS_IN_PROGRESS && $end) {
            return $end;
        }

        return null;
    }

    /**
     * Check if enrollment is allowed at the given UTC time.
     *
     * @param CarbonImmutable|null $nowUtc
     * @return bool
     */
    public function canEnroll(?CarbonImmutable $nowUtc = null): bool
    {
        $status = $this->scheduleStatus($nowUtc);
        return $status === self::SCHEDULE_STATUS_IN_PROGRESS || $status === self::SCHEDULE_STATUS_ALWAYS_OPEN;
    }
} 