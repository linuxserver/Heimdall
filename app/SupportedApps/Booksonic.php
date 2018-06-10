<?php namespace App\SupportedApps;

class Booksonic implements Contracts\Applications {
    public function defaultColour()
    {
        return '#58a';
    }
    public function icon()
    {
        return 'supportedapps/booksonic.png';
    }
}
