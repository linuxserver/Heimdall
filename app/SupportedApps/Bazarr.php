<?php namespace App\SupportedApps;

class Bazarr implements Contracts\Applications {
    
    public function defaultColour()
    {
        return '#222';
    }
    public function icon()
    {
        return 'supportedapps/bazarr.png';
    }
   
}
