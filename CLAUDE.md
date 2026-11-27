# CLAUDE.md

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.3) using MariaDB (port 3309)
- **Frontend:** Blade.php with plain CSS (`public/css/school.css`). No Tailwind, no npm, no Vite, no CDN links.
- **Design standard:** Front-end styling follows `.claude/agents/frontend-designer.md` (colours, buttons, forms, tables, badges). **Always** use `frontend-designer.md` for any front-end work (Blade markup and CSS), so the whole site has one standard design. Don't invent new styles or HTML structures that it already covers.

## Environment

- Windows 11, PowerShell, VS Code. Database `report_cards`, user `laravel`, MariaDB on 127.0.0.1 port 3309.
- Use the `mariadb` client, never `mysql` (on this PC `mysql` points at a different server).
- Run the app with `php artisan serve --port=8005` (http://localhost:8005). Tests: `php artisan test`.

## Working Instructions

1. **Discuss before executing.** Always discuss the approach with the user first, and get their agreement before running or writing any code.
2. **Ask when unsure.** Whenever you're unsure of something while coding, stop and ask the user for clarification. Do not guess or assume.
3. **Ask for missing context.** If you lack context, tell the user and ask specifically what you're missing so they can provide it.
4. **Pull first.** Before any other git action (branch, merge, commit, push, PR), run `git pull` (or `git fetch`) and check what changed on the remote. Tell the user about changes that touch your files or your plan before going on.

## Architecture (the restaurant)

- Web routes only in `routes/web.php`; API routes only in `routes/api.php`.
- Controllers (`app/Http/Controllers`) stay thin: get the models, call the service, return a view. No calculations.
- School rules live only in `app/Services/ReportCardService.php`.
- Models (`app/Models`) hold relationships and small helpers such as `fullName()`. No grading rules.
- Views (`resources/views`) only display data. No queries, no calculations. Every page extends `layouts/app.blade.php`.
- Change the database only with migrations. Never edit a migration that has already run; tell me instead.
- Load relationships with `with()` or `load()` so a page never runs one query per row.

## Seats and file ownership

One owner per file. Edit only your own seat's files. If you need a change in someone else's file, ask the user.

| Seat | Branch | Owns these files |
|---|---|---|
| 1 Schema (Sir Homer) | `feat/schema` | `database/`, `app/Models/` |
| 2 Owner, login and layout (Reggie) | `feat/auth` | `routes/web.php`, `bootstrap/app.php`, `layouts/app.blade.php`, `Auth/`, `CLAUDE.md` |
| 3 Students (Sir JP) | `feat/students` | `StudentController`, `StudentService`, its Form Requests, `views/students/` |
| 4 Subjects and marks (Sir Iam) | `feat/marks` | `SubjectController`, `MarkController`, `MarkPolicy`, `views/subjects/`, `views/marks/` |
| 5 Student portal (Sir Franklin) | `feat/portal` | `PortalController`, `views/portal/` |
| 6 Quality and principal (Sir Homer) | `feat/tests` | `tests/`, `.github/`, `PrincipalController`, `views/principal/` |

- `main` is protected: every change goes through a pull request with 1 approval and a passing `tests` check. Never merge your own PR.

## Database (decided in MyTask-01, details in `docs/MyTask-01.md` and README "Tables")

- MariaDB only. `users.role`, `attendances.status` and `leave_requests.status` are MariaDB ENUM columns. Their allowed values are repeated as `User::ROLES`, `Attendance::STATUSES` and `LeaveRequest::STATUSES`; use them in Form Requests (`Rule::in(...)`).
- `users.role` has no default and is never in `$fillable`. Columns the app sets itself are not fillable either: `students.user_id`, `leave_requests.user_id`/`status`/`decided_*`, `attendances.recorded_by`, `notes.author_id`. Set them in code.
- Tests run on MariaDB, in database `report_cards_test` (see `phpunit.xml`), never `report_cards`. Pest is not installed yet; the existing tests are PHPUnit classes.
- Each person sets their own `DB_PORT` in `.env`: 3309 on the lab PCs, 3306 for a MariaDB inside WSL.
- Tables: users (role), sections (adviser_id), students (user_id, student_no, section_id), subjects (code, section_id, teacher_id), marks, attendances, leave_requests, notes.
- The migrations `2026_11_27_000001` to `000008` have run. Never edit them; add a new migration instead.

## Who may do what

| Action | Admin | Principal | Teacher | Student |
|---|---|---|---|---|
| Create teacher/principal accounts, set up sections | yes | no | no | no |
| Add or edit a student | yes | no | yes | no |
| Add or edit a subject | yes | no | yes | no |
| Enter or change marks | yes | no | own subjects | no |
| Record attendance | yes | no | own section | no |
| Write a note on a student | yes | no | yes | no |
| See subjects, marks, attendance, notes | all | all | own section and subjects | own only |
| Apply for leave | no | no | yes | yes |
| Decide on a student's leave | yes | yes | own advisory section | no |
| Decide on a teacher's leave | yes | yes | no | no |
| See all teachers, all students, who is on leave today | yes | yes | no | no |

- "Own subjects" means `subjects.teacher_id` is the user. "Own section" and "own advisory section" mean `sections.adviser_id` is the user.
- Nobody decides on their own leave. A request is decided once: only `pending` can change. `end_date` is not before `start_date`.
- Students get `/my/...` pages that read the logged-in student and take no id from the URL. Any page that does take a student id returns 403 for another student.
- A teacher can't save a mark for a subject they don't teach, even with an edited hidden `subject_id`. Check it in the Policy, not just the form.
- The principal can NOT change marks.
- Write a test for every "own" cell.

## School rules

- Marks are 0 to 100 (`marks.score` is decimal(5,2)). One mark per student, subject and quarter (1 to 4).
- General average: plain average of every grade that is not NULL, rounded to 2 decimals.
- Any NULL grade: remark INC. No grades at all: NO GRADES. Average 75 or above: PASSED, else FAILED.

## How to work with me

- I am learning Laravel. For every build request, show a short plan first and wait for my OK. Work in small steps.
- After each change, explain each new or changed file in 2 or 3 plain sentences, and tell me one command or URL to check it works.
- Ask before installing anything (`composer require`, npm, Laravel Boost) and before running `php artisan migrate:fresh`.
- Do not edit `.env` or anything in `vendor/` unless I ask you to. Whenever you do edit `.env`, report exactly which lines you changed (old value and new value).
- Never run anything that needs administrator rights or the MariaDB root password. Give me the command and I run it myself.

## API rules (Day 4)
- API routes live only in `routes/api.php`. Everything except `POST /api/login` goes inside the `auth:sanctum` group.
- API controllers live in `app/Http/Controllers/Api`. They call the same services as the web controllers: no second copy of the school rules.
- Shape every JSON response with an API Resource or an explicit list of fields. Never return a whole model.
- Who may see or change something is decided in a Policy (`app/Policies`). Controllers call `Gate::authorize()`.
- Validate input with a Form Request (`app/Http/Requests`). Save only `$request->validated()`, never `$request->all()`.
- Every model lists the columns a request may change in `$fillable`. Never use `$guarded = []`.
- Never build SQL by gluing user input into a string. Use Eloquent or `?` placeholders.
- Status codes: 200 read or changed, 201 created, 204 nothing to send back, 401 no or bad token, 403 not allowed, 404 not found, 422 invalid input.
- Write or update a Pest test for every endpoint and every rule about who may do what. Run `php artisan test` before you say you are done.
