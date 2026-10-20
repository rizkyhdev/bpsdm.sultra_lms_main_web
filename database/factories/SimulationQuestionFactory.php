<?php

namespace Database\Factories;

use App\Models\SimulationQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SimulationQuestion>
 */
class SimulationQuestionFactory extends Factory
{
    protected $model = SimulationQuestion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pertanyaan' => fake()->sentence(12).'?',
            'pilihan_a' => fake()->sentence(),
            'pilihan_b' => fake()->sentence(),
            'pilihan_c' => fake()->sentence(),
            'pilihan_d' => fake()->sentence(),
            'kunci_jawaban' => fake()->randomElement(SimulationQuestion::OPTIONS),
        ];
    }
}
