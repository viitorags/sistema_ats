<?php

namespace Database\Factories;

use App\Models\Resume;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Resume>
 */
class ResumeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'filename' => fake()->word().'.pdf',
            'candidate_name' => fake()->name(),
            'candidate_email' => fake()->unique()->safeEmail(),
            'candidate_phone' => fake()->phoneNumber(),
            'score' => fake()->numberBetween(30, 100),
            'technical_score' => fake()->numberBetween(40, 100),
            'match_score' => fake()->numberBetween(50, 100),
            'summary' => fake()->sentence(20),
            'skills' => implode(', ', fake()->words(5)),
            'category' => fake()->randomElement(['Tecnologia', 'Vendas', 'Marketing', 'RH', 'Financeiro']),
            'processing_time_ms' => fake()->numberBetween(500, 2000),
        ];
    }
}
