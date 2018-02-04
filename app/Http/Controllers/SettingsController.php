<?php

namespace App\Http\Controllers;

use App\Setting;
use App\SettingGroup;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = SettingGroup::with([
            'settings',
        ])->orderBy('order', 'ASC')->get();

        return view('settings.list')->with([
            'groups' => $settings,
        ]);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $setting = Setting::find($id);

        if (!is_null($setting)) {
            return view('settings.edit')->with([
                'setting' => $setting,
            ]);
        } else {
            return redirect()->route('settings.list')->with([
                'error' => 'This Setting does not exist.',
            ]);
        }
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        $setting = Setting::find($id);

        if (!is_null($setting)) {
            $data = Setting::getInput();

            if ($setting->type == 'image') {
                if (!is_null($data->image) && $data->image->isValid()) {
                    $destinationPath = uploads_path().'/settings/';
                    $extension = $data->image->getClientOriginalExtension();
                    $fileName = rand(11111111, 99999999).'.'.$extension;
                    $data->image->move($destinationPath, $fileName);
                    $setting->value = $fileName;
                }
            } else {
                $setting->value = $data->value;
            }

            $setting->save();

            return redirect()->route('settings.list')->with([
                'success' => 'You have successfully edited this Setting!',
            ]);
        } else {
            return redirect()->route('settings.list')->with([
                'error' => 'This Setting does not exist.',
            ]);
        }
    }
}
