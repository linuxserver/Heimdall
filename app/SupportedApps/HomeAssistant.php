<?php namespace App\SupportedApps;

class HomeAssistant implements Contracts\Applications {
    public function defaultColour()
    {
        return '#073c52';
    }
    public function icon()
    {
        return 'supportedapps/homeassistant.png';
    }
}