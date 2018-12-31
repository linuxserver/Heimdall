<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2017 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;
use Nexmo\Call\Collection;
use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\JsonSerializableInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;

/**
 * Lightweight resource, only has put / delete.
 */
class Talk implements JsonSerializableInterface, ClientAwareInterface, \ArrayAccess
{
    use ClientAwareTrait;

    protected $id;

    protected $data = [];

    protected $params= [
        'text',
        'voice_name',
        'loop'
    ];

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function __invoke(self $entity = null)
    {
        if(is_null($entity)){
            return $this;
        }

        return $this->put($entity);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setText($text)
    {
        $this->data['text'] = (string) $text;
    }

    public function setVoiceName($name)
    {
        $this->data['voice_name'] = (string) $name;
    }

    public function setLoop($times)
    {
        $this->data['loop'] = (int) $times;
    }

    public function put($talk = null)
    {
        if(!$talk){
            $talk = $this;
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId() . '/talk',
            'PUT',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($talk));
        $response = $this->client->send($request);
        return $this->parseEventResponse($response);
    }

    public function delete()
    {
        $request = new Request(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId() . '/talk',
            'DELETE'
        );

        $response = $this->client->send($request);
        return $this->parseEventResponse($response);
    }

    protected function parseEventResponse(ResponseInterface $response)
    {
        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $json = json_decode($response->getBody()->getContents(), true);

        if(!$json){
            throw new Exception\Exception('Unexpected Response Body Format');
        }

        return new Event($json);
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        if($status >= 400 AND $status < 500) {
            $e = new Exception\Request($body['error_title'], $status);
        } elseif($status >= 500 AND $status < 600) {
            $e = new Exception\Server($body['error_title'], $status);
        } else {
            $e = new Exception\Exception('Unexpected HTTP Status Code');
            throw $e;
        }

        return $e;
    }

    function jsonSerialize()
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if(!in_array($offset, $this->params)){
            throw new \RuntimeException('invalid parameter: ' . $offset);
        }

        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if(!in_array($offset, $this->params)){
            throw new \RuntimeException('invalid parameter: ' . $offset);
        }

        unset($this->data[$offset]);
    }
}
