<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulationQuestion extends Model
{
    use HasFactory;

    /**
     * Huruf pilihan jawaban yang valid.
     */
    public const OPTIONS = ['A', 'B', 'C', 'D'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'kunci_jawaban',
    ];

    /**
     * Get the answers given to this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(SimulationAnswer::class);
    }

    /**
     * Pilihan jawaban sebagai [huruf => teks].
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        return [
            'A' => $this->pilihan_a,
            'B' => $this->pilihan_b,
            'C' => $this->pilihan_c,
            'D' => $this->pilihan_d,
        ];
    }
}
