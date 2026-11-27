# Seat 4: Subjects and Marks, session notes

Re-learning notes from my Claude Code sessions on 2026-11-27, branch `feat/marks`.
Updated after fitting the code to the real schema (commit `d295df0`).

---

## 1. The task

Our team split the capstone into 6 seats. Mine is **Seat 4: Subjects and marks**.

| What | Detail |
|---|---|
| Branch | `feat/marks` |
| Builds | Teacher adds subjects and enters marks for **their own** subjects |
| Owns | `SubjectController`, `MarkController`, `MarkPolicy`, `views/subjects/`, `views/marks/` |

Other seats I depend on:
- **Seat 1 (Schema):** tables and models. Merged.
- **Seat 2 (Auth):** login, the `role` middleware, `routes/web.php`. Merged (login and the `role` middleware).
- **Seat 3 (Students)** and **Seat 5 (Portal):** merged.
- **Seat 6 (Tests):** owns `tests/` and will add Pest.

---

## 2. Working method (important for re-learning)

1. Check the branch and what the team has merged (`git fetch`, `git log HEAD..origin/main`).
2. Build with **guessed** names and mark every guess with an `ASSUMPTION` comment, so the team can merge in any order.
3. After Seat 1 merged, read the real models and migrations and **fix every guess** (section 6).
4. Show a short plan, get the OK, then write code in small steps.

Git commands used:

```bash
git branch -a                      # list local and remote branches
git status -sb                     # current branch, ahead or behind
git fetch --all --prune            # download GitHub's state, change nothing locally
git log --oneline HEAD..origin/main   # what main has that I do not
git pull --ff-only origin main     # bring main in, only if no merge is needed
git switch feat/marks              # change branch (uncommitted changes come along if no conflict)
git push -u origin feat/marks      # first push links the branch; later just `git push`
```

Lesson: I once found myself on `main` with uncommitted work. **Always run `git branch --show-current` before committing.**

---

## 3. Setting up the Mac

### Packages
`composer install` rebuilds `vendor/` from `composer.lock`. It adds nothing new.

### `.env`
There was no `.env`, so the app had no key. Fix: `cp .env.example .env` then `php artisan key:generate`.
`.env` is git-ignored, so it is never pushed.

### Database (the surprising parts)
- This Mac runs MariaDB on **port 3306**. CLAUDE.md says **3309** (that is the Windows PC). I changed only my local `.env`: `DB_PORT=3309` to `3306`.
- `mariadb -u root -p` failed with **ERROR 1698**: Homebrew's `root` accepts only the Mac's administrator (unix socket login), so no password works. My own account works instead: just run `mariadb` with **no `-u root` and no `-p`**.
- I created the databases and user that way:
  `report_cards`, `report_cards_test`, and the user `laravel` (for `127.0.0.1` and `localhost`).
- The `laravel` password goes in `.env` line `DB_PASSWORD=`. If it is rejected (error 1045), reset it with `ALTER USER 'laravel'@'localhost' IDENTIFIED BY '...'` so both match.
- Then: `php artisan migrate` and `php artisan db:seed`.

Seeded data: 5 staff, 2 sections, 8 subjects, 12 students. **Every password is `password`.**
Teachers: `liza@school.edu.ph`, `ramon@school.edu.ph`, `maricel@school.edu.ph`.

### Pest could not be installed
This Mac has **PHP 8.6**. Pest needs paratest, which supports PHP up to 8.5. Forcing it with `--ignore-platform-req=php` would upgrade Symfony to version 8 (needs PHP 8.4+) and break teammates on PHP 8.3. So I wrote **plain PHPUnit tests** instead. Converting to Pest later is mostly renaming each test.

---

## 4. The real schema (what my code is built on)

| Table | Columns that matter |
|---|---|
| `users` | `name`, `email`, `role` (student, teacher, principal, admin) |
| `sections` | `name`, `grade_level`, `adviser_id`. Has `students()` and `subjects()`, and `fullName()` ("Grade 7 Sampaguita"). |
| `students` | `user_id` (name lives on the user), `student_no`, `section_id` |
| `subjects` | `code`, `name`, `section_id`, `teacher_id`. Unique on (`code`, `section_id`). |
| `marks` | `student_id`, `subject_id`, **`quarter` (1 to 4)**, **`score` decimal(5,2) NOT NULL**. Unique on (student, subject, quarter). |

