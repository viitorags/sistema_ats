<?php

namespace Database\Factories;

use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interview>
 */
class InterviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+2 weeks');
        $end = (clone $start)->modify('+1 hour');

        return [
            'summary' => 'Entrevista: '.fake()->name(),
            'description' => fake()->sentence(),
            'location' => fake()->randomElement(['Remoto (Google Meet)', 'Remoto (Zoom)', 'Presencial - Sala A']),
            'start_time' => $start,
            'end_time' => $end,
            'event_link' => fake()->url(),
            'status' => fake()->randomElement(['Confirmada', 'Pendente', 'Concluída']),
        ];
    }
}
