<?php

namespace Habib\Settings\Http\Controllers;

use Habib\Settings\Models\Setting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|Response|View
     */
    public function index()
    {
        $settings = Setting::search('name', true)->orWhere->search('value', true)->search('locale')->orderBy('group_by')->get();
        $headers=Setting::get()->pluck('group_by')->unique()->values()->map(function ($value){ return $value ?? 'others'; })->sortBy('group_by')->toArray();

        return view('settings::index', compact('settings','headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Setting $setting
     * @return Response
     */
    public function update(Request $request, Setting $setting)
    {
        // need to check file case
        if ($setting->type == 'file' && $request->hasFile('value')) {
            $setting->update(['value' => uploader($request->file('value'), 'settings')]);
        } else {
            $setting->update($request->validate(Setting::validateUpdate()));
        }
        return back()->withSuccess(' updated ');
    }
    public function updateMany()
    {
        $validated = request()->validate([
            "settings"=>['required','array'],
            "settings.*.id"=>['required','exists:settings,id']
        ]);
        foreach ($validated as $setting) {

        }
        return back()->withSuccess(' updated ');
    }

    public function forceRefreshCache(Request $request){
        cache()->clear();
        return back()->with('Force Refresh Cache');
    }

}