Consequences:
- There is **no enrollment table**. The students of a subject are the students of **its section**.
- A score **cannot be NULL**. So "no mark yet" (INC) is **no row at all**.
- Score range is **0 to 100** (CLAUDE.md says so now).

---

## 5. What I built

### Subjects
| File | What it does |
|---|---|
| `SubjectController` | `index` lists my subjects (sections loaded in one query), `create` shows the form with sections, `store` saves. `teacher_id` comes from the login, never the form. |
| `StoreSubjectRequest` | Name, section (must exist) and code are required. The code must be unique **within that section**, so a duplicate gives a form error instead of a database crash. |
| `subjects/index`, `subjects/create` | Table with code, name, section and an "Enter marks" link. Form with a section dropdown. |

### Marks
| File | What it does |
|---|---|
| `MarkPolicy` | `enter(User, Subject)` is true only if `subject.teacher_id` is my id. Anyone else gets **403**. |
| `MarkService` | `roster()` loads the section's students with their user and only this quarter's mark (a fixed number of queries). `saveScores()` creates or updates a mark, **deletes it when the box is empty**, all inside one transaction. `studentIds()` lists who may be graded. |
| `ShowMarksRequest` | Checks the policy and that `?quarter=` is 1 to 4. |
| `UpdateMarksRequest` | Checks the policy first (403 before 422). `quarter` 1 to 4. Each score empty or 0 to 100 with at most 2 decimals. Rejects any student not in the subject's section (a closure rule). |
| `MarkController` | `edit` and `update`. Thin: gets the models, calls the service, returns a view or redirect. |
| `marks/edit` | Quarter links, then a table of student number, name and score box. Empty box means no mark yet. |

### Routes (teachers only)
```php
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/subjects', ...)->name('subjects.index');
    Route::get('/subjects/create', ...)->name('subjects.create');
    Route::post('/subjects', ...)->name('subjects.store');
    Route::get('/subjects/{subject}/marks', ...)->name('marks.edit');   // ?quarter=1..4
    Route::put('/subjects/{subject}/marks', ...)->name('marks.update');
});
```
Check: `php artisan route:list --path=subjects`.

---

## 6. Key ideas I learned

- **Form Request:** validates before the controller runs; failures go back to the form with errors.
- **Policy + `Gate::authorize()`:** one place decides who may do what; false means **403**.
- **Route model binding:** `{subject}` in the URL becomes a `Subject`; a missing id is **404**.
- **`@csrf` and `@method('PUT')`:** every form needs the first; PUT forms need the second.
- **`old()`:** keeps what the user typed after an error.
- **Avoid one query per row:** `with()` / `pluck()`. Here `roster()` runs the same few queries for 2 or 200 students.
- **Transaction:** `DB::transaction()` saves everything or nothing.
- **`updateOrCreate`:** update if the row exists, else create.
- **Never trust the form for ownership:** `teacher_id` is set from `$request->user()->id`.
- **Unique rule scoped to another field:** `Rule::unique('subjects','code')->where('section_id', ...)` matches the database's two-column unique key.

Score rule used for each box:
```php
'scores.*' => ['nullable', 'numeric', 'between:0,100', 'decimal:0,2'],
```

---

## 7. Assumptions: what became of them

| Old guess | Reality | Status |
|---|---|---|
| `marks.grade` can be NULL | `score`, NOT NULL, plus `quarter` | Fixed. Empty box deletes the row. |
| `Subject::students()` | No enrollment; use the section | Fixed (`$subject->section->students()`). |
| Subject has only `name` | Also `code` and `section_id` | Fixed. |
| `Student::fullName()` | Name is on the user | Fixed (`->with('user')`). |
| Role middleware `role:teacher` | Seat 2 merged `EnsureRole` with alias `role`. My routes now use `role:teacher,admin` (Seat 2 changed them). | Fixed. |

