# RoomScheduling Module

Subject plotting and room/instructor scheduling for a Laravel enrollment system, built as an [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules) module. Handles rooms, instructors, subjects, sections, and schedule slots, with built-in conflict validation (no double-booked room, instructor, or section).

## What's included

- **Models**: `Room`, `Instructor`, `Subject`, `Section`, `Schedule`
- **Migrations** for all five tables
- **`ScheduleConflictService`** — checks a proposed slot for room/instructor/section overlaps
- **CRUD controllers + Form Requests** for every model, returning JSON (easy to wire to any frontend/admin panel)
- **`ScheduleController::checkConflicts`** — dry-run endpoint to validate a slot before saving (great for an interactive scheduling UI)
- **`ScheduleController::weeklyGrid`** — JSON timetable data grouped by day
- A basic Blade timetable view at `/room-scheduling/timetable`
- A demo seeder

## Installation (merging into your enrollment system)

1. If not already installed, add the modules package to your Laravel app:
   ```bash
   composer require nwidart/laravel-modules
   php artisan vendor:publish --provider="Nwidart\Modules\LaravelModulesServiceProvider"
   ```

2. Copy this `RoomScheduling` folder into your app's `Modules/` directory (default location — check `modules.paths.modules` in `config/modules.php` if you've customized it):
   ```
   your-project/Modules/RoomScheduling
   ```

3. Register the module (usually automatic if you run):
   ```bash
   composer dump-autoload
   php artisan module:enable RoomScheduling
   ```

4. Run migrations:
   ```bash
   php artisan module:migrate RoomScheduling
   ```
   (or `php artisan migrate` if your setup loads all module migrations together)

5. (Optional) seed demo data:
   ```bash
   php artisan module:seed RoomScheduling
   ```

## Routes

- Web admin UI (prefix `/room-scheduling`):
  - `GET /timetable` — Blade weekly grid
  - Full CRUD (index/create/edit/destroy) for `rooms`, `instructors`, `subjects`, `sections`, `schedules`
  - The schedule create/edit form checks for conflicts **live via JS** as you pick a room/instructor/time, before you even submit
- API (prefix `/api/room-scheduling`):
  - `GET|POST /rooms`, `/instructors`, `/subjects`, `/sections` (+ `{id}` show/update/delete)
  - `GET|POST /schedules` (+ `{id}` show/update/delete)
  - `POST /schedules-check-conflicts` — validate a slot without saving (used by the live form check above, and available for any custom UI)
  - `GET /schedules-weekly-grid?school_year=...&semester=...`

## Access control

Both route groups run through `EnsureCanManageScheduling` middleware, which checks a `manage-scheduling` Gate ability. The module registers a permissive default (`return true`) so it works immediately after install — **replace this before going live**.

To enforce real roles/permissions, define the gate yourself in your host app's `AuthServiceProvider` (or any provider that boots before the module):

```php
Gate::define('manage-scheduling', function ($user) {
    return $user && $user->hasAnyRole(['admin', 'registrar']);
});
```

The module checks `Gate::has('manage-scheduling')` before defining its own fallback, so your app's definition always wins.

The web admin group also includes Laravel's built-in `auth` middleware — remove it in `app/Providers/RouteServiceProvider.php` if your enrollment system uses a different guard/setup, or if these routes should be reachable without a logged-in session.

## Blade layout

All views `@extends('layouts.app')` and use Bootstrap classes (`table`, `btn`, `alert`, `form-control`, etc.) — the same convention as most Laravel starter kits. If your enrollment system uses Tailwind or a different base layout, swap the `@extends` target and class names; the controllers/routes/logic don't depend on styling.

## Wiring into an existing enrollment flow

If your enrollment system already has its own `Subject`/`Course` model, you have two options:

1. **Use this module's `Subject`/`Section` tables** and link your existing enrollment records to `sections.id`.
2. **Drop this module's `Subject` migration/model** and point `Section::subject()` at your existing subject model/table instead — the rest (Room, Instructor, Schedule, conflict service) works independently of where subjects come from.

## Conflict validation logic

A new/updated `Schedule` row is rejected if, for the same `day` + `school_year` + `semester`, its `[start_time, end_time)` range overlaps an existing row sharing the same `room_id`, `instructor_id`, or `section_id`. This runs automatically in `ScheduleRequest::withValidator()`, and is also exposed standalone via `ScheduleConflictService` for use in custom UI (e.g. live-checking as an admin picks a room/time in a dropdown).
