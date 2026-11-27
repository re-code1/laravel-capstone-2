# MyTask-01: Database schema, models and seed data

**Branch:** `feat/schema`
**Goal:** Build the tables, models, factories and seed data that the rest of the team builds on, and document the "Tables" and "Who may do what" sections.

This file records every change, command and decision made for this task, in order.

---

## Progress

| Step | What | Status |
|---|---|---|
| 1 | Migrations (8 files) | Done |
| 2 | Models with relationships and `$fillable` | Done |
| 3 | Factories (+ `FactoriesTest`) | Done |
| 4 | DatabaseSeeder (+ `DatabaseSeederTest`) | Done |
| 5 | README.md and CLAUDE.md sections | Done |
| 6 | Verify: `migrate:fresh --seed`, tinker checks, `php artisan test` | Done |

---

## Decisions

| # | Decision | Why |
|---|---|---|
| D1 | **MariaDB only.** We do not keep the schema SQLite-compatible. | The team targets MariaDB on the lab PCs and later in AWS. Our instructor confirmed that MySQL/MariaDB is fine as long as the app works. |
| D2 | `role` and every `status` are **ENUM** columns, not plain strings. | Because of D1, the database itself rejects invalid values. Form Requests will still validate them, so users get a friendly 422 instead of a database error. |
| D3 | `users.role` has **no default**. | Every user must be given a role on purpose. The ENUM lists `student` first, so if MariaDB ever has to fill in a value for old rows, it picks the role with the least access. |
| D4 | `role` is **not** in `$fillable`. | No form can make someone an admin. The seeder and factories set it explicitly. |
| D5 | One `leave_requests` table for students and teachers. | Both are rows in `users`, so `user_id` covers both. Their `role` decides who may approve: a student's request goes to the section adviser or the principal, a teacher's to the principal only. |
| D6 | One `role` column instead of a roles table. | Every user has exactly one role, and `$user->role === 'teacher'` is easy to read in a Policy. |
| D7 | Notes table included (it was marked stretch). | It is small, and it gives the "write a note on a student" feature a table to use. |
| D8 | Marks are **0 to 100** (CLAUDE.md said 60 to 100). | Follows the capstone brief. CLAUDE.md will be updated in step 5. |
| D9 | The principal **cannot** change marks. | Marks belong to the teacher who gave them. The principal sees everything and decides on leave. |
| D10 | Tests run on a separate MariaDB database, `report_cards_test`. | Because of D1, tests run on the same engine as the app. Tests wipe their database, so it must never be `report_cards`. |
| D11 | Migration files are named by hand: `2026_11_27_000001` to `000008`. | Laravel runs migrations in file-name order. Files made by `make:migration` in the same second share a timestamp and then sort alphabetically, which would run `attendances` before `students`. |

> **AWS note:** Amazon Aurora has no MariaDB edition; it comes only as MySQL- or PostgreSQL-compatible. For MariaDB in AWS, use **Amazon RDS for MariaDB**. If the team picks Aurora MySQL, ENUM columns and Laravel migrations still work.

---

## Step 1: Migrations

**Files created** (all in `database/migrations/`):

| File | Table | Key points |
|---|---|---|
| `2026_11_27_000001_add_role_to_users_table.php` | users | Adds `role` ENUM(student, teacher, principal, admin), indexed, no default. The original users migration is untouched. |
| `2026_11_27_000002_create_sections_table.php` | sections | name, grade_level, `adviser_id` → users (nullable; cleared if the adviser is deleted) |
| `2026_11_27_000003_create_students_table.php` | students | `user_id` → users (unique), `student_no` (unique), `section_id` → sections |
| `2026_11_27_000004_create_subjects_table.php` | subjects | code, name, `section_id`, `teacher_id` → users. Unique on (code, section_id). |
| `2026_11_27_000005_create_marks_table.php` | marks | quarter (1–4), score decimal(5,2). Unique on (student_id, subject_id, quarter). |
| `2026_11_27_000006_create_attendances_table.php` | attendances | date, status ENUM(present, absent, late, excused), `recorded_by` → users. Unique on (student_id, date). |
| `2026_11_27_000007_create_leave_requests_table.php` | leave_requests | start/end date, reason, status ENUM(pending, approved, rejected) default pending, nullable `decided_by`, `decided_at` and `decision_note` |
| `2026_11_27_000008_create_notes_table.php` | notes | `student_id`, `author_id` → users, body |

