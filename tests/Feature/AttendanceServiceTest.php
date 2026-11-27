<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seat 3: the attendance service saves one row per student per day.
 */
class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $adviser;

    private Section $section;

    private Student $juan;

    private Student $ana;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adviser = User::factory()->teacher()->create();
        $this->section = Section::factory()->create(['adviser_id' => $this->adviser->id]);
        $this->juan = Student::factory()->for($this->section)->create();
        $this->ana = Student::factory()->for($this->section)->create();
    }

    public function test_it_creates_one_row_per_student_for_the_day(): void
    {
        (new AttendanceService)->record($this->section, '2026-11-27', [
            $this->juan->id => 'present',
            $this->ana->id => 'late',
        ], $this->adviser);

        $this->assertDatabaseCount('attendances', 2);
        $this->assertDatabaseHas('attendances', ['student_id' => $this->juan->id, 'status' => 'present']);
        $this->assertDatabaseHas('attendances', ['student_id' => $this->ana->id, 'status' => 'late']);
    }

    public function test_recording_the_same_day_again_updates_instead_of_duplicating(): void
    {
        $service = new AttendanceService;
        $service->record($this->section, '2026-11-27', [$this->juan->id => 'present', $this->ana->id => 'present'], $this->adviser);
        $service->record($this->section, '2026-11-27', [$this->juan->id => 'absent', $this->ana->id => 'present'], $this->adviser);

        $this->assertDatabaseCount('attendances', 2);
        $this->assertSame('absent', Attendance::where('student_id', $this->juan->id)->first()->status);
    }

    public function test_it_records_who_recorded_it(): void
    {
        (new AttendanceService)->record($this->section, '2026-11-27', [$this->juan->id => 'present', $this->ana->id => 'present'], $this->adviser);

        $this->assertSame(2, Attendance::where('recorded_by', $this->adviser->id)->count());
    }

    public function test_a_different_day_creates_new_rows(): void
    {
        $service = new AttendanceService;
        $service->record($this->section, '2026-11-26', [$this->juan->id => 'present', $this->ana->id => 'present'], $this->adviser);
        $service->record($this->section, '2026-11-27', [$this->juan->id => 'present', $this->ana->id => 'present'], $this->adviser);

        $this->assertDatabaseCount('attendances', 4);
    }

    public function test_it_ignores_students_who_are_not_in_the_section(): void
    {
        $stranger = Student::factory()->create();

        (new AttendanceService)->record($this->section, '2026-11-27', [
            $this->juan->id => 'present',
            $stranger->id => 'present',
        ], $this->adviser);

        $this->assertDatabaseMissing('attendances', ['student_id' => $stranger->id]);
        $this->assertDatabaseCount('attendances', 1);
    }
}
