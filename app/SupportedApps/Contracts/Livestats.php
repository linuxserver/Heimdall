<?php namespace App\SupportedApps\Contracts;

interface Livestats {

    public function configDetails();

    public function testConfig();

    public function executeConfig();
    
}