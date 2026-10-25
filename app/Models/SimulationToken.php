<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulationToken extends Model
{
    /**
     * Batas waktu pengerjaan terpanjang yang dapat diatur admin (5 jam).
     */
    public const MAX_DURASI_MENIT = 300;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode',
        'sesi',
        'jumlah_soal',
        'durasi_menit',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_soal' => 'integer',
            'durasi_menit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the attempts started with this token.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(SimulationAttempt::class);
    }

    /**
     * Scope a query to the active token(s).
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
