<?php

namespace Habib\Settings\Http\Middleware;

use Closure;
use Habib\Settings\Models\Setting;

class SettingMiddleware
{
    public function handle($request, Closure $next)
    {
        $settings = cache()->remember('settings', now()->addMinute(), function () {
            return Setting::all();
        });
        config()->set('settings.settings', $settings);

        return $next($request);
    }
}
