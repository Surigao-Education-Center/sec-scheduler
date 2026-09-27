<?php

namespace Modules\RoomScheduling\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RoomSchedulingServiceProvider extends ServiceProvider
{
    protected string $name = 'RoomScheduling';
    protected string $nameLower = 'roomscheduling';

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerGates();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    protected function registerGates(): void
    {
        if (! Gate::has('manage-scheduling')) {
            Gate::define('manage-scheduling', function ($user): bool {
                return $user?->canManageScheduling() ?? false;
            });
        }
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path($this->nameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->name, 'config/config.php'),
            $this->nameLower
        );
    }

    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge(
            $this->getPublishableViewPaths(),
            [$sourcePath]
        ), $this->nameLower);
    }

    protected function getPublishableViewPaths(): array
    {
        $paths = [];

        foreach (config('view.paths', []) as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}
