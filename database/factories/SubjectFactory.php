<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Random unique code (e.g. QWER7) so two factory subjects in one section never clash.
            'code' => strtoupper(fake()->unique()->lexify('????')).'7',
            'name' => fake()->randomElement(['Mathematics', 'Science', 'English', 'Filipino', 'Araling Panlipunan', 'MAPEH']),
            'section_id' => Section::factory(),
            'teacher_id' => User::factory()->teacher(),
        ];
    }
}
