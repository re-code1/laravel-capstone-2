<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'author_id' => User::factory()->teacher(),
            'body' => fake()->sentence(12),
        ];
    }
}
