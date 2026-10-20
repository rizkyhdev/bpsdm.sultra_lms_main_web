<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationAnswer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'simulation_attempt_id',
        'simulation_question_id',
        'jawaban',
        'is_correct',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    /**
     * Get the attempt this answer belongs to.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(SimulationAttempt::class, 'simulation_attempt_id');
    }

    /**
     * Get the answered question.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SimulationQuestion::class, 'simulation_question_id');
    }
}
