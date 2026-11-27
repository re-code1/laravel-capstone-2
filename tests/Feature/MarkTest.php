<?php

namespace Tests\Feature;

use App\Models\Mark;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seat 4: a teacher enters marks (one quarter at a time) for their own subjects.
 */
class MarkTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;

    private Section $section;

    private Subject $subject;

    private Student $juan;

    private Student $ana;

    protected function setUp(): void
    {
        parent::setUp();

        // TODO (Seat 2): remove this line once the `role` middleware is merged.
        $this->withoutMiddleware('role');

        $this->teacher = User::factory()->teacher()->create();
        $this->section = Section::factory()->create();
        $this->subject = Subject::factory()->create([
            'section_id' => $this->section->id,
            'teacher_id' => $this->teacher->id,
        ]);
        $this->juan = Student::factory()->for($this->section)->create([
            'user_id' => User::factory()->student()->create(['name' => 'Juan Dela Cruz'])->id,
        ]);
        $this->ana = Student::factory()->for($this->section)->create([
            'user_id' => User::factory()->student()->create(['name' => 'Ana Santos'])->id,
        ]);
    }

    private function save(array $scores, int|string $quarter = 1, ?Subject $subject = null, ?User $as = null)
    {
        return $this->actingAs($as ?? $this->teacher)->put(
            route('marks.update', $subject ?? $this->subject),
            ['quarter' => $quarter, 'scores' => $scores],
        );
    }

    // ---------- who may do what ----------

    public function test_a_visitor_who_is_not_logged_in_is_sent_to_login(): void
    {
        $this->get(route('marks.edit', $this->subject))->assertRedirect(route('login'));
        $this->put(route('marks.update', $this->subject), [])->assertRedirect(route('login'));
    }

    public function test_a_teacher_can_open_the_marks_page_of_their_own_subject(): void
    {
        $this->actingAs($this->teacher)
            ->get(route('marks.edit', $this->subject))
            ->assertOk()
            ->assertSee('Juan Dela Cruz')
            ->assertSee('Ana Santos');
    }

    public function test_another_teacher_gets_403_on_the_marks_page(): void
    {
        $this->actingAs(User::factory()->teacher()->create())
            ->get(route('marks.edit', $this->subject))
            ->assertForbidden();
    }

    public function test_another_teacher_gets_403_when_saving_and_nothing_is_saved(): void
    {
        $this->save([$this->juan->id => 90], as: User::factory()->teacher()->create())
            ->assertForbidden();

        $this->assertDatabaseCount('marks', 0);
    }

    public function test_an_unknown_subject_gets_404(): void
    {
        $this->actingAs($this->teacher)->get('/subjects/99999/marks')->assertNotFound();
    }

    // ---------- the page ----------

    public function test_only_students_of_the_subjects_section_are_listed(): void
    {
        $stranger = Student::factory()->create([
            'user_id' => User::factory()->student()->create(['name' => 'Pedro Outsider'])->id,
        ]);

        $this->actingAs($this->teacher)
            ->get(route('marks.edit', $this->subject))
            ->assertDontSee('Pedro Outsider');
    }

    public function test_the_page_shows_the_score_of_the_chosen_quarter_only(): void
    {
        Mark::factory()->for($this->juan)->for($this->subject)->create(['quarter' => 1, 'score' => 81.50]);
        Mark::factory()->for($this->juan)->for($this->subject)->create(['quarter' => 2, 'score' => 92.25]);

        $this->actingAs($this->teacher)
            ->get(route('marks.edit', ['subject' => $this->subject, 'quarter' => 2]))
            ->assertSee('92.25')
            ->assertDontSee('81.50');
    }

    public function test_the_quarter_defaults_to_1_and_must_be_1_to_4(): void
    {
        $this->actingAs($this->teacher)
            ->get(route('marks.edit', $this->subject))
            ->assertOk()
            ->assertSee('Quarter 1 score');

        $this->actingAs($this->teacher)
            ->get(route('marks.edit', ['subject' => $this->subject, 'quarter' => 5]))
            ->assertSessionHasErrors('quarter');
    }

    // ---------- saving ----------

    public function test_a_teacher_can_save_scores_for_their_subject(): void
    {
        $this->save([$this->juan->id => 88, $this->ana->id => 95.5], quarter: 2)
            ->assertRedirect(route('marks.edit', ['subject' => $this->subject, 'quarter' => 2]));

        $this->assertDatabaseHas('marks', [
            'student_id' => $this->juan->id, 'subject_id' => $this->subject->id, 'quarter' => 2, 'score' => 88,
        ]);
        $this->assertDatabaseHas('marks', [
            'student_id' => $this->ana->id, 'subject_id' => $this->subject->id, 'quarter' => 2, 'score' => 95.5,
        ]);
    }

    public function test_saving_again_updates_the_mark_instead_of_adding_a_second_one(): void
    {
        $this->save([$this->juan->id => 80]);
        $this->save([$this->juan->id => 85]);

        $this->assertDatabaseCount('marks', 1);
        $this->assertDatabaseHas('marks', ['student_id' => $this->juan->id, 'score' => 85]);
    }

    public function test_an_empty_box_means_no_mark_and_removes_an_existing_mark(): void
    {
        Mark::factory()->for($this->juan)->for($this->subject)->create(['quarter' => 1, 'score' => 90]);

        $this->save([$this->juan->id => '', $this->ana->id => ''])->assertRedirect();

        $this->assertDatabaseCount('marks', 0);
    }

    public function test_emptying_a_box_does_not_touch_other_quarters(): void
    {
        Mark::factory()->for($this->juan)->for($this->subject)->create(['quarter' => 2, 'score' => 90]);

        $this->save([$this->juan->id => ''], quarter: 1);

        $this->assertDatabaseHas('marks', ['quarter' => 2, 'student_id' => $this->juan->id]);
    }

    public function test_scores_from_0_to_100_with_up_to_2_decimals_are_accepted(): void
    {
        foreach ([0, 59.99, 100, 75.5] as $score) {
            $this->save([$this->juan->id => $score])->assertSessionHasNoErrors();
        }
    }

    public function test_bad_scores_are_rejected_with_422_style_errors_and_nothing_is_saved(): void
    {
        foreach ([-1, 100.01, 101, 85.555, 'abc'] as $bad) {
            $this->save([$this->juan->id => $bad, $this->ana->id => 90])
                ->assertSessionHasErrors('scores.'.$this->juan->id);
        }

        $this->assertDatabaseCount('marks', 0);
    }

    public function test_a_bad_quarter_is_rejected(): void
    {
        foreach ([0, 5, 'x'] as $quarter) {
            $this->save([$this->juan->id => 90], quarter: $quarter)->assertSessionHasErrors('quarter');
        }

        $this->assertDatabaseCount('marks', 0);
    }

    public function test_a_student_from_another_section_cannot_be_graded(): void
    {
        $stranger = Student::factory()->create();

        $this->save([$stranger->id => 90, $this->juan->id => 80])->assertSessionHasErrors('scores');

        $this->assertDatabaseCount('marks', 0);
    }

    public function test_missing_scores_are_rejected(): void
    {
        $this->actingAs($this->teacher)
            ->put(route('marks.update', $this->subject), ['quarter' => 1])
            ->assertSessionHasErrors('scores');
    }
}
