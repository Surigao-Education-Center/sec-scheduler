<?php

use Illuminate\Support\Facades\Route;
use Modules\RoomScheduling\Http\Controllers\Admin\InstructorAdminController;
use Modules\RoomScheduling\Http\Controllers\Admin\RoomAdminController;
use Modules\RoomScheduling\Http\Controllers\Admin\ScheduleAdminController;
use Modules\RoomScheduling\Http\Controllers\Admin\SectionAdminController;
use Modules\RoomScheduling\Http\Controllers\Admin\SubjectAdminController;
use Modules\RoomScheduling\Http\Controllers\ReportsController;
use Modules\RoomScheduling\Http\Controllers\ScheduleGridController;

/*
 * Prefixed with /room-scheduling and named roomscheduling.* by the
 * module's RouteServiceProvider, which already applies 'auth' and the
 * "manage-scheduling" gate (via EnsureCanManageScheduling) to this
 * whole group — see RouteServiceProvider::boot().
 */

Route::get('/timetable', [ScheduleGridController::class, 'index'])->name('timetable');
Route::patch('/timetable/schedules/{schedule}/position', [ScheduleGridController::class, 'updatePosition'])->name('timetable.position');

Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
Route::get('/reports/export/csv', [ReportsController::class, 'exportCsv'])->name('reports.export.csv');
Route::get('/reports/print', [ReportsController::class, 'print'])->name('reports.print');

Route::resource('rooms', RoomAdminController::class)->except('show');
Route::resource('instructors', InstructorAdminController::class)->except('show');
Route::resource('subjects', SubjectAdminController::class)->except('show');
Route::resource('sections', SectionAdminController::class)->except('show');
Route::resource('schedules', ScheduleAdminController::class)->except('show');
