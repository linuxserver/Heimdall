<?php namespace App\SupportedApps;

class Nzbget implements Contracts\Applications {
    
    public function defaultColour()
    {
        return '#124019';
    }
    public function icon()
    {
        return 'supportedapps/nzbget.png';
    }
    public function configDetails()
    {
        return 'nzbget';
    }
   
}