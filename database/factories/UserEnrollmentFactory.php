<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserEnrollment>
 */
class UserEnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrolledAt = $this->faker->dateTimeBetween('-18 months', '-1 months');
        $completed = $this->faker->boolean(20);
        $completionPercent = $completed ? 100 : $this->faker->numberBetween(0, 99);
        
        return [
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'course_id' => Course::factory()->create()->id,
            'enrollment_date' => $enrolledAt,
            'status' => $completed ? 'completed' : $this->faker->randomElement(['enrolled', 'in_progress']),
            'completion_percent' => $completionPercent,
            'completed_at' => $completed ? $this->faker->dateTimeBetween($enrolledAt, 'now') : null,
        ];
    }

    /**
     * Indicate that the enrollment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completion_percent' => 100,
            'status' => 'completed',
            'completed_at' => $this->faker->dateTimeBetween($attributes['enrollment_date'], 'now'),
        ]);
    }
}
