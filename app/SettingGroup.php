<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\SettingGroup
 *
 * @property int $id
 * @property string $title
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Setting[] $settings
 * @property-read int|null $settings_count
 * @method static \Illuminate\Database\Eloquent\Builder|SettingGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingGroup whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingGroup whereTitle($value)
 * @mixin \Eloquent
 */
class SettingGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'setting_groups';

    /**
     * Tell the Model this Table doesn't support timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    public function settings(): HasMany
    {
        return $this->hasMany(\App\Setting::class, 'group_id');
    }
}
