<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

interface SearchInterface
{
    public function getResults($query, $providerdetails);

}