---

## 8. Tests (PHPUnit, 41 pass in total)

Run: `php artisan test` (needs MariaDB running, and `report_cards_test` to exist).

- `tests/Feature/SubjectTest.php` (9 tests): own subjects only, login required, `teacher_id` from the login, duplicate code per section rejected, bad input rejected.
- `tests/Feature/MarkTest.php` (18 tests): another teacher gets 403 (page and save), 404 for unknown subject, only the section's students, per-quarter scores, update not duplicate, empty box deletes, scores 0 to 100 with 2 decimals, bad quarter, student from another section rejected.

The temporary `withoutMiddleware('role')` lines are **gone** now that the real middleware is merged. Two new tests check that a **student gets 403** on the subject and marks pages.

Tip learned: `withoutMiddleware()` needs the plain alias (`role`), not `role:teacher`.

Note: the middleware lets `admin` in too. An admin passes `role`, but `MarkPolicy` still gives 403 on marks they do not teach.

---

## 9. Git state

- Commit `6fee997`: first version (merged to `main` by a teammate as PR #2).
- Commit `d295df0`: fit to the real schema and add tests. Merged to `main` as PR #10.
- Commit `b02da38`: **restyle** of `subjects/` and `marks/` with the design standard (see section 11). Pushed to `origin/feat/marks`, needs its own pull request.
- Then `main` (with Seat 5's portal restyle, PR #12) was merged into `feat/marks`, with no conflicts.
- A push alone does **not** create a pull request. Open it on GitHub: **Compare & pull request** banner, or **Pull requests > New pull request** (base `main`, compare `feat/marks`).

---

## 10. Next steps

1. (Done) Seat 2's `role` middleware merged; test bypass removed.
2. Open a pull request from `feat/marks` into `main`.
3. Tell Seat 2 that `.table`, `.table-wrap`, `.num` and `.btn-compose` are missing from the main CSS, so they can move my copies there (then delete mine, keep `.quarter-tabs` and `.input-score`).
4. Open `/subjects` in a browser (log in as `liza@school.edu.ph`, password `password`) and check how the restyled pages look.
5. When Seat 6 adds Pest, convert my two test files.
6. Decide whether the policy should also allow other roles to see marks (for example the principal reads, Seat 6).

---

## 11. Restyle with the design standard

CLAUDE.md says: **always** follow `.claude/agents/frontend-designer.md` for Blade and CSS, and **pull first** before any git action.

- Views: `subjects/index` (page header, compose button, table, empty state), `subjects/create` (narrow card, `.input` fields, error text under each field, form actions), `marks/edit` (quarter tabs, score table, per-row errors, form actions).
- The shared layout shows the `status` message once, so my views no longer print it.
- `school.css` is owned by Seat 2. I only **appended** one marked block at the end: `/* ==== seat 4: ... ==== */` (22 lines: `.table`, `.table-wrap`, `.btn-compose`, `.quarter-tabs`, `.input-score`, `.text-muted`, `.text-small`). Appending at the end of a shared file can cause merge conflicts; if one happens, keep both sides.
- Rules from the standard: no inline `style=""`, no raw hex colours (use `var(--...)`), no CDN, no Tailwind classes in Blade, no `[[placeholders]]` left over.
- I delegated this to the `frontend-designer` agent and then **checked its work myself**: CSS diff was append-only, no hex or inline styles, views compile, 39 tests pass.
- Not yet seen in a browser. Login and the `role` middleware are merged now, so this can be checked (see next steps).

---

## Handy commands

| Command | Use |
|---|---|
| `php artisan serve --port=8005` | Run the app at http://localhost:8005 |
| `php artisan test` | Run all tests |
| `php artisan test tests/Feature/MarkTest.php` | Run one file |
| `php artisan route:list --path=subjects` | See my routes |
| `php artisan migrate` / `php artisan db:seed` | Create tables / fill demo data |
| `php -l file.php` | Syntax-check a PHP file |
| `php artisan view:cache` then `view:clear` | Compile all views to catch errors |
| `mariadb` | Database client (no `-u root`). Never `mysql`. |
