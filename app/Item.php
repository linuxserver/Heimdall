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
            'NZBGet' => \App\SupportedApps\Nzbget::class,
            'pFsense' => \App\SupportedApps\Pfsense::class,
            'Pihole' => \App\SupportedApps\Pihole::class,
            'Plex' => \App\SupportedApps\Plex::class,
            'UniFi' => \App\SupportedApps\Unifi::class,
            'Portainer' => \App\SupportedApps\Portainer::class,
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
        if(isset($this->description) && !empty($this->description)){
            $output = json_decode($this->description);
            if(isset($output->type) && !empty($output->type)) {
                $class = $output->type;
                $sap = new $class();
                $view = $sap->configDetails();
            }
            $output->view = $view;
        }
        return (object)$output;
    }
}
