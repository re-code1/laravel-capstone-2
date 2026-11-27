<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * A believable school for the demo. Run with: php artisan migrate:fresh --seed
 * Every password is "password".
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Same "random" scores on every run, so the demo is predictable.
        mt_srand(2026);

        // ---- Staff -------------------------------------------------------------
        $admin     = $this->makeUser('School Admin', 'admin@school.edu.ph', 'admin');
        $principal = $this->makeUser('Dr. Rosario Mendoza', 'principal@school.edu.ph', 'principal');
        $liza      = $this->makeUser('Liza Reyes', 'liza@school.edu.ph', 'teacher');
        $ramon     = $this->makeUser('Ramon Cruz', 'ramon@school.edu.ph', 'teacher');
        $maricel   = $this->makeUser('Maricel Santos', 'maricel@school.edu.ph', 'teacher');

        // ---- Sections (adviser decides on leave for the section's students) ---
        $sampaguita = Section::create(['name' => 'Sampaguita', 'grade_level' => 7, 'adviser_id' => $liza->id]);
        $narra      = Section::create(['name' => 'Narra', 'grade_level' => 7, 'adviser_id' => $ramon->id]);

        // ---- Subjects: 4 per section --------------------------------------------
        // Liza also teaches Math in Narra, where she is NOT the adviser:
        // a real case for "own subjects" vs "own section".
        foreach ([$sampaguita, $narra] as $section) {
            Subject::create(['code' => 'MATH7', 'name' => 'Mathematics', 'section_id' => $section->id, 'teacher_id' => $liza->id]);
            Subject::create(['code' => 'SCI7',  'name' => 'Science',     'section_id' => $section->id, 'teacher_id' => $ramon->id]);
            Subject::create(['code' => 'ENG7',  'name' => 'English',     'section_id' => $section->id, 'teacher_id' => $maricel->id]);
            Subject::create(['code' => 'FIL7',  'name' => 'Filipino',    'section_id' => $section->id, 'teacher_id' => $maricel->id]);
        }

        // ---- Students: 6 per section --------------------------------------------
        $studentNo = 0;
        $students = [];
        foreach ([
            [$sampaguita, 'Juan dela Cruz', 'juan'],
            [$sampaguita, 'Angelica Bautista', 'angelica'],
            [$sampaguita, 'Mark Anthony Villanueva', 'markanthony'],
            [$sampaguita, 'Kristine Mendiola', 'kristine'],
            [$sampaguita, 'Paolo Garcia', 'paolo'],
            [$sampaguita, 'Jasmine Aquino', 'jasmine'],
            [$narra, 'Miguel Torres', 'miguel'],
            [$narra, 'Andrea Ramos', 'andrea'],
            [$narra, 'Carlo Fernandez', 'carlo'],
            [$narra, 'Bea Castillo', 'bea'],
            [$narra, 'Joshua Navarro', 'joshua'],
            [$narra, 'Patricia Domingo', 'patricia'],
        ] as [$section, $name, $login]) {
            $user = $this->makeUser($name, "{$login}@school.edu.ph", 'student');
            // student()->create() fills user_id for us (user_id is not fillable).
            $students[$login] = $user->student()->create([
                'student_no' => sprintf('2026-%04d', ++$studentNo),
                'section_id' => $section->id,
            ]);
        }

        // ---- Marks: quarter 1, every student x every subject of their section ----
        foreach ($students as $login => $student) {
            foreach ($student->section->subjects as $subject) {
                // Bea has no Filipino mark yet, so the report card can show INC.
                if ($login === 'bea' && $subject->code === 'FIL7') {
                    continue;
                }
                // Paolo is struggling (average below 75, so FAILED); everyone else 76 to 98.
                $score = $login === 'paolo' ? mt_rand(6500, 7400) / 100 : mt_rand(7600, 9800) / 100;

                $student->marks()->create(['subject_id' => $subject->id, 'quarter' => 1, 'score' => $score]);
            }
        }

        // ---- Attendance for today, recorded by each section's adviser ------------
        $todayStatus = ['paolo' => 'late', 'carlo' => 'absent', 'andrea' => 'excused'];
        foreach ($students as $login => $student) {
            $student->attendances()->make([
                'date' => today(),
                'status' => $todayStatus[$login] ?? 'present',
            ])->forceFill(['recorded_by' => $student->section->adviser_id])->save();
        }

        // ---- Leave requests -------------------------------------------------------
        // 1. Pending, from a student (Juan). His adviser Liza or the principal may decide.
        $students['juan']->user->leaveRequests()->create([
            'start_date' => today()->addDays(3),
            'end_date' => today()->addDays(4),
            'reason' => 'Family reunion in Batangas.',
        ]);

        // 2. Pending, from a teacher (Ramon). Only the principal may decide.
        $ramon->leaveRequests()->create([
            'start_date' => today()->addDays(7),
            'end_date' => today()->addDays(8),
            'reason' => 'DepEd regional science teachers seminar.',
        ]);

        // 3. Approved by the principal, covering TODAY, so "on leave today" shows Maricel.
        $this->decide($maricel->leaveRequests()->create([
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
            'reason' => 'Fever, advised by the doctor to rest for three days.',
        ]), 'approved', $principal, 'Get well soon.');

        // 4. Rejected by the adviser (Ramon), so "a request is decided once" can be tested.
        $this->decide($students['miguel']->user->leaveRequests()->create([
            'start_date' => today()->addDays(2),
            'end_date' => today()->addDays(2),
            'reason' => 'Attend a basketball tryout.',
        ]), 'rejected', $ramon, 'Tryout is after class hours; please attend class.');

        // ---- Notes (written by Liza) ----------------------------------------------
        $this->addNote($students['juan'], $liza, 'Very active in class recitation. Encourage him to join the Math quiz bee.');
        $this->addNote($students['paolo'], $liza, 'Struggling in Math this quarter. Parent conference scheduled.');

        $this->command?->info('Seeded: 5 staff, 2 sections, 8 subjects, 12 students. Every password is "password".');
    }

    /** role is not fillable, so it is set on purpose here, never from a form. */
    private function makeUser(string $name, string $email, string $role): User
    {
        $user = new User(['name' => $name, 'email' => $email, 'password' => 'password']); // hashed by the model cast
        $user->role = $role;
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    /** status and decided_* are not fillable: they are set only by a decision. */
    private function decide(LeaveRequest $leave, string $status, User $decider, string $note): void
    {
        $leave->forceFill([
            'status' => $status,
            'decided_by' => $decider->id,
            'decided_at' => now()->subDays(2),
            'decision_note' => $note,
        ])->save();
    }

    /** author_id is not fillable: it is the logged-in teacher in the real app. */
    private function addNote(Student $student, User $author, string $body): void
    {
        $student->notes()->make(['body' => $body])->forceFill(['author_id' => $author->id])->save();
    }
}
