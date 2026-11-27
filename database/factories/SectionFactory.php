<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Sampaguita', 'Narra', 'Ilang-Ilang', 'Molave', 'Gumamela',
                'Rosal', 'Dama de Noche', 'Acacia', 'Mahogany', 'Kamagong',
            ]),
            'grade_level' => 7,
            'adviser_id' => User::factory()->teacher(),
        ];
    }

    /** Section::factory()->withoutAdviser()->create() */
    public function withoutAdviser(): static
    {
        return $this->state(fn () => ['adviser_id' => null]);
    }
}
