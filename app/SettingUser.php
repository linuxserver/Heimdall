<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\SettingUser
 *
 * @property int $setting_id
 * @property int $user_id
 * @property string|null $uservalue
 * @method static \Illuminate\Database\Eloquent\Builder|SettingUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingUser whereSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingUser whereUservalue($value)
 * @mixin \Eloquent
 */
class SettingUser extends Pivot
{
    //
}
