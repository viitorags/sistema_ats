<?php

namespace Database\Factories;

use App\Models\Vacancie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vacancie>
 */
class VacancieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->city().', '.fake()->stateAbbr(),
            'is_remote' => fake()->boolean(),
            'active' => true,
        ];
    }
}
