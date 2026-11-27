# Student Management Portal

A school portal built with Laravel 13 for the PSHS full-stack capstone. Admins, the principal, teachers and students log in, and each sees something different.

- **Backend:** Laravel 13, PHP 8.3, MariaDB
- **Frontend:** Blade with plain CSS (`public/css/school.css`). No npm, Vite or CDN.

---

## Setup

```bash
git clone <repo-url> laravel-capstone-2
cd laravel-capstone-2
composer install
cp .env.example .env            # PowerShell: Copy-Item .env.example .env
php artisan key:generate
```

Edit `.env`:

- `DB_PASSWORD`: your `laravel` MariaDB user's password.
- `DB_PORT`: the port **your** MariaDB listens on. The lab PCs use `3309`, and a MariaDB inside WSL usually uses `3306`.

Then run:

```bash
php artisan migrate:fresh --seed   # builds and fills the database (every demo starts here)
php artisan serve --port=8005      # http://localhost:8005
```

### Tests

Tests run on **MariaDB**, in a separate database called `report_cards_test` (set in `phpunit.xml`). Create it once as the MariaDB root user:

```sql
CREATE DATABASE report_cards_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON report_cards_test.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
```

Then run `php artisan test`. Tests wipe `report_cards_test` every time, never `report_cards`.

---

## Seeded logins

Every password is `password`.

| Login | Who |
|---|---|
| admin@school.edu.ph | Admin |
| principal@school.edu.ph | The principal (Dr. Rosario Mendoza) |
| liza@school.edu.ph | Teacher, adviser of Grade 7 Sampaguita, teaches Math (both sections) |
| ramon@school.edu.ph | Teacher, adviser of Grade 7 Narra, teaches Science (both sections) |
| maricel@school.edu.ph | Teacher (not an adviser), teaches English and Filipino (both sections) |
| juan@school.edu.ph | Student in Sampaguita |

There are 11 more students, 6 per section. Their logins are their first name @school.edu.ph, e.g. `miguel@school.edu.ph`, `bea@school.edu.ph`; one exception is `markanthony@school.edu.ph`.

**What the demo data shows**
- **Marks:** quarter 1. Paolo's average is below 75 (FAILED). Bea has no Filipino mark (INC).
- **Attendance:** today, with some late, absent and excused.
- **Leave:** one pending from a student (Juan), one pending from a teacher (Ramon), one approved that covers today (Maricel), and one rejected (Miguel).

---

## Tables

| Table | Columns | Notes |
|---|---|---|
| users | id, name, email, password, **role** | `role` is a MariaDB ENUM: `student`, `teacher`, `principal`, `admin`. No default. Not fillable. |
| sections | id, name, grade_level, adviser_id → users | e.g. Grade 7 Sampaguita. The adviser decides on leave for this section's students. `adviser_id` is nullable. |
| students | id, user_id → users (unique), student_no (unique), section_id → sections | School data only. Name and email stay on `users`. |
| subjects | id, code, name, section_id → sections, teacher_id → users | One row per subject per section. Unique on (code, section_id). |
| marks | id, student_id, subject_id, quarter (1–4), score decimal(5,2) | Unique on (student_id, subject_id, quarter). Scores are 0 to 100. |
| attendances | id, student_id, date, status, recorded_by → users | `status` ENUM: present, absent, late, excused. Unique on (student_id, date). |
| leave_requests | id, user_id → users, start_date, end_date, reason, status, decided_by → users, decided_at, decision_note | `status` ENUM: pending, approved, rejected; default pending. The `decided_*` columns stay empty until someone decides. |
| notes | id, student_id → students, author_id → users, body | A teacher's note on a student. |

### Relationships

| Model | Relationships |
|---|---|
| User | `student()` hasOne, `subjectsTaught()` hasMany Subject on `teacher_id`, `advisedSection()` hasOne Section on `adviser_id`, `leaveRequests()` hasMany |
| Section | `adviser()` belongsTo User, `students()` hasMany, `subjects()` hasMany |
| Student | `user()`, `section()` belongsTo; `marks()`, `attendances()`, `notes()` hasMany |
| Subject | `section()` belongsTo, `teacher()` belongsTo User on `teacher_id`, `marks()` hasMany |
| Mark | `student()`, `subject()` belongsTo |
| Attendance | `student()` belongsTo, `recorder()` belongsTo User on `recorded_by` |
| LeaveRequest | `user()` belongsTo (the requester), `decider()` belongsTo User on `decided_by` |
| Note | `student()` belongsTo, `author()` belongsTo User on `author_id` |

### Design choices (and where we changed the starting design)

- **MariaDB only, with ENUM columns.** The brief suggested plain strings so tests could run on SQLite. Our team runs the lab PCs, the tests and the planned AWS database (Amazon RDS for MariaDB) all on MariaDB, so we used ENUM columns. MariaDB then rejects an invalid role or status itself, and Form Requests still validate the values so users get a clear 422 error.
- **No default role.** Every user must be given a role on purpose.
- **A `role` column, not a roles table.** Every user here has exactly one role, and `$user->role === 'teacher'` is easy to read in a Policy. A roles table would pay off only if roles overlapped.
- **One leave table for students and teachers.** Both are rows in `users`, so `leave_requests.user_id` covers both. The requester's role decides who may approve. "Who is on leave today" is one query on one table.
- **`role` and every column the app sets itself are not fillable.** This covers `user_id`, `status`, `decided_*`, `recorded_by` and `author_id`. No form can make someone an admin, approve their own leave, or fake who recorded something.

---

## Who may do what

| Action | Admin | Principal | Teacher | Student |
|---|---|---|---|---|
| Create teacher and principal accounts, set up sections | yes | no | no | no |
| Add or edit a student | yes | no | yes | no |
| Add or edit a subject | yes | no | yes | no |
| Enter or change marks | yes | no | **own subjects** | no |
| Record attendance | yes | no | **own section** | no |
| Write a note on a student | yes | no | yes | no |
| See subjects, marks, attendance, notes | all | all | **own section and subjects** | **own only** |
| Apply for leave | no | no | yes | yes |
| Decide on a student's leave | yes | yes | **own advisory section** | no |
| Decide on a teacher's leave | yes | yes | no | no |
| See all teachers, all students, who is on leave today | yes | yes | no | no |

### The rules that break

- Nobody decides on their own leave, whatever their role.
- A leave request is decided once. Approving an already rejected request is refused.
- The end date can't be before the start date.
- A student who changes `/students/4/marks` to `/students/5/marks` gets a 403. Better still, give students pages like `/my/marks` that read the logged-in student and take no id from the URL.
- A teacher can't enter a mark for a subject someone else teaches, even by editing the form's hidden `subject_id` in the browser's developer tools.
- Marks are between 0 and 100. The same student, subject and quarter can have only one mark.

### Should the principal be able to change marks?

**No.** The principal sees everything and decides on leave, but marks belong to the teacher who gave them. The marks Policy must refuse the principal, and a test must prove it.
