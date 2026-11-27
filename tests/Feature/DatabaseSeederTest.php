<?php

namespace Tests\Feature;

use App\Models\LeaveRequest;
use App\Models\Mark;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The capstone's "Check the schema" list, as a test, plus the demo data the brief asks for.
 */
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_five_required_logins_exist_with_password_password(): void
    {
        foreach ([
            'admin@school.edu.ph' => 'admin',
            'principal@school.edu.ph' => 'principal',
            'liza@school.edu.ph' => 'teacher',
            'ramon@school.edu.ph' => 'teacher',
            'juan@school.edu.ph' => 'student',
        ] as $email => $role) {
            $user = User::where('email', $email)->first();
            $this->assertNotNull($user, "$email is missing");
            $this->assertSame($role, $user->role);
            $this->assertTrue(Hash::check('password', $user->password));
        }
    }

    public function test_juans_subjects_are_listed(): void
    {
        $subjects = User::where('email', 'juan@school.edu.ph')->first()->student->section->subjects->pluck('name');

        $this->assertEqualsCanonicalizing(['Mathematics', 'Science', 'English', 'Filipino'], $subjects->all());
    }

    public function test_three_teachers_and_no_user_without_a_role(): void
    {
        $this->assertSame(3, User::where('role', 'teacher')->count());
        $this->assertSame(0, User::whereNull('role')->orWhere('role', '')->count());
    }

    public function test_advisers_and_section_sizes(): void
    {
        $this->assertSame('liza@school.edu.ph', Section::where('name', 'Sampaguita')->first()->adviser->email);
        $this->assertSame('ramon@school.edu.ph', Section::where('name', 'Narra')->first()->adviser->email);
        Section::withCount(['students', 'subjects'])->get()->each(function (Section $section) {
            $this->assertSame(6, $section->students_count);
            $this->assertSame(4, $section->subjects_count);
        });
    }

    public function test_quarter_1_marks_are_between_0_and_100(): void
    {
        $this->assertSame(47, Mark::where('quarter', 1)->count()); // 12 students x 4 subjects, minus Bea's missing FIL7
        $this->assertSame(0, Mark::where('score', '<', 0)->orWhere('score', '>', 100)->count());
    }

    public function test_leave_requests_cover_the_demo_cases(): void
    {
        $this->assertSame(1, LeaveRequest::where('status', 'pending')->whereRelation('user', 'role', 'student')->count());
        $this->assertSame(1, LeaveRequest::where('status', 'pending')->whereRelation('user', 'role', 'teacher')->count());
        $this->assertSame(1, LeaveRequest::where('status', 'rejected')->count());

        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->with('user')->get();
        $this->assertSame(['maricel@school.edu.ph'], $onLeaveToday->pluck('user.email')->all());
    }
}
