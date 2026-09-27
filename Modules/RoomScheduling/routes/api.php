<?php

use Illuminate\Support\Facades\Route;
use Modules\RoomScheduling\Http\Controllers\InstructorController;
use Modules\RoomScheduling\Http\Controllers\RoomController;
use Modules\RoomScheduling\Http\Controllers\ScheduleController;
use Modules\RoomScheduling\Http\Controllers\SectionController;
use Modules\RoomScheduling\Http\Controllers\SubjectController;

/*
 * All routes here are prefixed with /api/room-scheduling and named
 * roomscheduling.api.* by the module's RouteServiceProvider.
 *
 * Wrap this group in your host app's auth/role middleware as needed,
 * e.g. by extending RouteServiceProvider or applying middleware
 * in your enrollment system's own route files that call into these
 * controllers directly.
 */

Route::apiResource('rooms', RoomController::class);
Route::apiResource('instructors', InstructorController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('schedules', ScheduleController::class)->except(['create', 'edit']);

Route::post('schedules-check-conflicts', [ScheduleController::class, 'checkConflicts'])
    ->name('schedules.check-conflicts');

Route::get('schedules-weekly-grid', [ScheduleController::class, 'weeklyGrid'])
    ->name('schedules.weekly-grid');
