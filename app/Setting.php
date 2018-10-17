<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Form;
use Illuminate\Support\Facades\Auth;
use App\User;

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
        if((bool)$this->system === true) {
            $value = self::_fetch($this->key);
        } else {
            $value = self::fetch($this->key);
        }
        $this->value = $value;
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
        if((bool)$this->system === true) {
            $value = self::_fetch($this->key);
        } else {
            $value = self::fetch($this->key);
        }
        $this->value = $value;
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
        $user = self::user();
        return self::_fetch($key, $user);
    }
    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function _fetch($key, $user=null)
    {
        #$cachekey = ($user === null) ? $key : $key.'-'.$user->id;
        #if (Setting::cached($cachekey)) {
        #    return Setting::$cache[$cachekey];
        #} else {
            $find = self::where('key', '=', $key)->first();

            if (!is_null($find)) {
                if((bool)$find->system === true) { // if system variable use global value
                    $value = $find->value;
                } else { // not system variable so use user specific value
                    // check if user specified value has been set
                    //print_r($user);
                    $usersetting = $user->settings()->where('id', $find->id)->first();
                    //print_r($user->settings);
                    //die(var_dump($usersetting));
                    //->pivot->value;
                    //echo "user: ".$user->id." --- ".$usersettings;
                    if(isset($usersetting) && !empty($usersetting)) {
                        $value = $usersetting->pivot->uservalue;
                    } else { // if not get default from base setting
                        //$user->settings()->save($find, ['value' => $find->value]);
                        #$has_setting = $user->settings()->where('id', $find->id)->exists();
                        #if($has_setting) {
                        #    $user->settings()->updateExistingPivot($find->id, ['uservalue' => (string)$find->value]);
                        #} else {
                        #    $user->settings()->save($find, ['uservalue' => (string)$find->value]);
                        #}
                        $value = $find->value;
                    }
                    
                }
                #Setting::add($cachekey, $value);

                return $value;
            } else {
                return false;
            }
        #}
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
        $user_search_provider = self::fetch('search_provider');
        //die(print_r($search_provider));
       
        //die(var_dump($user_search_provider));
        // return early if search isn't applicable
        if((bool)$homepage_search !== true) return $output;
        if($user_search_provider === 'none') return $output;
        if(empty($user_search_provider)) return $output;
        if(is_null($user_search_provider)) return $output;


        if((bool)$homepage_search && (bool)$search_provider) {

            $options = (array)json_decode($search_provider->options);
            $name = $options[$user_search_provider];
            if((bool)$user_search_provider) {
                switch($user_search_provider) {
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

    /**
     * The users that belong to the setting.
     */
    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\SettingUser')->withPivot('uservalue');
    }

    public static function user()
    {
        return User::currentUser();
    }


}
