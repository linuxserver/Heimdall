<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Form;

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

    public function getListValueAttribute()
    {
        switch($this->type) {
            case 'image':
                if(!empty($this->value)) {
                    $value = '<a href="'.asset('storage/'.$this->value).'" title="View" target="_blank">View</a>';
                } else {
                    $value = '- not set -';
                }    
                break;
            case 'boolean':
                if((bool)$this->value === true) {
                    $value = 'Yes';
                } else {
                    $value = 'No';
                }    
                break;
            case 'select':
                if(!empty($this->value) && $this->value !== 'none') {
                    $options =  (array)json_decode($this->options);
                    $value = $options[$this->value];
                } else {
                    $value = '- not set -';
                }                
                break;
            default:
                $value = $this->value;
                break;
        }

        return $value;

    }

    public function getEditValueAttribute()
    {
        switch($this->type) {
            case 'image':
                $value = '';
                if(isset($this->value) && !empty($this->value)) {
                    $value .= '<a class="setting-view-image" href="'.asset('storage/'.$this->value).'" title="View" target="_blank"><img src="'.asset('storage/'.$this->value).'" /></a>';
                }
                $value .= Form::file('value', ['class' => 'form-control']);
                if(isset($this->value) && !empty($this->value)) {
                    $value .= '<a class="settinglink" href="'.route('settings.clear', $this->id).'" title="Remove">Reset back to default</a>';
                }
                
                break;
            case 'boolean':
                $checked = false;
                if(isset($this->value) && (bool)$this->value === true) $checked = true;
                $set_checked = ($checked) ? ' checked="checked"' : '';
                $value = '
                <label class="switch">
                    <input type="hidden" name="value" value="0" />
                    <input type="checkbox" name="value" value="1"'.$set_checked.' />
                    <span class="slider round"></span>
                </label>';

                break;
            case 'select':
                $options = json_decode($this->options);
                $value = Form::select('value', $options, null, ['class' => 'form-control']);
                break;
            default:
                $value = Form::text('value', null, ['class' => 'form-control']);
                break;
        }

        return $value;

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

    /**
     * @return html
     */
    public static function search()
    {
        $output = '';
        $homepage_search = self::fetch('homepage_search');
        $search_provider = self::where('key', '=', 'search_provider')->first();
        
        //die(var_dump($search_provider->value));
        // return early if search isn't applicable
        if((bool)$homepage_search !== true) return $output;
        if($search_provider->value === 'none') return $output;
        if(empty($search_provider->value)) return $output;
        if(is_null($search_provider->value)) return $output;


        if((bool)$homepage_search && (bool)$search_provider) {

            $options = (array)json_decode($search_provider->options);
            $name = $options[$search_provider->value];
            if((bool)$search_provider->value) {
                switch($search_provider->value) {
                    case 'google':
                        $url = 'https://www.google.com/search';
                        $var = 'q';
                        break;
                    case 'ddg':
                        $url = 'https://duckduckgo.com/';
                        $var = 'q';
                        break;
                    case 'bing':
                        $url = 'https://www.bing.com/search';
                        $var = 'q';
                        break;
                }
                $output .= '<div class="searchform">';
                $output .= Form::open(['url' => $url, 'method' => 'get']);
                $output .= '<div class="input-container">';
                $output .= Form::text($var, null, ['class' => 'homesearch', 'placeholder' => $name.' search...']);
                $output .= '<button type="submit">Search</button>';
                $output .= '</div>';
                $output .= Form::close();
                $output .= '</div>';
            }
        }
        return $output;
    }
}
