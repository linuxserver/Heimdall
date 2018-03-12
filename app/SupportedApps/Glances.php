<?php namespace App\SupportedApps;

class Glances implements Contracts\Applications {
    
    public function defaultColour()
    {
        return '#2c363f';
    }
    public function icon()
    {
        return 'supportedapps/glances.png';
    }
   
}