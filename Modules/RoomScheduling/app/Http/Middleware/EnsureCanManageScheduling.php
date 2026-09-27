<?php

namespace Modules\RoomScheduling\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate stub for scheduling management.
 *
 * By default this checks the "manage-scheduling" ability, which the
 * module registers with a permissive fallback (see
 * RoomSchedulingServiceProvider::registerGates()) so the module works
 * out of the box.
 *
 * To enforce real access control, define your own gate in your host
 * app's AuthServiceProvider *before* the module boots, e.g.:
 *
 *   Gate::define('manage-scheduling', function ($user) {
 *       return $user->hasRole('registrar') || $user->hasRole('admin');
 *   });
 *
 * The module only defines the gate if it isn't already defined, so
 * your app's definition always wins.
 */
class EnsureCanManageScheduling
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Gate::forUser($request->user())->allows('manage-scheduling')) {
            abort(403, 'You are not authorized to manage room scheduling.');
        }

        return $next($request);
    }
}
