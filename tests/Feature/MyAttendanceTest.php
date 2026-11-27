<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seat 3: a student sees only their own attendance at /my/attendance.
 */
class MyAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Student $juan;

    private Student $ana;

    private User $adviser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adviser = User::factory()->teacher()->create();
        $section = Section::factory()->create(['adviser_id' => $this->adviser->id]);
        $this->juan = Student::factory()->for($section)->create();
        $this->ana = Student::factory()->for($section)->create();
    }

    private function record(Student $student, string $date, string $status): void
    {
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => $date,
            'status' => $status,
            'recorded_by' => $this->adviser->id,
        ]);
    }

    public function test_a_student_sees_their_own_attendance_with_status_badges(): void
    {
        $this->record($this->juan, '2026-11-26', 'present');
        $this->record($this->juan, '2026-11-27', 'late');

        $this->actingAs($this->juan->user)->get('/my/attendance')
            ->assertOk()
            ->assertSee('Thu, Nov 26, 2026')
            ->assertSee('badge-present', false)
            ->assertSee('badge-late', false)
            ->assertSee('Present 1, Absent 0, Late 1, Excused 0');
    }

    public function test_a_student_never_sees_another_students_attendance(): void
    {
        $this->record($this->juan, '2026-11-26', 'present');
        $this->record($this->ana, '2026-11-25', 'absent');

        $this->actingAs($this->juan->user)->get('/my/attendance')
            ->assertOk()
            ->assertSee('Thu, Nov 26, 2026')
            ->assertDontSee('Wed, Nov 25, 2026')
            ->assertDontSee('badge-absent', false);
    }

    public function test_a_student_with_no_records_sees_a_friendly_message(): void
    {
        $this->actingAs($this->juan->user)->get('/my/attendance')
            ->assertOk()
            ->assertSee('No attendance recorded yet.');
    }

    public function test_a_teacher_and_the_principal_get_403(): void
    {
        foreach ([$this->adviser, User::factory()->principal()->create()] as $user) {
            $this->actingAs($user)->get('/my/attendance')->assertForbidden();
        }
    }

    public function test_the_admin_sees_a_short_message_not_an_error(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/my/attendance')
            ->assertOk()
            ->assertSee('Only students have attendance records.');
    }

    public function test_a_guest_is_sent_to_login(): void
    {
        $this->get('/my/attendance')->assertRedirect('/login');
    }
}
