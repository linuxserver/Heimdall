<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\User;
use App\ItemTag;
use App\Application;

class Item extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('user_id', function (Builder $builder) {
            $current_user = User::currentUser();
            if($current_user) {
                $builder->where('user_id', $current_user->id)->orWhere('user_id', 0);
            } else {
                $builder->where('user_id', 0);
            }
        });
    }

    //
    protected $fillable = [
        'title', 'url', 'colour', 'icon', 'description', 'pinned', 'order', 'type', 'class', 'user_id'
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


    public function tags()
    {
        $id = $this->id;
        $tags = ItemTag::select('tag_id')->where('item_id', $id)->pluck('tag_id')->toArray();
        $tagdetails = Item::select('id', 'title', 'url', 'pinned')->whereIn('id', $tags)->get();
        //print_r($tags);
        if(in_array(0, $tags)) {
            $details = new Item([
                "id" => 0,
                "title" => __('app.dashboard'),
                "url" => '',
                "pinned" => 0
            ]);
            $tagdetails->prepend($details);
        }
        return $tagdetails;
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

    public static function nameFromClass($class)
    {
        $explode = explode('\\', $class);
        $name = end($explode);
        
        return $name;
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
        if(isset($this->class) && !empty($this->class)) {
            $app = new $this->class;
        } else {
            return false;
        }
        return (bool)($app instanceof \App\EnhancedApps);
    }

    public static function isEnhanced($class)
    {
        if($class === null || $class === 'null') return false;
        $app = new $class;
        return (bool)($app instanceof \App\EnhancedApps);
    }

    public static function isSearchProvider($class)
    {
        $app = new $class;
        return ((bool)($app instanceof \App\SearchInterface)) ? $app : false;
    }

    public function enabled()
    {
        if($this->enhanced()) {
            $config = $this->getconfig();
            if($config) {
                return (bool) $config->enabled;
            }
        }
        return false;
    }

    public function getconfig()
    {
        $explode = explode('\\', $this->class);
        

        if(!isset($this->description) || empty($this->description)) {
            $config = new \stdClass;
            $config->name = end($explode);
            $config->enabled = false;
            return $config;
        }

        

        $config = json_decode($this->description);

        $config->name = end($explode);

        
        $config->url = $this->url;
        if(isset($config->override_url) && !empty($config->override_url)) {
            $config->url = $config->override_url;
        }
    
        return $config;
    }

    public static function applicationDetails($class)
    {
        if(!empty($class)) {
            $name = self::nameFromClass($class);
            $application = Application::where('name', $name)->first();
            if($application) return $application;
        }

        return false;

    }

    public static function getApplicationDescription($class)
    {
        $details = self::applicationDetails($class);
        if($details !== false) {
            return $details->description.' - '.$details->license;
        }
        return '';
    }

    /**
     * Get the user that owns the item.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }   


}
