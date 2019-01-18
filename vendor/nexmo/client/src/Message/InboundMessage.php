<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Message;

use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\Psr7Trait;
use Psr\Http\Message\ServerRequestInterface;

class InboundMessage implements MessageInterface, \ArrayAccess
{
    use Psr7Trait;
    use JsonResponseTrait;
    use CollectionTrait;

    protected $id;

    /**
     * InboundMessage constructor.
     * @param string|ServerRequestInterface $idOrRequest Message ID, or inbound HTTP request.
     */
    public function __construct($idOrRequest)
    {
        if($idOrRequest instanceof ServerRequestInterface){
            $this->setRequest($idOrRequest);
            return;
        }

        if(is_string($idOrRequest)){
            $this->id = $idOrRequest;
            return;
        }

        throw new \RuntimeException(sprintf(
            '`%s` must be constructed with a server request or a message id',
            self::class
        ));
    }

    public static function createFromGlobals()
    {
        $serverRequest = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
        return new self($serverRequest);
    }

    /**
     * Create a matching reply to the inbound message. Currently only supports text replies.
     *
     * @param string $body
     * @return Text
     */
    public function createReply($body)
    {
        return new Text($this->getFrom(), $this->getTo(), $body);
    }

    public function getRequestData($sent = true)
    {
        $request = $this->getRequest();

        if(is_null($request)){
            return [];
        }

        if(!($request instanceof ServerRequestInterface)){
            throw new \RuntimeException('inbound message request should only ever be `' . ServerRequestInterface::class . '`');
        }

        // Check our incoming content type
        $isApplicationJson = false;
        $contentTypes = $request->getHeader('Content-Type');
        // We only respect application/json if it's the first entry without any preference weighting
        // as that's what Nexmo send
        if (count($contentTypes) && $contentTypes[0] === 'application/json') {
            $isApplicationJson = true;
        }

        switch($request->getMethod()){
            case 'POST':
                $params = $isApplicationJson ? json_decode((string)$request->getBody(), true) : $request->getParsedBody();
                break;
            case 'GET':
                $params = $request->getQueryParams();
                break;
            default:
                $params = [];
                break;
        }

        return $params;
    }

    public function getFrom()
    {
        if($this->getRequest()){
            return $this['msisdn'];
        } else {
            return $this['from'];
        }
    }

    public function getTo()
    {
        return $this['to'];
    }

    public function getMessageId()
    {
        if(isset($this->id)){
            return $this->id;
        }

        return $this['messageId'];
    }

    public function isValid()
    {
        return (bool) $this->getMessageId();
    }

    public function getBody()
    {
        if($this->getRequest()){
            return $this['text'];
        } else {
            return $this['body'];
        }
    }

    public function getType()
    {
        return $this['type'];
    }

    public function getAccountId()
    {
        return $this['account-id'];
    }

    public function getNetwork()
    {
        return $this['network'];
    }

    /**
     * Allow the object to access the data from the API response, a sent API request, or the user set data that the
     * request will be created from - in that order.
     *
     * @param mixed $offset
     * @return bool
     * @throws \Exception
     */
    public function offsetExists($offset)
    {
        $response = $this->getResponseData();

        if(isset($this->index)){
            $response = $response['items'][$this->index];
        }

        $request  = $this->getRequestData();
        $dirty    = $this->getRequestData(false);
        return isset($response[$offset]) || isset($request[$offset]) || isset($dirty[$offset]);
    }

    /**
     * Allow the object to access the data from the API response, a sent API request, or the user set data that the
     * request will be created from - in that order.
     *
     * @param mixed $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        $response = $this->getResponseData();

        if(isset($this->index)){
            $response = $response['items'][$this->index];
        }

        $request  = $this->getRequestData();
        $dirty    = $this->getRequestData(false);

        if(isset($response[$offset])){
            return $response[$offset];
        }

        if(isset($request[$offset])){
            return $request[$offset];
        }

        if(isset($dirty[$offset])){
            return $dirty[$offset];
        }
    }

    /**
     * All properties are read only.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw $this->getReadOnlyException($offset);
    }

    /**
     * All properties are read only.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw $this->getReadOnlyException($offset);
    }

    /**
     * All properties are read only.
     *
     * @param $offset
     * @return \RuntimeException
     */
    protected function getReadOnlyException($offset)
    {
        return new \RuntimeException(sprintf(
            'can not modify `%s` using array access',
            $offset
        ));
    }
}