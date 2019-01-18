<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Request;

abstract class AbstractRequest implements RequestInterface
{
    protected $params = array();

    /**
     * @return array
     */
    public function getParams()
    {
        return array_filter($this->params, 'is_scalar');
    }
} 