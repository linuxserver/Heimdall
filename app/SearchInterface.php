<?php

namespace App;

interface SearchInterface
{
    public function getResults($query, $providerdetails);
}
