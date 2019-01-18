<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;

use Psr\Http\Message\ResponseInterface;

trait JsonResponseTrait
{
    protected $responseJson;

    public function getResponseData()
    {
        if(!($this instanceof EntityInterface)){
            throw new \Exception(sprintf(
                '%s can only be used if the class implements %s',
                __TRAIT__,
                EntityInterface::class
            ));
        }

        if(($response = $this->getResponse()) && ($response instanceof ResponseInterface)){
            if($response->getBody()->isSeekable()){
                $response->getBody()->rewind();
            }

            $body = $response->getBody()->getContents();
            $this->responseJson = json_decode($body, true);
            return $this->responseJson;
        }

        return [];
    }
}
