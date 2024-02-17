<?php

namespace App;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * App\Application
 *
 * @property string $appid
 * @property string $name
 * @property string|null $sha
 * @property string|null $icon
 * @property string|null $website
 * @property string|null $license
 * @property string|null $description
 * @property int $enhanced
 * @property string $tile_background
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $class
 * @method static Builder|Application newModelQuery()
 * @method static Builder|Application newQuery()
 * @method static Builder|Application query()
 * @method static Builder|Application whereAppid($value)
 * @method static Builder|Application whereClass($value)
 * @method static Builder|Application whereCreatedAt($value)
 * @method static Builder|Application whereDescription($value)
 * @method static Builder|Application whereEnhanced($value)
 * @method static Builder|Application whereIcon($value)
 * @method static Builder|Application whereLicense($value)
 * @method static Builder|Application whereName($value)
 * @method static Builder|Application whereSha($value)
 * @method static Builder|Application whereTileBackground($value)
 * @method static Builder|Application whereUpdatedAt($value)
 * @method static Builder|Application whereWebsite($value)
 */
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

    public function iconView(): string
    {
        return asset('storage/'.$this->icon);
    }

    public function defaultColour(): string
    {
        // check if light or dark
        if ($this->tile_background == 'light') {
            return '#fafbfc';
        }

        return '#161b1f';
    }

    public function class(): string
    {
        $name = $this->name;
        $name = preg_replace('/[^\p{L}\p{N}]/u', '', $name);

        return \App\SupportedApps::class.'\\'.$name.'\\'.$name;
    }

    /**
     * @param $name
     */
    public static function classFromName($name): string
    {
        $name = preg_replace('/[^\p{L}\p{N}]/u', '', $name);

        $class = \App\SupportedApps::class.'\\'.$name.'\\'.$name;

        return $class;
    }

    public static function apps(): Collection
    {
        $json = json_decode(file_get_contents(storage_path('app/supportedapps.json'))) ?? [];
        $apps = collect($json->apps);

        return $apps->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
    }

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
     * @throws GuzzleException
     */
    public static function getApp($appid)
    {
        Log::debug("Get app triggered for: $appid");

        $localapp = self::where('appid', $appid)->first();
        $app = self::single($appid);

        $application = ($localapp) ? $localapp : new self;

        // Files missing? || app not in db || old sha version
        if (! file_exists(app_path('SupportedApps/'.className($app->name))) ||
            ! $localapp ||
            $localapp->sha !== $app->sha
        ) {
            $gotFiles = SupportedApps::getFiles($app);
            if ($gotFiles) {
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
            if ($appModel) {
                $app = json_decode($appModel->toJson());
            }
        }

        if ($app === null) {
            return null;
        }
        $classname = preg_replace('/[^\p{L}\p{N}]/u', '', $app->name);
        $app->class = \App\SupportedApps::class.'\\'.$classname.'\\'.$classname;

        return $app;
    }

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

        foreach ($appsListFromDB as $app) {
            // Already existing keys are overwritten,
            // only private apps should be added at the end
            $list[$app->appid] = $app->name;
        }

        return $list;
    }
}
