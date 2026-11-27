<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seat 4: a teacher lists and adds their own subjects.
 */
class SubjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // TODO (Seat 2): remove this line once the `role` middleware is merged,
        // so these tests also cover "only teachers may enter".
        $this->withoutMiddleware('role');
    }

    public function test_a_teacher_sees_only_their_own_subjects(): void
    {
        $teacher = User::factory()->teacher()->create();
        $mine = Subject::factory()->create(['teacher_id' => $teacher->id, 'code' => 'MINE7']);
        $other = Subject::factory()->create(['code' => 'OTHER7']);

        $this->actingAs($teacher)
            ->get(route('subjects.index'))
            ->assertOk()
            ->assertSee('MINE7')
            ->assertDontSee('OTHER7');
    }

    public function test_a_visitor_who_is_not_logged_in_is_sent_to_login(): void
    {
        $this->get(route('subjects.index'))->assertRedirect(route('login'));
        $this->post(route('subjects.store'), [])->assertRedirect(route('login'));
    }

    public function test_the_create_form_lists_the_sections(): void
    {
        $section = Section::factory()->create(['name' => 'Narra']);

        $this->actingAs(User::factory()->teacher()->create())
            ->get(route('subjects.create'))
            ->assertOk()
            ->assertSee('Narra');
    }

    public function test_a_teacher_can_add_a_subject_and_it_is_theirs(): void
    {
        $teacher = User::factory()->teacher()->create();
        $section = Section::factory()->create();

        $this->actingAs($teacher)
            ->post(route('subjects.store'), [
                'name' => 'Mathematics',
                'code' => 'MATH7',
                'section_id' => $section->id,
            ])
            ->assertRedirect(route('subjects.index'));

        $this->assertDatabaseHas('subjects', [
            'code' => 'MATH7',
            'section_id' => $section->id,
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_a_teacher_cannot_set_the_teacher_from_the_form(): void
    {
        $teacher = User::factory()->teacher()->create();
        $someoneElse = User::factory()->teacher()->create();

        $this->actingAs($teacher)->post(route('subjects.store'), [
            'name' => 'Science',
            'code' => 'SCI7',
            'section_id' => Section::factory()->create()->id,
            'teacher_id' => $someoneElse->id,
        ]);

        $this->assertDatabaseHas('subjects', ['code' => 'SCI7', 'teacher_id' => $teacher->id]);
    }

    public function test_the_same_code_twice_in_one_section_is_rejected(): void
    {
        $teacher = User::factory()->teacher()->create();
        $section = Section::factory()->create();
        Subject::factory()->create(['code' => 'MATH7', 'section_id' => $section->id]);

        $this->actingAs($teacher)
            ->post(route('subjects.store'), [
                'name' => 'Mathematics',
                'code' => 'MATH7',
                'section_id' => $section->id,
            ])
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('subjects', 1);
    }

    public function test_the_same_code_in_another_section_is_allowed(): void
    {
        $teacher = User::factory()->teacher()->create();
        Subject::factory()->create(['code' => 'MATH7']);

        $this->actingAs($teacher)
            ->post(route('subjects.store'), [
                'name' => 'Mathematics',
                'code' => 'MATH7',
                'section_id' => Section::factory()->create()->id,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('subjects', 2);
    }

    public function test_missing_or_bad_input_is_rejected(): void
    {
        $this->actingAs(User::factory()->teacher()->create())
            ->post(route('subjects.store'), ['section_id' => 99999])
            ->assertSessionHasErrors(['name', 'code', 'section_id']);

        $this->assertDatabaseCount('subjects', 0);
    }
}
