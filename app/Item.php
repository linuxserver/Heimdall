<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;


class Item extends Model
{
    use SoftDeletes;

    //
    protected $fillable = [
        'title', 'url', 'colour', 'icon', 'description', 'pinned', 'order', 'type'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public static function supportedList()
    {
        return [
            'Deluge' => \App\SupportedApps\Deluge::class,
            'Duplicati' => \App\SupportedApps\Duplicati::class,
            'Emby' => \App\SupportedApps\Emby::class,
            'Graylog' => \App\SupportedApps\Graylog::class,
            'Home Assistant' => \App\SupportedApps\HomeAssistant::class,
            'Jackett' => \App\SupportedApps\Jackett::class,
            'Jdownloader' => \App\SupportedApps\Jdownloader::class,
            'Lidarr' => \App\SupportedApps\Lidarr::class,
            'Mcmyadmin' => \App\SupportedApps\Mcmyadmin::class,
            'Medusa' => \App\SupportedApps\Medusa::class,
            'NZBGet' => \App\SupportedApps\Nzbget::class,
            'Netdata' => \App\SupportedApps\Netdata::class,
            'Nextcloud' => \App\SupportedApps\Nextcloud::class,
            'Nzbhydra' => \App\SupportedApps\Nzbhydra::class,
            'Ttrss' => \App\SupportedApps\Ttrss::class,
            'Ombi' => \App\SupportedApps\Ombi::class,
            'OPNSense' => \App\SupportedApps\Opnsense::class,
            'Openhab' => \App\SupportedApps\Openhab::class,
            'Pihole' => \App\SupportedApps\Pihole::class,
            'Plex' => \App\SupportedApps\Plex::class,
            'Plexpy' => \App\SupportedApps\Plexpy::class,
            'Plexrequests' => \App\SupportedApps\Plexrequests::class,
            'Portainer' => \App\SupportedApps\Portainer::class,
            'Proxmox' => \App\SupportedApps\Proxmox::class,
            'Radarr' => \App\SupportedApps\Radarr::class,
            'Sabnzbd' => \App\SupportedApps\Sabnzbd::class,
            'Sonarr' => \App\SupportedApps\Sonarr::class,
            'Traefik' => \App\SupportedApps\Traefik::class,
            'UniFi' => \App\SupportedApps\Unifi::class,
            'pFsense' => \App\SupportedApps\Pfsense::class,
            'ruTorrent' => \App\SupportedApps\ruTorrent::class,
        ];
    }
    public static function supportedOptions()
    {
        return array_keys(self::supportedList());
    }

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

    public function getTargetAttribute()
    {
        if((int)$this->type === 1) {
            return '';
        } else {
            return ' target="_blank"';
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


}
