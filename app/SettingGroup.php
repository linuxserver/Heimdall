<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function settings()
    {
        return $this->hasMany('App\Setting', 'group_id');
    }
}
