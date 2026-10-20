<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulationAttempt extends Model
{
    /**
     * Jawaban yang tiba sesaat setelah waktu habis tetap diterima (latensi jaringan).
     */
    public const GRACE_SECONDS = 15;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'simulation_token_id',
        'question_ids',
        'jumlah_soal',
        'durasi_menit',
        'started_at',
        'ends_at',
        'completed_at',
        'skor',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_ids' => 'array',
            'jumlah_soal' => 'integer',
            'durasi_menit' => 'integer',
            'started_at' => 'datetime',
            'ends_at' => 'datetime',
            'completed_at' => 'datetime',
            'skor' => 'integer',
        ];
    }

    /**
     * Get the participant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the token (session) used to start this attempt.
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(SimulationToken::class, 'simulation_token_id');
    }

    /**
     * Get the answers of this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(SimulationAnswer::class);
    }

    /**
     * Scope a query to attempts whose time ran out without being finished.
     */
    public function scopeExpired(Builder $query): void
    {
        $query->whereNull('completed_at')->where('ends_at', '<=', now());
    }

    public function isFinished(): bool
    {
        return $this->completed_at !== null;
    }

    public function isExpired(): bool
    {
        return ! $this->isFinished() && now()->gte($this->ends_at);
    }

    public function acceptsAnswers(): bool
    {
        return ! $this->isFinished() && now()->lte($this->ends_at->copy()->addSeconds(self::GRACE_SECONDS));
    }

    public function remainingSeconds(): int
    {
        return max(0, (int) floor(now()->diffInSeconds($this->ends_at, false)));
    }

    /**
     * Status yang ditampilkan: Selesai, Sedang Mengerjakan, atau Waktu Habis.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->isFinished() => 'Selesai',
            $this->isExpired() => 'Waktu Habis',
            default => 'Sedang Mengerjakan',
        });
    }
}
