<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\User;

class Item extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('user_id', function (Builder $builder) {
            $current_user = User::currentUser();
            $builder->where('user_id', $current_user->id);
        });
    }

    //
    protected $fillable = [
        'title', 'url', 'colour', 'icon', 'description', 'pinned', 'order', 'type', 'user_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Scope a query to only include pinned items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePinned($query)
    {
        return $query->where('pinned', 1);
    }

    public function getConfigAttribute()
    {
        $output = null;
        $view = null;
        if(isset($this->description) && !empty($this->description)){
            $output = json_decode($this->description);
            $output = is_object($output) ? $output : new \stdClass();
            if(isset($output->type) && !empty($output->type)) {
                $class = $output->type;
                $sap = new $class();
                $view = $sap->configDetails();
                $output->view = $view;
            }
            if(!isset($output->dataonly)) $output->dataonly = '0';

        }
        return (object)$output;
    }
    public static function checkConfig($config)
    {
        if(empty($config)) {
            $config = null;
        } else {
            $store = false;
            //die(var_dump($config));
            foreach($config as $key => $check) {
                if($key == 'type') continue;
                if($key == 'dataonly') continue;
                if(!empty($check) && $check != '0') {
                    $store = true;
                    break;
                }
            }
            //die(var_dump($store))

            $config['enabled'] = ($store) ? true : false;
            $config = json_encode($config);
        }
        return $config;

    }

    public function parents()
    {
        return $this->belongsToMany('App\Item', 'item_tag', 'item_id', 'tag_id');
    }
    public function children()
    {
        return $this->belongsToMany('App\Item', 'item_tag', 'tag_id', 'item_id');
    }

    public function getLinkAttribute()
    {
        if((int)$this->type === 1) {
            return '/tag/'.$this->url;
        } else {
            return $this->url;
        }
    }

    public function getDroppableAttribute()
    {
        if((int)$this->type === 1) {
            return ' droppable';
        } else {
            return '';
        }
    }

    public function getLinkTargetAttribute()
    {
        $target = Setting::fetch('window_target');

        if((int)$this->type === 1 || $target === 'current') {
            return '';
        } else {
            return ' target="' . $target . '"';
        }
    }

    public function getLinkIconAttribute()
    {
        if((int)$this->type === 1) {
            return 'fa-tag';
        } else {
            return 'fa-arrow-alt-to-right';
        }
    }
    public function getLinkTypeAttribute()
    {
        if((int)$this->type === 1) {
            return 'tags';
        } else {
            return 'items';
        }
    }

    public function scopeOfType($query, $type)
    {
        switch($type) {
            case 'item':
                $typeid = 0;
                break;
            case 'tag':
                $typeid = 1;
                break;
        }

        return $query->where('type', $typeid);
    }

    /**
     * Get the user that owns the item.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }   


}
