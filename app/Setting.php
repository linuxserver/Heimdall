<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

class Setting extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Tell the Model this Table doesn't support timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Cache storage for Settings.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * @return array
     */
    public static function getInput()
    {
        return (object) [
            'value' => Input::get('value'),
            'image' => Input::file('value'),
        ];
    }

    public function group()
    {
        return $this->belongsTo('App\SettingGroup', 'group_id');
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function fetch($key)
    {
        if (Setting::cached($key)) {
            return Setting::$cache[$key];
        } else {
            $find = self::where('key', '=', $key)->first();

            if (!is_null($find)) {
                $value = $find->value;
                Setting::add($key, $value);

                return $value;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $key
     * @param $value
     */
    public static function add($key, $value)
    {
        Setting::$cache[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function cached($key)
    {
        return array_key_exists($key, Setting::$cache);
    }
}
