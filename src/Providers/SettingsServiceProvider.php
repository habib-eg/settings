<?php

namespace Habib\Settings\Providers;

use Habib\Settings\Http\Middleware\SettingMiddleware;
use Habib\Settings\Router;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        \Illuminate\Routing\Router::mixin(new Router());

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/config/settings.php', 'settings'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $package_path = dirname(__DIR__);
        $this->publishesFiles($package_path);
        $this->loadFiles($package_path);
        // add middleware
        $kernel = app()->make(Kernel::class);
        $kernel->appendMiddlewareToGroup('web', SettingMiddleware::class);
    }

    public function publishesFiles(string $package_path): void
    {
        //publish config settings.php
        $this->publishes([
            $package_path . '/config/settings.php' => config_path('settings.php'),
        ], 'config');
        //publish views
        $this->publishes([
            $package_path . '/resources/views' => resource_path('views/vendor/settings'),
        ], 'views');
        //publish views
        $this->publishes([
            $package_path . '/resources/lang' => resource_path('lang/vendor/settings'),
        ], 'lang');
        //publish assets
        $this->publishes([
            $package_path . '/resources/assets' => public_path('vendor/settings'),
        ], 'assets');
        //publish migrations
        $this->publishes([
            $package_path . '/database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    public function loadFiles(string $package_path): void
    {
        $this->loadViewsFrom($package_path . '/resources/views', 'settings');
        $this->loadViewsFrom($package_path . '/resources/views', 'settings');
        $this->loadTranslationsFrom($package_path . '/resources/lang', 'settings');
        $this->loadMigrationsFrom($package_path . '/database/migrations');
        $this->loadFactoriesFrom($package_path . '/database/factory');
    }

}
