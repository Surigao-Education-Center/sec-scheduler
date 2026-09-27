<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Modules\RoomScheduling\Http\Middleware\EnsureCanManageScheduling;
use Modules\RoomScheduling\Http\Controllers\ScheduleGridController;

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
	Route::post('/login', [AuthController::class, 'login'])->name('login.store');
	Route::post('/login/auto', [AuthController::class, 'autoLogin'])->name('login.auto');
});

Route::post('/logout', [AuthController::class, 'logout'])
	->middleware('auth')
	->name('logout');

Route::middleware(['auth', EnsureCanManageScheduling::class])->group(function () {
	Route::get('/', [ScheduleGridController::class, 'index'])->name('home');
});
