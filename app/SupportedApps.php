<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

abstract class SupportedApps
{
    public $config;

    public function test($url, $requiresLoginFirst=false)
    {

    }

    public function execute($url, $requiresLoginFirst=false)
    {
        
    }

    public function login()
    {

    }

    public function apiRequest($url)
    {

    }

}