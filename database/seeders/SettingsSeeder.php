<?php

namespace Database\Seeders;

use App\Setting;
use App\SettingGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Locale;

class SettingsSeeder extends Seeder
{

    /**
     * @return false|string
     */
    public static function getSupportedLanguageMap()
    {
        if (! class_exists('Locale')) {
            Log::info('PHP Extension Intl not found. Falling back to English language support only.');
            return json_encode(['en' => 'English']);
        }

        $languageDirectories = array_filter(glob(lang_path().'/*'), 'is_dir');
        $result = [];

        foreach ($languageDirectories as $languageDirectory) {
            $language = self::getLanguageFromDirectory($languageDirectory);
            $resultNative = mb_convert_case(
                Locale::getDisplayLanguage($language.'-', $language),
                MB_CASE_TITLE,
                'UTF-8'
            );
            $resultEn = ucfirst(Locale::getDisplayLanguage($language, 'en'));
            $result[$language] = "$resultNative ($resultEn)";
        }

        return json_encode($result);
    }

    /**
     * @param $languageDirectory
     * @return false|string[]
     */
    public static function getLanguageFromDirectory($languageDirectory)
    {
        $directories = explode('/', $languageDirectory);

        return $directories[array_key_last($directories)];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Groups
        if (! $setting_group = SettingGroup::find(1)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 1;
            $setting_group->title = 'app.settings.system';
            $setting_group->order = 0;
            $setting_group->save();
        } else {
            $setting_group->title = 'app.settings.system';
            $setting_group->save();
        }
        if (! $setting_group = SettingGroup::find(2)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 2;
            $setting_group->title = 'app.settings.appearance';
            $setting_group->order = 1;
            $setting_group->save();
        } else {
            $setting_group->title = 'app.settings.appearance';
            $setting_group->save();
        }
        if (! $setting_group = SettingGroup::find(3)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 3;
            $setting_group->title = 'app.settings.miscellaneous';
            $setting_group->order = 2;
            $setting_group->save();
        } else {
            $setting_group->title = 'app.settings.miscellaneous';
            $setting_group->save();
        }
        if (! $setting_group = SettingGroup::find(4)) {
            $setting_group = new SettingGroup;
            $setting_group->id = 4;
            $setting_group->title = 'app.settings.advanced';
            $setting_group->order = 3;
            $setting_group->save();
        } else {
            $setting_group->title = 'app.settings.advanced';
            $setting_group->save();
        }

        if ($version = Setting::find(1)) {
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

        if (! $setting = Setting::find(2)) {
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
        if (! $setting = Setting::find(3)) {
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
            'qwant' => 'app.options.qwant',
            'bing' => 'app.options.bing',
            'startpage' => 'app.options.startpage',
        ]);

        if (! $setting = Setting::find(4)) {
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

        $language_options = SettingsSeeder::getSupportedLanguageMap();

        if ($languages = Setting::find(5)) {
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
        
        if (! $setting = Setting::find(12)) {
            $setting = new Setting;
            $setting->id = 12;
            $setting->group_id = 2;
            $setting->key = 'trianglify';
            $setting->type = 'boolean';
            $setting->label = 'app.settings.trianglify';
            $setting->save();
        } else {
            $setting->label = 'app.settings.trianglify';
            $setting->save();
        }
        
        if (! $setting = Setting::find(13)) {
            $setting = new Setting;
            $setting->id = 13;
            $setting->group_id = 2;
            $setting->key = 'trianglify_seed';
            $setting->type = 'text';
            $setting->value = 'heimdall';
            $setting->label = 'app.settings.trianglify_seed';
            $setting->save();
        } else {
            $setting->label = 'app.settings.trianglify_seed';
            $setting->save();
        }

        $window_target_options = json_encode([
            'current' => 'app.settings.window_target.current',
            'heimdall' => 'app.settings.window_target.one',
            '_blank' => 'app.settings.window_target.new',
        ]);

        if (! $setting = Setting::find(7)) {
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

        if ($support = Setting::find(8)) {
            $support->label = 'app.settings.support';
            $support->value =
                '<a rel="noopener" target="_blank" href="https://discord.gg/CCjHKn4">Discord</a>'.
                ' | '.
                '<a rel="noopener" target="_blank" href="https://github.com/linuxserver/Heimdall">Github</a>'.
                ' | '.
                '<a rel="noopener" target="_blank" href="https://blog.heimdall.site/">Blog</a>';
            $support->save();
        } else {
            $setting = new Setting;
            $setting->id = 8;
            $setting->group_id = 1;
            $setting->key = 'support';
            $setting->type = 'text';
            $setting->label = 'app.settings.support';
            $setting->value = '<a rel="noopener" target="_blank" href="https://discord.gg/CCjHKn4">Discord</a>'.
                ' | '.
                '<a rel="noopener" target="_blank" href="https://github.com/linuxserver/Heimdall">Github</a>'.
                ' | '.
                '<a rel="noopener" target="_blank" href="https://blog.heimdall.site/">Blog</a>';
            $setting->system = true;
            $setting->save();
        }

        if ($donate = Setting::find(9)) {
            $donate->label = 'app.settings.donate';
            $donate->value = '<a rel="noopener" target="_blank" href="https://www.paypal.me/heimdall">Paypal</a>';
            $donate->save();
        } else {
            $setting = new Setting;
            $setting->id = 9;
            $setting->group_id = 1;
            $setting->key = 'donate';
            $setting->type = 'text';
            $setting->label = 'app.settings.donate';
            $setting->value = '<a rel="noopener" target="_blank" href="https://www.paypal.me/heimdall">Paypal</a>';
            $setting->system = true;
            $setting->save();
        }

        if (! $setting = Setting::find(10)) {
            $setting = new Setting;
            $setting->id = 10;
            $setting->group_id = 4;
            $setting->key = 'custom_css';
            $setting->type = 'textarea';
            $setting->label = 'app.settings.custom_css';
            $setting->value = '';
            $setting->save();
        } else {
            $setting->type = 'textarea';
            $setting->group_id = 4;
            $setting->label = 'app.settings.custom_css';
            $setting->save();
        }

        if (! $setting = Setting::find(11)) {
            $setting = new Setting;
            $setting->id = 11;
            $setting->group_id = 4;
            $setting->key = 'custom_js';
            $setting->type = 'textarea';
            $setting->label = 'app.settings.custom_js';
            $setting->value = '';
            $setting->save();
        } else {
            $setting->type = 'textarea';
            $setting->group_id = 4;
            $setting->label = 'app.settings.custom_js';
            $setting->save();
        }

        if (! $home_tag = \App\Item::find(0)) {
            $home_tag = new \App\Item;
            $home_tag->id = 0;
            $home_tag->title = 'app.dashboard';
            $home_tag->pinned = 0;
            $home_tag->url = '';
            $home_tag->type = 1;
            $home_tag->user_id = 0;

            $home_tag->save();
            $home_tag_id = $home_tag->id;

            if ($home_tag_id != 0) {
                Log::info("Home Tag returned with id $home_tag_id from db! Changing to 0.");

                DB::update('update items set id = 0 where id = ?', [$home_tag_id]);
            }

            $homeapps = \App\Item::withoutGlobalScope('user_id')->doesntHave('parents')->get();
            foreach ($homeapps as $app) {
                if ($app->id === 0) {
                    continue;
                }
                $app->parents()->attach(0);
            }
        }

        $tag_options = json_encode([
            'folders' => 'app.settings.folders',
            'tags' => 'app.settings.tags',
            'categories' => 'app.settings.categories',
        ]);

        if (! $setting = Setting::find(14)) {
            $setting = new Setting;
            $setting->id = 14;
            $setting->group_id = 2;
            $setting->key = 'treat_tags_as';
            $setting->type = 'select';
            $setting->options = $tag_options;
            $setting->value = 'folders';
            $setting->label = 'app.settings.treat_tags_as';
            $setting->save();
        } else {
            $setting->options = $tag_options;
            $setting->label = 'app.settings.treat_tags_as';
            $setting->save();
        }

    }
}
