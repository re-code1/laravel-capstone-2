<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    /** A pending request from a student, starting tomorrow, for 2 days. */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'start_date' => today()->addDay(),
            'end_date' => today()->addDays(2),
            'reason' => fake()->randomElement([
                'Family emergency in the province.',
                'Fever and cough, advised to rest by the doctor.',
                'Dental appointment.',
                'Attending a relative\'s wedding.',
            ]),
            'status' => 'pending',
        ];
    }

    /** LeaveRequest::factory()->approved()->create() (decided by a principal) */
    public function approved(): static
    {
        return $this->decided('approved');
    }

    /** LeaveRequest::factory()->rejected()->create() (decided by a principal) */
    public function rejected(): static
    {
        return $this->decided('rejected');
    }

    private function decided(string $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'decided_by' => User::factory()->principal(),
            'decided_at' => now(),
        ]);
    }
}
