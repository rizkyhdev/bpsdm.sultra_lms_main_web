<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'module_id',
        'sub_module_id',
        'judul',
        'deskripsi',
        'nilai_minimum',
        'max_attempts',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai_minimum' => 'float',
            'max_attempts' => 'integer',
        ];
    }

    /**
     * Get the course that owns the quiz.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the module that owns the quiz.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the sub module that owns the quiz.
     */
    public function subModule(): BelongsTo
    {
        return $this->belongsTo(SubModule::class);
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the quiz attempts for the quiz.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Check if a user has passed this quiz.
     */
    public function hasUserPassed($userId): bool
    {
        $passedAttempt = $this->quizAttempts()
            ->where('user_id', $userId)
            ->where('is_passed', true)
            ->first();
        
        return $passedAttempt !== null;
    }

    /**
     * Get the level of this quiz (course, module, or sub_module).
     */
    public function getLevel(): string
    {
        if ($this->sub_module_id) {
            return 'sub_module';
        } elseif ($this->module_id) {
            return 'module';
        } elseif ($this->course_id) {
            return 'course';
        }
        return 'unknown';
    }
} 