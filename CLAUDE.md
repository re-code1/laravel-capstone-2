# CLAUDE.md

## Tech Stack

- **Backend:** Laravel 13 (PHP 8.3) using MariaDB (port 3309)
- **Frontend:** Blade.php with plain CSS (`public/css/school.css`). No Tailwind, no npm, no Vite, no CDN links.

## Environment

- Windows 11, PowerShell, VS Code. Database `report_cards`, user `laravel`, MariaDB on 127.0.0.1 port 3309.
- Use the `mariadb` client, never `mysql` (on this PC `mysql` points at a different server).
- Run the app with `php artisan serve --port=8005` (http://localhost:8005). Tests: `php artisan test`.

## Working Instructions

1. **Discuss before executing.** Always discuss the approach with the user first, and get their agreement before running or writing any code.
2. **Ask when unsure.** Whenever you're unsure of something while coding, stop and ask the user for clarification. Do not guess or assume.
3. **Ask for missing context.** If you lack context, tell the user and ask specifically what you're missing so they can provide it.

## Architecture (the restaurant)

- Web routes only in `routes/web.php`; API routes only in `routes/api.php`.
- Controllers (`app/Http/Controllers`) stay thin: get the models, call the service, return a view. No calculations.
- School rules live only in `app/Services/ReportCardService.php`.
- Models (`app/Models`) hold relationships and small helpers such as `fullName()`. No grading rules.
- Views (`resources/views`) only display data. No queries, no calculations. Every page extends `layouts/app.blade.php`.
- Change the database only with migrations. Never edit a migration that has already run; tell me instead.
- Load relationships with `with()` or `load()` so a page never runs one query per row.

## School rules

- Grades are 60 to 100.
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
