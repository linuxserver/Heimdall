<?php namespace App\SupportedApps;

class LazyLibrarian implements Contracts\Applications {
    public function defaultColour()
    {
        return '#a50';
    }
    public function icon()
    {
        return 'supportedapps/lazylibrarian.png';
    }
}