**Order matters:** a table can only point at a table that already exists, so the order is users → sections → students → subjects → marks → attendances → leave_requests → notes.

**Commands run**

| Command | Result |
|---|---|
| `php -l` on each new migration | No syntax errors |
| `php artisan migrate --pretend` (no `.env` yet) | Warning: "SQLite database ... does not exist". Laravel falls back to SQLite when there is no `.env`. Answer **no**. |
| `php artisan migrate --pretend` (port 3309) | `Connection refused`. Nothing listens on 3309 on this PC. |
| `php artisan migrate --pretend` (port 3306) | Success. It printed the SQL for all 8 migrations, including `enum(...)` columns, the foreign keys and the unique keys, without changing the database. |

The first version of these files used plain `string` columns and a `role` default of `student`, which is what the trainer's brief recommends for SQLite. They were changed to ENUM with no role default after decisions D1 to D3. None of the migrations had been run yet, so editing them was safe.

### Local environment setup (`.env`)

`.env` is git-ignored, so a fresh clone does not have one. Without it, Laravel uses SQLite. Steps:

1. `cp .env.example .env` (PowerShell: `Copy-Item .env.example .env`)
2. `php artisan key:generate`
3. Set `DB_PASSWORD` to your `laravel` user's password.
4. Set `DB_PORT` to the port **your** MariaDB uses. Check it with `ss -ltn | grep 33` (Linux/WSL) or in your MariaDB config.

| Machine | MariaDB port |
|---|---|
| Lab PCs (Windows MariaDB, `.env.example` default) | 3309 |
| My dev machine (MariaDB inside WSL) | 3306 |

On this machine, `report_cards` and `report_cards_test` already existed, and the `laravel` user could log into both.

### Test database setup (D10)

**Files changed:** `phpunit.xml`. `DB_CONNECTION` changed from `sqlite` to `mariadb`, and `DB_DATABASE` from `:memory:` to `report_cards_test`. Host, port, user and password still come from `.env`.

**One-time setup (run yourself; it needs the MariaDB root password):**

```sql
CREATE DATABASE report_cards_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON report_cards_test.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
```

Every teammate needs to run these SQL commands once on their own PC. If GitHub Actions is added later, its workflow needs a MariaDB service. Right now the repo has no `.github/workflows` folder.

---

## Step 2: Models

**Files:** `app/Models/User.php` (updated). New: `Section.php`, `Student.php`, `Subject.php`, `Mark.php`, `Attendance.php`, `LeaveRequest.php`, `Note.php`.

These models use Laravel 13's `#[Fillable([...])]` attribute, the same style as the starter `User` model. `#[Fillable]` does the same job as a `$fillable` property.

| Model | Relationships | Fillable (what a form may change) | Extras |
|---|---|---|---|
| User | `student()` hasOne, `subjectsTaught()` hasMany Subject on `teacher_id`, `advisedSection()` hasOne Section on `adviser_id`, `leaveRequests()` hasMany | name, email, password (**not role**) | `ROLES` constant, `hasRole()` |
| Section | `adviser()` belongsTo User, `students()`, `subjects()` hasMany | name, grade_level, adviser_id | `fullName()` → "Grade 7 Sampaguita" |
| Student | `user()`, `section()` belongsTo; `marks()`, `attendances()`, `notes()` hasMany | student_no, section_id (not user_id) | |
| Subject | `section()`, `teacher()` belongsTo User on `teacher_id`; `marks()` hasMany | code, name, section_id, teacher_id | |
| Mark | `student()`, `subject()` belongsTo | student_id, subject_id, quarter, score | `score` cast to `decimal:2` |
| Attendance | `student()`, `recorder()` belongsTo User on `recorded_by` | student_id, date, status (not recorded_by) | `STATUSES` constant |
| LeaveRequest | `user()` (the requester), `decider()` belongsTo User on `decided_by` | start_date, end_date, reason (not user_id, status or decided_*) | `STATUSES` constant, `isPending()` |
| Note | `student()`, `author()` belongsTo User on `author_id` | student_id, body (not author_id) | |

**Rule used:** a column that the app sets from the logged-in user, or that a decision sets, is never fillable. For example, a student cannot send `status=approved` in a leave form, and a teacher cannot fake `recorded_by`.

The `ROLES` and `STATUSES` constants repeat the ENUM lists from the migrations, so Form Requests can use `Rule::in(User::ROLES)`.

