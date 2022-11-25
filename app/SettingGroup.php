<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * @return HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(\App\Setting::class, 'group_id');
    }
}
