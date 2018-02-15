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
        'title', 'url', 'colour', 'icon', 'description', 'pinned', 'order'
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
            'Duplicati' => \App\SupportedApps\Duplicati::class,
            'Emby' => \App\SupportedApps\Emby::class,
            'Home Assistant' => \App\SupportedApps\HomeAssistant::class,
            'Jackett' => \App\SupportedApps\Jackett::class,
            'Jdownloader' => \App\SupportedApps\Jdownloader::class,
            'Lidarr' => \App\SupportedApps\Lidarr::class,
            'Mcmyadmin' => \App\SupportedApps\Mcmyadmin::class,
            'Nextcloud' => \App\SupportedApps\Nextcloud::class,
            'NZBGet' => \App\SupportedApps\Nzbget::class,
            'Openhab' => \App\SupportedApps\Openhab::class,
            'pFsense' => \App\SupportedApps\Pfsense::class,
            'Netdata' => \App\SupportedApps\Netdata::class,
            'OPNSense' => \App\SupportedApps\Opnsense::class,
            'Pihole' => \App\SupportedApps\Pihole::class,
            'Plex' => \App\SupportedApps\Plex::class,
            'Plexpy' => \App\SupportedApps\Plexpy::class,
            'Plexrequests' => \App\SupportedApps\Plexrequests::class,
            'Portainer' => \App\SupportedApps\Portainer::class,
            'Proxmox' => \App\SupportedApps\Proxmox::class,
            'Radarr' => \App\SupportedApps\Radarr::class,
            'ruTorrent' => \App\SupportedApps\ruTorrent::class,
            'Sabnzbd' => \App\SupportedApps\Sabnzbd::class,
            'Sonarr' => \App\SupportedApps\Sonarr::class,
            'Traefik' => \App\SupportedApps\Traefik::class,
            'UniFi' => \App\SupportedApps\Unifi::class,
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
}
