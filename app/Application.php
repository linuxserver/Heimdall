<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'appid';

    //
    public function icon()
    {
        if(!file_exists(storage_path('app/public/'.$this->icon)))  {
            $img_src = app_path('SupportedApps/'.$this->name.'/'.str_replace('icons/', '', $this->icon));
            $img_dest = storage_path('app/public/'.$this->icon);
            //die("i: ".$img_src);
            @copy($img_src, $img_dest);
        }


        return $this->icon;
    }

    public function iconView()
    {
        return asset('storage/'.$this->icon);
    }

    public function defaultColour()
    {
        // check if light or dark
        if($this->tile_background == 'light') return '#fafbfc';
        return '#161b1f';
    }

    public function class()
    {
        $name = $this->name;
        $name = preg_replace('/[^\p{L}\p{N}]/u', '', $name); 

        $class = '\App\SupportedApps\\'.$name.'\\'.$name;
        return $class;
    }

    public static function apps()
    {
        $json = json_decode(file_get_contents(storage_path('app/supportedapps.json'))) ?? [];
        $apps = collect($json->apps);
        $sorted = $apps->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);
        return $sorted;
    }

    public static function autocomplete()
    {
        $apps = self::apps();
        $list = [];
        foreach($apps as $app) {
            $list[] = (object)[
                'label' => $app->name,
                'value' => $app->appid
            ];
        }
        return $list;
    }

    public static function single($appid)
    {
        $apps = self::apps();
        $app = $apps->where('appid', $appid)->first();
        $classname = preg_replace('/[^\p{L}\p{N}]/u', '', $app->name); 
        $app->class = '\App\SupportedApps\\'.$classname.'\\'.$classname;
        return $app;
    }

    public static function applist()
    {
        $list = [];
        $list['null'] = 'None';
        $apps = self::apps();
        foreach($apps as $app) {
            $list[$app->appid] = $app->name;
        }
        return $list;
    }


}
