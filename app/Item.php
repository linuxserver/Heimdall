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

    public function enhanced()
    {
        $details = $this->getconfig();
        $class = $details->type;
        $app = new $class;
        return (bool)($app instanceof \App\EnhancedApps);
    }

    public function getconfig()
    {
        $config = json_decode($this->description);

        $explode = explode('\\', $config->type);
        $config->name = end($explode);

        
        $config->url = $this->url;
        if(isset($config->override_url) && !empty($config->override_url)) {
            $config->url = $config->override_url;
        }
    
        return $config;
    }



    /**
     * Get the user that owns the item.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }   


}
