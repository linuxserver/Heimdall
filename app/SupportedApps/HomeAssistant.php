<?php namespace App\SupportedApps;

class HomeAssistant implements Contracts\Applications {
    public function defaultColour()
    {
        return '#28D';
    }
    public function icon()
    {
        return 'supportedapps/homeassistant.png';
    }
}