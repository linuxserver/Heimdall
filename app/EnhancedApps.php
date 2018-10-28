<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

interface EnhancedApps
{
    public function test();
    public function livestats();
    public function url($endpoint);

}