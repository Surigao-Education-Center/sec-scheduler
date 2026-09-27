# Timetable Scheduler Deployment

This is a standalone Laravel 9 application containing only the college timetable scheduler and the supporting institution, department, program, section, course, faculty, room, term, weekday, and academic-year data needed by it.

## Local setup

```powershell
composer install
npm install
Copy-Item .env.example .env
New-Item -ItemType File database/database.sqlite -Force
php artisan key:generate
php artisan migrate:fresh --seed --force
npm run build
php artisan serve --host 127.0.0.1 --port 8010
```

Open `http://127.0.0.1:8010/scheduler`.

## Production deployment

1. Point the document root at `public/`.
2. Set `APP_ENV=production`, `APP_DEBUG=false`, and a stable `APP_KEY` in `.env`.
3. Use an absolute `DB_DATABASE` path for SQLite, or switch `DB_CONNECTION` and the `DB_*` values to MySQL/PostgreSQL.
4. Run `composer install --no-dev --optimize-autoloader` and `npm run build` during deployment.
5. Run `php artisan migrate --force --seed` for a new installation.
6. Run `php artisan config:cache` and `php artisan view:cache`.

## Exported scheduler files

- `app/Http/Livewire/TimetableScheduler.php`
- `app/Models/School.php`, `Department.php`, `Program.php`, `Classes.php`
- `app/Models/Subject.php`, `Instructor.php`, `Room.php`, `AcademicYear.php`, `Semester.php`
- `app/Models/Timetable.php`, `TimeTableTimeSlot.php`, `WeekDay.php`
- `database/migrations/2026_09_21_000000_create_scheduler_tables.php`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/livewire/timetable-scheduler.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/css/app.css`

## College scheduling model

- A **program** belongs to a department, for example `BSCS`.
- A **section** is represented by a class record with program, year level, section name, and capacity.
- A **course** keeps its academic code and credit hours.
- Each weekly meeting assignment stores its course, instructor, and room.
- Rooms include building names and seat capacity for registrar planning.
- Student registration respects section capacity, places overflow registrations on a waitlist, and promotes the oldest waitlisted student when a registered student drops.
- Instructor and room availability windows are checked before a meeting is assigned.
- Section, instructor, and room calendar views are available from the Calendars tab.
- Draft, review, published, and archived timetable states are audited in `timetable_audits`.
- `calendar/export` returns an `.ics` calendar file; `api/schedule/feed` returns JSON for integrations.
- The dashboard can generate draft meetings for unassigned courses using available instructors and rooms.
