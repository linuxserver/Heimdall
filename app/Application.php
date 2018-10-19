<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    //
    public function icon()
    {
        $path = public_path('storage/apps/'.$this->icon);
        if(!file_exists($path)) {
            Storage::putFileAs('apps', new File(app_path('Apps/'.$this->name.'/'.$this->icon)), $this->icon);
        }
        return asset('storage/apps/'.$this->icon);
    }

    public function defaultColour()
    {
        // check if light or dark
        if($this->tile_background == 'light') return '#fafbfc';
        return '#161b1f';
    }

    public function class()
    {
        $class = '\App\SupportedApps\\'.$this->name;
        return $class;
    }
}
