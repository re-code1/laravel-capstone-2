<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Seat 3: who may record attendance for a section.
 */
class AttendancePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_adviser_may_record_attendance_for_their_own_section(): void
    {
        $adviser = User::factory()->teacher()->create();
        $section = Section::factory()->create(['adviser_id' => $adviser->id]);

        $this->assertTrue(Gate::forUser($adviser)->allows('record', [Attendance::class, $section]));
    }

    public function test_a_teacher_may_not_record_attendance_for_another_teachers_section(): void
    {
        $other = User::factory()->teacher()->create();
        $section = Section::factory()->create();

        $this->assertFalse(Gate::forUser($other)->allows('record', [Attendance::class, $section]));
    }

    public function test_nobody_may_record_attendance_for_a_section_with_no_adviser(): void
    {
        $teacher = User::factory()->teacher()->create();
        $section = Section::factory()->withoutAdviser()->create();

        $this->assertFalse(Gate::forUser($teacher)->allows('record', [Attendance::class, $section]));
    }

    public function test_the_admin_may_record_attendance_for_any_section(): void
    {
        $admin = User::factory()->admin()->create();
        $section = Section::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('record', [Attendance::class, $section]));
    }

    public function test_the_principal_and_students_may_not_record_attendance(): void
    {
        $section = Section::factory()->create();

        foreach ([User::factory()->principal()->create(), User::factory()->student()->create()] as $user) {
            $this->assertFalse(Gate::forUser($user)->allows('record', [Attendance::class, $section]));
        }
    }
}
