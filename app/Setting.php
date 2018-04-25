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

    protected $fillable = [
        'id', 'group_id', 'key', 'type', 'options', 'label', 'value', 'order', 'system'
    ];

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
                    $value = '<a href="'.asset('storage/'.$this->value).'" title="'.__('app.settings.view').'" target="_blank">'.__('app.settings.view').'</a>';
                } else {
                    $value = __('app.options.none');
                }    
                break;
            case 'boolean':
                if((bool)$this->value === true) {
                    $value = __('app.options.yes');
                } else {
                    $value = __('app.options.no');
                }    
                break;
            case 'select':
                if(!empty($this->value) && $this->value !== 'none') {
                    $options =  (array)json_decode($this->options);
                    $value = __($options[$this->value]);
                } else {
                    $value = __('app.options.none');
                }                
                break;
            default:
                $value = __($this->value);
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
                    $value .= '<a class="setting-view-image" href="'.asset('storage/'.$this->value).'" title="'.__('app.settings.view').'" target="_blank"><img src="'.asset('storage/'.$this->value).'" /></a>';
                }
                $value .= Form::file('value', ['class' => 'form-control']);
                if(isset($this->value) && !empty($this->value)) {
                    $value .= '<a class="settinglink" href="'.route('settings.clear', $this->id).'" title="'.__('app.settings.remove').'">'.__('app.settings.reset').'</a>';
                }
                
                break;
            case 'boolean':
                $checked = false;
                if(isset($this->value) && (bool)$this->value === true) $checked = true;
                $set_checked = ($checked) ? ' checked="checked"' : '';
                $value = '
                <input type="hidden" name="value" value="0" />
                <label class="switch">
                    <input type="checkbox" name="value" value="1"'.$set_checked.' />
                    <span class="slider round"></span>
                </label>';

                break;
            case 'select':
                $options = json_decode($this->options);
                foreach($options as $key => $opt) {
                    $options->$key = __($opt);
                }
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
                    case 'startpage':
                        $url = 'https://www.startpage.com/';
                        $var = 'q';
                }
                $output .= '<div class="searchform">';
                $output .= Form::open(['url' => $url, 'method' => 'get']);
                $output .= '<div class="input-container">';
                $output .= Form::text($var, null, ['class' => 'homesearch', 'autofocus' => 'autofocus', 'placeholder' => __($name).' '.__('app.settings.search').'...']);
                $output .= '<button type="submit">'.ucwords(__('app.settings.search')).'</button>';
                $output .= '</div>';
                $output .= Form::close();
                $output .= '</div>';
            }
        }
        return $output;
    }
}
