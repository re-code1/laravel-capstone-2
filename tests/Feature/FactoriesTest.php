<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Mark;
use App\Models\Note;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Checks that every factory can build a valid row, and that the database
 * itself enforces the schema rules (ENUMs, unique keys).
 */
class FactoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_sets_a_role_and_has_role_states(): void
    {
        $this->assertSame('student', User::factory()->create()->role);
        $this->assertSame('admin', User::factory()->admin()->create()->role);
        $this->assertSame('principal', User::factory()->principal()->create()->role);
        $this->assertSame('teacher', User::factory()->teacher()->create()->role);
    }

    public function test_every_factory_creates_a_row_with_its_parents(): void
    {
        $mark = Mark::factory()->create();
        $this->assertSame('student', $mark->student->user->role);
        $this->assertSame('teacher', $mark->subject->teacher->role);
        $this->assertSame('teacher', $mark->subject->section->adviser->role);

        $this->assertNotNull(Attendance::factory()->create()->recorder);
        $this->assertNotNull(Note::factory()->create()->author);
        $this->assertSame(1, Section::factory()->withoutAdviser()->count(1)->create()->count());
    }

    public function test_leave_request_states(): void
    {
        $pending = LeaveRequest::factory()->create();
        $this->assertTrue($pending->fresh()->isPending());   // fresh(): read back what the database stored
        $this->assertNull($pending->decided_by);

        $approved = LeaveRequest::factory()->approved()->create();
        $this->assertSame('approved', $approved->status);
        $this->assertSame('principal', $approved->decider->role);
    }

    public function test_role_is_not_mass_assignable(): void
    {
        $user = User::factory()->create();
        $user->update(['role' => 'admin']);   // what a sneaky form would try

        $this->assertSame('student', $user->fresh()->role);
    }

    public function test_database_rejects_a_role_outside_the_enum(): void
    {
        $this->expectException(QueryException::class);
        User::factory()->create()->forceFill(['role' => 'superuser'])->save();
    }

    public function test_one_mark_per_student_subject_and_quarter(): void
    {
        $student = Student::factory()->create();
        $subject = Subject::factory()->create(['section_id' => $student->section_id]);
        Mark::factory()->for($student)->for($subject)->create(['quarter' => 1]);

        $this->expectException(QueryException::class);
        Mark::factory()->for($student)->for($subject)->create(['quarter' => 1]);
    }
}
