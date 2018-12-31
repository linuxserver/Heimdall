<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Request;

use Nexmo\Client\Response\ResponseInterface;

interface WrapResponseInterface
{
    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function wrapResponse(ResponseInterface $response);
}