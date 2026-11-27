<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'student_no' => fake()->unique()->numerify('2026-####'),
            'section_id' => Section::factory(),
        ];
    }
}
