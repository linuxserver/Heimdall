<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;


interface EntityInterface
{
    public function getRequest();

    public function getRequestData($sent = true);

    public function getResponse();

    public function getResponseData();

    public function setResponse(\Psr\Http\Message\ResponseInterface $response);

    public function setRequest(\Psr\Http\Message\RequestInterface $request);

}