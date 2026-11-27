<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seat 3: a teacher records attendance for the section they advise; admin for any section.
 */
class AttendanceTest extends TestCase
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

    /** A valid sheet for the section; override any part with $overrides. */
    private function sheet(array $overrides = []): array
    {
        return array_merge([
            'section_id' => $this->section->id,
            'date' => today()->toDateString(),
            'statuses' => [$this->juan->id => 'present', $this->ana->id => 'absent'],
        ], $overrides);
    }

    public function test_the_adviser_sees_their_own_students_on_the_sheet(): void
    {
        $response = $this->actingAs($this->adviser)->get('/attendance');

        $response->assertOk();
        $response->assertSee($this->juan->user->name);
        $response->assertSee($this->ana->user->name);
    }

    public function test_a_teacher_who_is_not_an_adviser_sees_a_message_not_a_sheet(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)->get('/attendance')
            ->assertOk()
            ->assertSee('You are not an adviser of a section.')
            ->assertDontSee($this->juan->user->name);
    }

    public function test_a_teacher_asking_for_another_section_still_sees_only_their_own(): void
    {
        $otherSection = Section::factory()->create();
        $stranger = Student::factory()->for($otherSection)->create();

        $this->actingAs($this->adviser)->get('/attendance?section='.$otherSection->id)
            ->assertOk()
            ->assertSee($this->juan->user->name)
            ->assertDontSee($stranger->user->name);
    }

    public function test_the_admin_can_pick_any_section_on_the_sheet(): void
    {
        $admin = User::factory()->admin()->create();
        $otherSection = Section::factory()->create();
        $stranger = Student::factory()->for($otherSection)->create();

        $this->actingAs($admin)->get('/attendance?section='.$otherSection->id)
            ->assertOk()
            ->assertSee($stranger->user->name)
            ->assertSee('name="section"', false);
    }

    public function test_a_saved_status_is_shown_again_as_selected(): void
    {
        $this->actingAs($this->adviser)->post('/attendance', $this->sheet());

        $page = $this->actingAs($this->adviser)->get('/attendance?date='.today()->toDateString());

        // Ana was saved as absent, so her select shows "absent" as the selected option.
        $this->assertMatchesRegularExpression(
            '/<select id="status-'.$this->ana->id.'"[^>]*>.*?<option value="absent"\s+selected/s',
            $page->getContent()
        );
    }

    public function test_an_adviser_can_save_attendance_for_their_own_section(): void
    {
        $response = $this->actingAs($this->adviser)->post('/attendance', $this->sheet());

        $response->assertRedirect();
        $this->assertDatabaseCount('attendances', 2);
        $this->assertDatabaseHas('attendances', ['student_id' => $this->juan->id, 'status' => 'present', 'recorded_by' => $this->adviser->id]);
        $this->assertDatabaseHas('attendances', ['student_id' => $this->ana->id, 'status' => 'absent']);
    }

    public function test_a_teacher_cannot_save_attendance_for_another_teachers_section(): void
    {
        $other = User::factory()->teacher()->create();

        $this->actingAs($other)->post('/attendance', $this->sheet())->assertForbidden();

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_the_admin_can_save_attendance_for_any_section(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/attendance', $this->sheet())->assertRedirect();

        $this->assertDatabaseCount('attendances', 2);
        $this->assertDatabaseHas('attendances', ['recorded_by' => $admin->id]);
    }

    public function test_the_principal_and_students_cannot_save_attendance(): void
    {
        foreach ([User::factory()->principal()->create(), User::factory()->student()->create()] as $user) {
            $this->actingAs($user)->post('/attendance', $this->sheet())->assertForbidden();
        }

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_a_guest_is_sent_to_login(): void
    {
        $this->post('/attendance', $this->sheet())->assertRedirect('/login');
        $this->get('/attendance')->assertRedirect('/login');
    }

    public function test_a_future_date_is_refused(): void
    {
        $response = $this->actingAs($this->adviser)
            ->post('/attendance', $this->sheet(['date' => today()->addDay()->toDateString()]));

        $response->assertSessionHasErrors('date');
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_a_past_date_is_allowed(): void
    {
        $this->actingAs($this->adviser)
            ->post('/attendance', $this->sheet(['date' => today()->subDays(3)->toDateString()]))
            ->assertRedirect();

        $this->assertDatabaseCount('attendances', 2);
    }

    public function test_a_bad_status_is_refused(): void
    {
        $response = $this->actingAs($this->adviser)->post('/attendance', $this->sheet([
            'statuses' => [$this->juan->id => 'sleepy', $this->ana->id => 'present'],
        ]));

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_a_student_from_another_section_is_refused(): void
    {
        $stranger = Student::factory()->create();

        $response = $this->actingAs($this->adviser)->post('/attendance', $this->sheet([
            'statuses' => [$this->juan->id => 'present', $this->ana->id => 'present', $stranger->id => 'present'],
        ]));

        $response->assertSessionHasErrors('statuses');
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_recorded_by_in_the_form_is_ignored(): void
    {
        $someoneElse = User::factory()->teacher()->create();

        $this->actingAs($this->adviser)->post('/attendance', $this->sheet(['recorded_by' => $someoneElse->id]));

        $this->assertDatabaseMissing('attendances', ['recorded_by' => $someoneElse->id]);
        $this->assertDatabaseHas('attendances', ['recorded_by' => $this->adviser->id]);
    }
}
