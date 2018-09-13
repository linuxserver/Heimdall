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
            'AirSonic' => \App\SupportedApps\AirSonic::class,
            'Cardigann' => \App\SupportedApps\Cardigann::class,
            'CouchPotato' => \App\SupportedApps\CouchPotato::class,
            'Bazarr' => \App\SupportedApps\Bazarr::class,
            'Booksonic' => \App\SupportedApps\Booksonic::class,
            'BookStack' => \App\SupportedApps\BookStack::class,
            'Deluge' => \App\SupportedApps\Deluge::class,
            'Dokuwiki' => \App\SupportedApps\Dokuwiki::class,
            'Duplicati' => \App\SupportedApps\Duplicati::class,
            'Emby' => \App\SupportedApps\Emby::class,
            'Flood' => \App\SupportedApps\Flood::class,
            'FreshRSS' => \App\SupportedApps\FreshRSS::class,
            'Gitea' => \App\SupportedApps\Gitea::class,
            'Glances' => \App\SupportedApps\Glances::class,
            'Grafana' => \App\SupportedApps\Grafana::class,
            'Graylog' => \App\SupportedApps\Graylog::class,
            'Headphones' => \App\SupportedApps\Headphones::class,
            'Home Assistant' => \App\SupportedApps\HomeAssistant::class,
            'Jackett' => \App\SupportedApps\Jackett::class,
            'Jdownloader' => \App\SupportedApps\Jdownloader::class,
            'Krusader' => \App\SupportedApps\Krusader::class,
            'LibreNMS' => \App\SupportedApps\LibreNMS::class,
            'Lidarr' => \App\SupportedApps\Lidarr::class,
            'Mcmyadmin' => \App\SupportedApps\Mcmyadmin::class,
            'Medusa' => \App\SupportedApps\Medusa::class,
            'MusicBrainz' => \App\SupportedApps\MusicBrainz::class,
            'Mylar' => \App\SupportedApps\Mylar::class,
            'NZBGet' => \App\SupportedApps\Nzbget::class,
            'Netdata' => \App\SupportedApps\Netdata::class,
            'Nextcloud' => \App\SupportedApps\Nextcloud::class,
            'Now Showing' => \App\SupportedApps\NowShowing::class,
            'Nzbhydra' => \App\SupportedApps\Nzbhydra::class,
            'OPNSense' => \App\SupportedApps\Opnsense::class,
            'Ombi' => \App\SupportedApps\Ombi::class,
            'Openhab' => \App\SupportedApps\Openhab::class,
            'OpenMediaVault' => \App\SupportedApps\OpenMediaVault::class,
            'Pihole' => \App\SupportedApps\Pihole::class,
            'Plex' => \App\SupportedApps\Plex::class,
            'Plexpy' => \App\SupportedApps\Plexpy::class,
            'Plexrequests' => \App\SupportedApps\Plexrequests::class,
            'Portainer' => \App\SupportedApps\Portainer::class,
            'Proxmox' => \App\SupportedApps\Proxmox::class,
            'Radarr' => \App\SupportedApps\Radarr::class,
            'Rancher' => \App\SupportedApps\Rancher::class,
            'Runeaudio' => \App\SupportedApps\Runeaudio::class,
            'Sabnzbd' => \App\SupportedApps\Sabnzbd::class,
            'Sickrage' => \App\SupportedApps\Sickrage::class,
            'Sonarr' => \App\SupportedApps\Sonarr::class,
            'Syncthing' => \App\SupportedApps\Syncthing::class,
            'Tautulli' => \App\SupportedApps\Tautulli::class,
            'Transmission' => \App\SupportedApps\Transmission::class,
            'Traefik' => \App\SupportedApps\Traefik::class,
            'tt-rss' => \App\SupportedApps\Ttrss::class,
            'TVheadend' => \App\SupportedApps\TVheadend::class,
            'UniFi' => \App\SupportedApps\Unifi::class,
            'unRAID' => \App\SupportedApps\Unraid::class,
            'pfSense' => \App\SupportedApps\Pfsense::class,
            'pyLoad' => \App\SupportedApps\pyLoad::class,
            'ruTorrent' => \App\SupportedApps\ruTorrent::class,
            'Watcher3' => \App\SupportedApps\Watcher3::class,
            'WebTools' => \App\SupportedApps\WebTools::class,
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


}
