<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([

    ])
    ->setFinder($finder)
;

return $config;
