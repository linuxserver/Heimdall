<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Verify;

class Check
{
    /**
     * Possible status of checking a code.
     */
    const VALID = 'VALID';
    const INVALID = 'INVALID';

    /**
     * @var array
     */
    protected $data;

    public function __construct(Array $data)
    {
        $this->data = $data;
    }

    public function getCode()
    {
        return $this->data['code'];
    }

    public function getDate()
    {
        return new \DateTime($this->data['date_received']);
    }

    public function getStatus()
    {
        return $this->data['status'];
    }

    public function getIpAddress()
    {
        return $this->data['ip_address'];
    }
}