<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Application extends Model
{
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $primaryKey = 'appid';

    /**
     * @return mixed
     */
    public function icon()
    {
        if (! file_exists(storage_path('app/public/'.$this->icon))) {
            $img_src = app_path('SupportedApps/'.$this->name.'/'.str_replace('icons/', '', $this->icon));
            $img_dest = storage_path('app/public/'.$this->icon);
            //die("i: ".$img_src);
            @copy($img_src, $img_dest);
        }

        return $this->icon;
    }

    /**
     * @return string
     */
    public function iconView(): string
    {
        return asset('storage/'.$this->icon);
    }

    /**
     * @return string
     */
    public function defaultColour(): string
    {
        // check if light or dark
        if ($this->tile_background == 'light') {
            return '#fafbfc';
        }

        return '#161b1f';
    }

    /**
     * @return string
     */
    public function class(): string
    {
        $name = $this->name;
        $name = preg_replace('/[^\p{L}\p{N}]/u', '', $name);

        $class = '\App\SupportedApps\\'.$name.'\\'.$name;

        return $class;
    }

    /**
     * @param $name
     * @return string
     */
    public static function classFromName($name)
    {
        $name = preg_replace('/[^\p{L}\p{N}]/u', '', $name);

        $class = '\App\SupportedApps\\'.$name.'\\'.$name;

        return $class;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function apps(): \Illuminate\Support\Collection
    {
        $json = json_decode(file_get_contents(storage_path('app/supportedapps.json'))) ?? [];
        $apps = collect($json->apps);

        return $apps->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
    }

    /**
     * @return array
     */
    public static function autocomplete(): array
    {
        $apps = self::apps();
        $list = [];
        foreach ($apps as $app) {
            $list[] = (object) [
                'label' => $app->name,
                'value' => $app->appid,
            ];
        }

        return $list;
    }

    /**
     * @param $appid
     * @return mixed|null
     */
    public static function getApp($appid)
    {
        Log::debug("Get app triggered for: $appid");

        $localapp = self::where('appid', $appid)->first();
        $app = self::single($appid);

        $application = ($localapp) ? $localapp : new self;

        if (! file_exists(app_path('SupportedApps/'.className($app->name)))) {
            SupportedApps::getFiles($app);
            SupportedApps::saveApp($app, $application);
        } else {
            // check if there has been an update for this app
            if ($localapp) {
                if ($localapp->sha !== $app->sha) {
                    SupportedApps::getFiles($app);
                    $app = SupportedApps::saveApp($app, $application);
                }
            } else {
                SupportedApps::getFiles($app);
                $app = SupportedApps::saveApp($app, $application);
            }
        }

        return $app;
    }

    /**
     * @param $appid
     * @return mixed|null
     */
    public static function single($appid)
    {
        $apps = self::apps();
        $app = $apps->where('appid', $appid)->first();

        if ($app === null) {
            // Try in db for Private App
            $appModel = self::where('appid', $appid)->first();
            if($appModel) {
                $app = json_decode($appModel->toJson());
            }
        }

        if ($app === null) {
            return null;
        }
        $classname = preg_replace('/[^\p{L}\p{N}]/u', '', $app->name);
        $app->class = '\App\SupportedApps\\'.$classname.'\\'.$classname;

        return $app;
    }

    /**
     * @return array
     */
    public static function applist(): array
    {
        $list = [];
        $list['null'] = 'None';
        $apps = self::apps();
        foreach ($apps as $app) {
            $list[$app->appid] = $app->name;
        }

        // Check for private apps in the db
        $appsListFromDB = self::all(['appid', 'name']);

        foreach($appsListFromDB as $app) {
            // Already existing keys are overwritten,
            // only private apps should be added at the end
            $list[$app->appid] = $app->name;
        }

        return $list;
    }
}