`Attendance::recorder()`, `Note::student()` and `Note::author()` are extra relationships; they are not in the brief's relationship table.

**Commands run**

| Command | Result |
|---|---|
| `php -l` on every model | No syntax errors |
| `php artisan tinker`: build each relationship and print its target model and key | All 23 relationships point to the right model and column. `role` is not fillable. |

---

## Step 3: Factories

Tests use factories to build fake rows quickly. A factory also creates the parents a row needs: `Mark::factory()->create()` also creates a student (with a login and a section) and a subject (with a teacher).

**Files:** `database/factories/UserFactory.php` (updated). New: `SectionFactory`, `StudentFactory`, `SubjectFactory`, `MarkFactory`, `AttendanceFactory`, `LeaveRequestFactory`, `NoteFactory`.

| Factory | Default | States |
|---|---|---|
| User | role `student` | `->admin()`, `->principal()`, `->teacher()`, `->student()` |
| Section | Grade 7, random Filipino flower or tree name, a new teacher as adviser | `->withoutAdviser()` |
| Student | new student login, `2026-####`, new section | |
| Subject | random unique code like `QWER7`, new section, new teacher | |
| Mark | new student, new subject, quarter 1, score 70–98 | |
| Attendance | new student, today, `present`, recorded by a new teacher | |
| LeaveRequest | student login, tomorrow to the day after, `pending` | `->approved()`, `->rejected()` (decided by a new principal) |
| Note | new student, written by a new teacher | |

**Watch out:** by default a Mark's student and subject are in different sections. When a test cares, pass both: `Mark::factory()->for($student)->for($subject)->create()`.

**Fix made while writing:** Section names first used `fake()->unique()` on a list of 10 names, which would crash a test that makes 11 sections. Section names do not need to be unique, so `unique()` was removed.

### Test: `tests/Feature/FactoriesTest.php`

Written in PHPUnit style like `ExampleTest`, because **Pest is not installed** in this project (only its plugin is allowed in `composer.json`). CLAUDE.md asks for Pest tests, so installing it is a team decision. `composer require pestphp/pest --dev` needs approval first.

| Test | Proves |
|---|---|
| user factory sets a role | Every factory user has a role, and each state gives the right one |
| every factory creates a row with its parents | All factories work, and the parents get the right roles |
| leave request states | Pending has no decider; approved is decided by a principal |
| role is not mass assignable | `$user->update(['role' => 'admin'])` does **not** change the role |
| database rejects a role outside the enum | MariaDB refuses `role = 'superuser'` |
| one mark per student, subject and quarter | A second mark for the same student, subject and quarter is refused |

**Commands run**

| Command | Result |
|---|---|
| `php -l` on every factory | No syntax errors |
| `SHOW TABLES` on `report_cards_test` | Empty, so tests can safely use it |
| `php artisan test` | **8 passed (19 assertions)** on MariaDB `report_cards_test` |

---

## Step 4: DatabaseSeeder

**File:** `database/seeders/DatabaseSeeder.php`, which replaces the default "Test User" seeder.

### Logins (every password is `password`)

| Login | Who |
|---|---|
| admin@school.edu.ph | School Admin |
| principal@school.edu.ph | Dr. Rosario Mendoza, principal |
| liza@school.edu.ph | Liza Reyes. Teacher, adviser of Grade 7 Sampaguita. Teaches Math in **both** sections. |
| ramon@school.edu.ph | Ramon Cruz. Teacher, adviser of Grade 7 Narra. Teaches Science in both sections. |
| maricel@school.edu.ph | Maricel Santos. Third teacher, not an adviser. Teaches English and Filipino in both sections. |
| juan@school.edu.ph | Juan dela Cruz, student in Sampaguita |

Other students (login = first name @school.edu.ph):

| Section | Students |
|---|---|
| Sampaguita | Juan dela Cruz, Angelica Bautista, Mark Anthony Villanueva (`markanthony@`), Kristine Mendiola, Paolo Garcia, Jasmine Aquino |
| Narra | Miguel Torres, Andrea Ramos, Carlo Fernandez, Bea Castillo, Joshua Navarro, Patricia Domingo |

Student numbers are `2026-0001` to `2026-0012`.

### Data

