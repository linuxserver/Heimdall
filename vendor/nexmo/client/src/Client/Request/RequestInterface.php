<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */
namespace Nexmo\Client\Request;

interface RequestInterface
{
    /**
     * @return array
     */
    public function getParams();

    /**
     * @return string
     */
    public function getURI();
}