<?php

namespace App\Http\Controllers;

use App\Setting;
use App\SettingGroup;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('allowed');
    }

    public function index(): View
    {
        $settings = SettingGroup::with([
            'settings',
        ])->orderBy('order', 'ASC')->get();

        return view('settings.list')->with([
            'groups' => $settings,
        ]);
    }

    /**
     *
     * @return RedirectResponse|View
     */
    public function edit(int $id)
    {
        $setting = Setting::find($id);
        //die("s: ".$setting->label);

        if ((bool) $setting->system === true) {
            return abort(404);
        }

        if (! is_null($setting)) {
            return view('settings.edit')->with([
                'setting' => $setting,
            ]);
        } else {
            $route = route('settings.list', []);

            return redirect($route)
            ->with([
                'errors' => collect([__('app.alert.error.not_exist')]),
            ]);
        }
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $setting = Setting::find($id);
        $user = $this->user();
        $route = route('settings.index', []);

        try {
            if (is_null($setting)) {
                throw new Exception('not_exists');
            }

            if ($setting->type === 'image') {
                $validatedData = $request->validate([
                    'value' => 'image'
                ]);

                if (!$request->hasFile('value')) {
                    throw new \Exception(
                        'file_too_big'
                    );
                }

                $path = $request->file('value')->store('backgrounds', 'public');

                if ($path === null) {
                    throw new \Exception('file_not_stored');
                }

                $setting_value = $path;
            } else {
                $data = Setting::getInput($request);
                $setting_value = $data->value;
            }

            $user->settings()->detach($setting->id);
            $user->settings()->save($setting, ['uservalue' => $setting_value]);

            return redirect($route)
                ->with([
                    'success' => __('app.alert.success.setting_updated'),
                ]);
        } catch (Exception $e) {
            return redirect($route)
                ->with([
                    'errors' => collect([__('app.alert.error.'.$e->getMessage())]),
                ]);
        }
    }

    public function clear(int $id): RedirectResponse
    {
        $user = $this->user();
        $setting = Setting::find($id);
        if ((bool) $setting->system !== true) {
            $user->settings()->detach($setting->id);
            $user->settings()->save($setting, ['uservalue' => '']);
        }
        $route = route('settings.index', []);

        return redirect($route)
        ->with([
            'success' => __('app.alert.success.setting_updated'),
        ]);
    }

    public function search(Request $request)
    {
    }
}
