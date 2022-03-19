<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

interface EnhancedApps
{
    public function test();

    public function livestats();

    public function url($endpoint);
}
