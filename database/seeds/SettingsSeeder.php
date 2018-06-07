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
        if(!$setting_group = SettingGroup::find(1)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 1;
            $setting_group->title = 'app.settings.system';
            $setting_group->order = 0;
            $setting_group->save();
        } else {
            $setting_group->title = 'app.settings.system';
            $setting_group->save();
        }
        if(!$setting_group = SettingGroup::find(2)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 2;
            $setting_group->title = 'app.settings.appearance';
            $setting_group->order = 1;
            $setting_group->save();
        } else {
            $setting_group->title = 'app.settings.appearance';
            $setting_group->save();
        }
        if(!$setting_group = SettingGroup::find(3)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 3;
            $setting_group->title = 'app.settings.miscellaneous';
            $setting_group->order = 2;
            $setting_group->save();
        } else {
            $setting_group->title = 'app.settings.miscellaneous';
            $setting_group->save();
        }

        if($version = Setting::find(1)) {
            $version->label = 'app.settings.version';
            $version->value = config('app.version');
            $version->save();
        } else {
            $setting = new Setting;
            $setting->id = 1;
            $setting->group_id = 1;
            $setting->key = 'version';
            $setting->type = 'text';
            $setting->label = 'app.settings.version';
            $setting->value = config('app.version');
            $setting->system = true;
            $setting->save();
        }

        if(!$setting = Setting::find(2)) {
            $setting = new Setting;
            $setting->id = 2;
            $setting->group_id = 2;
            $setting->key = 'background_image';
            $setting->type = 'image';
            $setting->label = 'app.settings.background_image';
            $setting->save();
        } else {
            $setting->label = 'app.settings.background_image';
            $setting->save();
        }
        if(!$setting = Setting::find(3)) {
            $setting = new Setting;
            $setting->id = 3;
            $setting->group_id = 3;
            $setting->key = 'homepage_search';
            $setting->type = 'boolean';
            $setting->label = 'app.settings.homepage_search';
            $setting->save();
        } else {
            $setting->label = 'app.settings.homepage_search';
            $setting->save();
        }

        $options = json_encode([
            'none' => 'app.options.none',
            'google' => 'app.options.google',
            'ddg' => 'app.options.ddg',
            'bing' => 'app.options.bing',
            'startpage' => 'app.options.startpage',
        ]);

        if(!$setting = Setting::find(4)) {
            
            $setting = new Setting;
            $setting->id = 4;
            $setting->group_id = 3;
            $setting->key = 'search_provider';
            $setting->type = 'select';
            $setting->options = $options;
            $setting->label = 'app.settings.search_provider';
            $setting->save();
        } else {
            $setting->options = $options;
            $setting->label = 'app.settings.search_provider';
            $setting->save();
        }


        $language_options = json_encode([
            'de' => 'Deutsch (German)',
            'en' => 'English',
            'fi' => 'Suomi (Finnish)',
            'fr' => 'Français (French)',
            'it' => 'Italiano (Italian)',
            'no' => 'Norsk (Norwegian)',   
            'pl' => 'Polski (Polish)',            
            'sv' => 'Svenska (Swedish)',
            'es' => 'Español (Spanish)',
            'tr' => 'Türkçe (Turkish)',
        ]);
        if($languages = Setting::find(5)) {
            $languages->options = $language_options;
            $languages->save();
        } else {
            $setting = new Setting;
            $setting->id = 5;
            $setting->group_id = 1;
            $setting->key = 'language';
            $setting->type = 'select';
            $setting->label = 'app.settings.language';
            $setting->options = $language_options;
            $setting->value = 'en';
            $setting->save();
        }

        if(!$setting = Setting::find(6)) {
            $setting = new Setting;
            $setting->id = 6;
            $setting->group_id = 2;
            $setting->key = 'trianglify';
            $setting->type = 'boolean';
            $setting->label = 'app.settings.trianglify';
            $setting->save();
        } else {
            $setting->label = 'app.settings.trianglify';
            $setting->save();
        }

        $window_target_options = json_encode([
            'current' => 'app.settings.window_target.current',
            'heimdall' => 'app.settings.window_target.one',
            '_blank' => 'app.settings.window_target.new',
        ]);

        if(!$setting = Setting::find(7)) {
            
            $setting = new Setting;
            $setting->id = 7;
            $setting->group_id = 3;
            $setting->key = 'window_target';
            $setting->type = 'select';
            $setting->options = $window_target_options;
            $setting->label = 'app.settings.window_target';
            $setting->value = 'heimdall';
            $setting->save();
        } else {
            $setting->options = $window_target_options;
            $setting->label = 'app.settings.window_target';
            $setting->save();
        }

    }
}
