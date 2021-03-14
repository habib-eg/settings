<?php


namespace Habib\Settings;

use Habib\Settings\Http\Controllers\SettingController;

/**
 * Class Router
 * @package Habib\Settings
 */
class Router
{
    /**
     * @return \Closure
     */
    public function settingRoutes()
    {
        return function (array $options = [])  {
            $options['prefix'] = $options['prefix'] ?? config('settings.route_prefix', '');
            $options['middleware'] = $options['middleware'] ?? config('settings.middleware', []);
            $this->group($options, function () {
                $this->post('/forceRefreshCache', ['\\'.SettingController::class, 'forceRefreshCache'])->name('clear.cache');
                $this->resource('setting','\\'.SettingController::class)->only('update', 'index');
            });
        };
    }

//    public static function routes()
//    {
//        $options['prefix'] = $options['prefix'] ?? config('settings.route_prefix', '');
//        $options['middleware'] = $options['middleware'] ?? config('settings.middleware', []);
//         \Route::group($options, function () {
//            \Route::post('/forceRefreshCache', [SettingController::class, 'forceRefreshCache'])->name('clear.cache');
//            \Route::resource('setting', SettingController::class)->only('update', 'index');
//        });
//    }
}
