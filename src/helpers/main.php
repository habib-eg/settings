<?php

use Habib\Settings\Models\Setting;

/**
 * @param string $name
 * @param string $default
 * @param string|null $type
 * @param string|null $group_by
 * @param string|null $locale
 * @return string
 */
function setting(string $name, $default = '', string $type = null, string $group_by = null, string $locale = null): string
{

    $locale = $locale ?? substr(app()->getLocale(), 0, 2);
    $type = $type ?? 'string';
    if (cache()->has('settings')) {
        $settings = cache()->get('settings');
    }else{
        $settings = cache()->remember('settings',now()->addMinutes(5),fn()=>Setting::all());
    }

    if ($setting = $settings->where('name', $name)->firstWhere('locale', '=', $locale)) {
        return $setting->value ?? $default;
    }

    return Setting::firstOrCreate(
            ['name' => $name, 'locale' => $locale],
            ['name' => $name, 'type' => $type, 'locale' => $locale, 'value' => $default ?? $name, 'group_by' => $group_by]
        )->value ?? $default;
}
