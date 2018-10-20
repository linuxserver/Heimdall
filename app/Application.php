<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    //
    public function icon()
    {
        return asset($this->icon);
    }

    public function defaultColour()
    {
        // check if light or dark
        if($this->tile_background == 'light') return '#fafbfc';
        return '#161b1f';
    }

    public function class()
    {
        $class = '\App\SupportedApps\\'.$this->name.'\\'.$this->name;
        return $class;
    }
}
