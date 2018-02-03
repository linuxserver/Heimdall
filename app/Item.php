<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\ClassLoader\ClassMapGenerator;



class Item extends Model
{
    //
    protected $fillable = [
        'title', 'url', 'colour', 'icon', 'description', 'pinned', 'order'
    ];

    public static function supportedList()
    {
        return [
            'NZBGet' => App\SupportedApps\Nzbget::class,
            'Plex' => App\SupportedApps\Plex::class,
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
}
