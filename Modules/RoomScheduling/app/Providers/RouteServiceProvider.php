<?php

namespace Modules\RoomScheduling\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Modules\RoomScheduling\Http\Middleware\EnsureCanManageScheduling;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'RoomScheduling';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // Read/write JSON API. Gated by the "manage-scheduling" ability —
            // see RoomSchedulingServiceProvider::registerGates() to plug in
            // real roles/permissions.
            Route::middleware(['api', 'auth', EnsureCanManageScheduling::class])
                ->prefix('api/room-scheduling')
                ->name('roomscheduling.api.')
                ->group(module_path($this->name, '/routes/api.php'));

            // Blade scheduling UI (timetable + CRUD forms). Authentication can
            // be added by the host app once its login routes are registered.
            Route::middleware(['web', 'auth', EnsureCanManageScheduling::class])
                ->prefix('room-scheduling')
                ->name('roomscheduling.')
                ->group(module_path($this->name, '/routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('roomscheduling-api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
