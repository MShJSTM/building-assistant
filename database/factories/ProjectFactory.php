<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'slug' => $this->faker->slug,
            'status' => $this->faker->word,
            'project_type' => $this->faker->word,
            'address' => $this->faker->address,
            'postal_code' => $this->faker->postcode,
            'land_area' => $this->faker->numberBetween(100, 10000),
            'building_area' => $this->faker->numberBetween(100, 10000),
            'structure_type' => $this->faker->word,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'permit_start_date' => $this->faker->date(),
            'permit_end_date' => $this->faker->date(),
        ];
    }
}
