<?php

namespace App;

use Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Input;

/**
 * App\Setting
 *
 * @mixin Builder
 * @property int $id
 * @property int $group_id
 * @property string $key
 * @property string $type
 * @property string|null $options
 * @property string $label
 * @property string|null $value
 * @property string $order
 * @property int $system
 * @property-read mixed $edit_value
 * @property-read mixed $list_value
 * @property-read \App\SettingGroup|null $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereGroupId($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereKey($value)
 * @method static Builder|Setting whereLabel($value)
 * @method static Builder|Setting whereOptions($value)
 * @method static Builder|Setting whereOrder($value)
 * @method static Builder|Setting whereSystem($value)
 * @method static Builder|Setting whereType($value)
 * @method static Builder|Setting whereValue($value)
 */
class Setting extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    protected $fillable = [
        'id', 'group_id', 'key', 'type', 'options', 'label', 'value', 'order', 'system',
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

    public static function getInput(Request $request): object
    {
        return (object) [
            'value' => $request->input('value'),
            'image' => $request->file('value'),
        ];
    }

    public function getListValueAttribute()
    {
        if ((bool) $this->system === true) {
            $value = self::_fetch($this->key);
        } else {
            $value = self::fetch($this->key);
        }
        $this->value = $value;
        switch ($this->type) {
            case 'image':
                if (! empty($this->value)) {
                    $value = '<a href="'.asset('storage/'.$this->value).'" title="'.
                        __('app.settings.view').
                        '" target="_blank">'.
                        __('app.settings.view').
                        '</a>';
                } else {
                    $value = __('app.options.none');
                }
                break;
            case 'boolean':
                if ((bool) $this->value === true) {
                    $value = __('app.options.yes');
                } else {
                    $value = __('app.options.no');
                }
                break;
            case 'select':
                if (! empty($this->value) && $this->value !== 'none') {
                    $options = (array) json_decode($this->options);
                    if ($this->key === 'search_provider') {
                        $options = Search::providers()->pluck('name', 'id')->toArray();
                    }
                    $value = (array_key_exists($this->value, $options))
                        ? __($options[$this->value])
                        : __('app.options.none');
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
        if ((bool) $this->system === true) {
            $value = self::_fetch($this->key);
        } else {
            $value = self::fetch($this->key);
        }
        $this->value = $value;
        switch ($this->type) {
            case 'image':
                $value = '';
                if (isset($this->value) && ! empty($this->value)) {
                    $value .= '<a class="setting-view-image" href="'.
                        asset('storage/'.$this->value).
                        '" title="'.
                        __('app.settings.view').
                        '" target="_blank"><img src="'.
                        asset('storage/'.
                            $this->value).
                        '" /></a>';
                }
                $value .= Form::file('value', ['class' => 'form-control']);
                if (isset($this->value) && ! empty($this->value)) {
                    $value .= '<a class="settinglink" href="'.
                        route('settings.clear', $this->id).
                        '" title="'.
                        __('app.settings.remove').
                        '">'.
                        __('app.settings.reset').
                        '</a>';
                }

                break;
            case 'boolean':
                $checked = false;
                if (isset($this->value) && (bool) $this->value === true) {
                    $checked = true;
                }
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
                if ($this->key === 'search_provider') {
                    $options = Search::providers()->pluck('name', 'id');
                }
                foreach ($options as $key => $opt) {
                    $options->$key = __($opt);
                }
                $value = Form::select('value', $options, null, ['class' => 'form-control']);
                break;
            case 'textarea':
                $value = Form::textarea('value', null, ['class' => 'form-control', 'cols' => '44', 'rows' => '15']);
                break;
            default:
                $value = Form::text('value', null, ['class' => 'form-control']);
                break;
        }

        return $value;
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(\App\SettingGroup::class, 'group_id');
    }

    /**
     *
     * @return mixed
     */
    public static function fetch(string $key)
    {
        $user = self::user();

        return self::_fetch($key, $user);
    }

    // @codingStandardsIgnoreStart
    /**
     *
     * @return mixed
     */
    public static function _fetch(string $key, $user = null)
    {
        // @codingStandardsIgnoreEnd
        //$cachekey = ($user === null) ? $key : $key.'-'.$user->id;
        //if (Setting::cached($cachekey)) {
        //    return Setting::$cache[$cachekey];
        //} else {
        $find = self::where('key', '=', $key)->first();

        if (! is_null($find)) {
            if ((bool) $find->system === true) { // if system variable use global value
                $value = $find->value;
            } else { // not system variable so use user specific value
                // check if user specified value has been set
                //print_r($user);
                $usersetting = $user->settings()->where('id', $find->id)->first();
                //print_r($user->settings);
                //die(var_dump($usersetting));
                //->pivot->value;
                //echo "user: ".$user->id." --- ".$usersettings;
                if (isset($usersetting) && ! empty($usersetting)) {
                    $value = $usersetting->pivot->uservalue;
                } else { // if not get default from base setting
                    //$user->settings()->save($find, ['value' => $find->value]);
                    //$has_setting = $user->settings()->where('id', $find->id)->exists();
                    //if($has_setting) {
                    //    $user->settings()->updateExistingPivot($find->id, ['uservalue' => (string)$find->value]);
                    //} else {
                    //    $user->settings()->save($find, ['uservalue' => (string)$find->value]);
                    //}
                    $value = $find->value;
                }
            }
            //Setting::add($cachekey, $value);

            return $value;
        } else {
            return false;
        }
        //}
    }

    /**
     * @param $value
     */
    public static function add(string $key, $value)
    {
        self::$cache[$key] = $value;
    }

    public static function cached(string $key): bool
    {
        return array_key_exists($key, self::$cache);
    }

    /**
     * The users that belong to the setting.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\User::class)->using(\App\SettingUser::class)->withPivot('uservalue');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|SessionManager|Store|mixed
     */
    public static function user()
    {
        return User::currentUser();
    }
}
