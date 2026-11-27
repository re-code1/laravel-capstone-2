<?php

namespace Database\Factories;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mark>
 *
 * Note: by default the student and the subject are in different (new) sections.
 * In tests that care, pass both: Mark::factory()->for($student)->for($subject)->create().
 */
class MarkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'quarter' => 1,
            'score' => fake()->randomFloat(2, 70, 98),
        ];
    }
}
