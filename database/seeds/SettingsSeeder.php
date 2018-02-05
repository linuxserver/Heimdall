<?php

use Illuminate\Database\Seeder;
use App\Setting;
use App\SettingGroup;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Groups
        if(!SettingGroup::find(1)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 1;
            $setting_group->title = 'System';
            $setting_group->order = 0;
            $setting_group->save();
        }
        if(!SettingGroup::find(2)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 2;
            $setting_group->title = 'Appearance';
            $setting_group->order = 1;
            $setting_group->save();
        }
        if(!SettingGroup::find(3)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 3;
            $setting_group->title = 'Miscellaneous';
            $setting_group->order = 2;
            $setting_group->save();
        }

        if(!Setting::find(1)) {
            $setting = new Setting;
            $setting->id = 1;
            $setting->group_id = 1;
            $setting->key = 'version';
            $setting->type = 'text';
            $setting->label = 'Version';
            $setting->value = config('app.version');
            $setting->system = true;
            $setting->save();
        }
        if(!Setting::find(2)) {
            $setting = new Setting;
            $setting->id = 2;
            $setting->group_id = 2;
            $setting->key = 'background_image';
            $setting->type = 'image';
            $setting->label = 'Background Image';
            $setting->save();
        }
        if(!Setting::find(3)) {
            $setting = new Setting;
            $setting->id = 3;
            $setting->group_id = 3;
            $setting->key = 'homepage_search';
            $setting->type = 'boolean';
            $setting->label = 'Homepage Search';
            $setting->save();
        }
        if(!Setting::find(4)) {
            $options = json_encode([
                'none' => '- not set -',
                'google' => 'Google',
                'ddg' => 'DuckDuckGo',
                'bing' => 'Bing'
            ]);
            
            $setting = new Setting;
            $setting->id = 4;
            $setting->group_id = 3;
            $setting->key = 'search_provider';
            $setting->type = 'select';
            $setting->options = $options;
            $setting->label = 'Search Provider';
            $setting->save();
        }
        
    }
}