| What | Details |
|---|---|
| Subjects | Each section has MATH7 (Liza), SCI7 (Ramon), ENG7 (Maricel) and FIL7 (Maricel). |
| Marks | Quarter 1, every student × every subject in their section: **47 marks**. Scores are 76–98. **Paolo** gets 65–74, so his average is below 75 (FAILED). **Bea** has no FIL7 mark, so her report card can show INC. `mt_srand(2026)` makes the scores the same on every run. |
| Attendance | Today, for all 12 students, recorded by each section's adviser. Paolo is late, Carlo absent, Andrea excused; everyone else is present. |
| Leave 1 | Juan (student): **pending**, 3–4 days from today |
| Leave 2 | Ramon (teacher): **pending**, 7–8 days from today. Only the principal may decide. |
| Leave 3 | Maricel (teacher): **approved** by the principal, from yesterday to tomorrow. This is what "on leave today" shows. |
| Leave 4 | Miguel (student): **rejected** by his adviser Ramon. Used to test "decided once". |
| Notes | 2 notes, written by Liza: on Juan and on Paolo |

All dates are relative to `today()`, so the demo works on any day.

### How the seeder sets non-fillable columns

The seeder respects `$fillable` just as the real app will. Columns that no form may set are set on purpose in code:

- `role`: `$user->role = 'teacher'` in `makeUser()`
- `students.user_id`: `$user->student()->create([...])`, where the relationship fills it in
- `leave_requests.user_id`: `$user->leaveRequests()->create([...])`
- `status`, `decided_by`, `decided_at`, `decision_note`: `forceFill()` in `decide()`
- `recorded_by` and `author_id`: `forceFill()`

### Test: `tests/Feature/DatabaseSeederTest.php`

This test runs the seeder on `report_cards_test` and checks the brief's "Check the schema" list automatically:

| Test | Checks |
|---|---|
| five required logins | All five exist with the right role, and the password is `password` |
| Juan's subjects | `juan → student → section → subjects` = Mathematics, Science, English, Filipino |
| three teachers, no empty role | `role = teacher` count is 3; users with a NULL or empty role: 0 |
| advisers and section sizes | Liza advises Sampaguita and Ramon advises Narra; each section has 6 students and 4 subjects |
| quarter 1 marks | 47 marks, all between 0 and 100 |
| leave requests | 1 pending from a student, 1 pending from a teacher, 1 rejected; only Maricel is on leave today |

**Commands run**

| Command | Result |
|---|---|
| `php -l database/seeders/DatabaseSeeder.php` | No syntax errors |
| `php artisan test` | **14 passed (49 assertions)** |

---

## Step 5: README.md and CLAUDE.md

**README.md:** the default Laravel text was replaced with:
- setup steps, including `.env`, the DB port and the test database;
- the seeded logins and what the demo data shows;
- **Tables**, with relationships and our design choices, including why we changed the brief's string columns to ENUM;
- **Who may do what**, with "The rules that break";
- the principal-marks decision: **no**.

**CLAUDE.md:**
- Changed "Grades are 60 to 100" to "Marks are 0 to 100", with the one-mark-per-quarter rule (D8).
- Added a **Database** section: MariaDB/ENUM, the constants to use in Form Requests, non-fillable columns, tests on `report_cards_test`, Pest not installed, the DB port per machine, and "never edit the 8 migrations".
- Added the **Who may do what** table, plus the rules in short form, so AI assistants and teammates follow them.

---

## Step 6: Final verification

The user ran `php artisan migrate:fresh --seed` on `report_cards` with no errors.

| Check (from the capstone brief) | Result |
|---|---|
| `php artisan migrate:status` | All 11 migrations ran (3 Laravel defaults + our 8) |
| `User::where('email','juan@school.edu.ph')->first()->student->section->subjects->pluck('name')` | Mathematics, Science, English, Filipino |
| `User::where('role','teacher')->count()` | 3 |
| Users with an empty or NULL role | 0 |
| Row counts | users 17, sections 2, students 12, subjects 8, marks 47, attendances 12, leave_requests 4, notes 2 |
| Approved leave covering today | Maricel Santos |
| `php artisan test` | 14 passed (49 assertions) |

> **From now on, the 8 migrations must not be edited**, because they have run. Any schema change is a new migration.

---

## How to check

```bash
php artisan migrate --pretend          # shows the SQL, changes nothing
php artisan migrate:fresh --seed       # rebuilds the database (after step 4)
php artisan test                       # runs on report_cards_test
```
