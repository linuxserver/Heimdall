<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

interface SearchInterface
{
    public function getResults($query, $providerdetails);
}
