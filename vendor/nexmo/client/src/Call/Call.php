<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Conversations\Conversation;
use Nexmo\Entity\EntityInterface;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\JsonUnserializableInterface;
use Nexmo\Entity\NoRequestResponseTrait;
use Psr\Http\Message\ResponseInterface;
use Nexmo\Client\Exception;
use Zend\Diactoros\Request;

/**
 * Class Call
 *
 * @property \Nexmo\Call\Stream $stream
 * @property \Nexmo\Call\Talk   $talk
 * @property \Nexmo\Call\Dtmf   $dtmf
 *
 * @method \Nexmo\Call\Stream stream()
 * @method \Nexmo\Call\Talk   talk()
 * @method \Nexmo\Call\Dtmf   dtmf()
 */
class Call implements EntityInterface, \JsonSerializable, JsonUnserializableInterface, ClientAwareInterface
{
    use NoRequestResponseTrait;
    use JsonSerializableTrait;
    use JsonResponseTrait;
    use ClientAwareTrait;

    const WEBHOOK_ANSWER = 'answer';
    const WEBHOOK_EVENT  = 'event';

    const TIMER_LENGTH  = 'length';
    const TIMER_RINGING = 'ringing';

    const TIMEOUT_MACHINE = 'machine';

    protected $id;

    protected $to;

    protected $from;

    /**
     * @var Webhook[]
     */
    protected $webhooks = [];

    protected $data = [];

    protected $subresources = [];

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function get()
    {
        $request = new Request(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId()
            ,'GET'
        );

        $response = $this->getClient()->send($request);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $this->jsonUnserialize($data);

        return $this;
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
        }

        return $e;
    }

    public function put($payload)
    {
        $request = new Request(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId()
            ,'PUT',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($payload));
        $response = $this->client->send($request);

        $responseCode = $response->getStatusCode();
        if($responseCode != '200' && $responseCode != '204'){
            throw $this->getException($response);
        }

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTo($endpoint)
    {
        if(!($endpoint instanceof Endpoint)){
            $endpoint = new Endpoint($endpoint);
        }

        $this->to = $endpoint;
        return $this;
    }

    /**
     * @return Endpoint
     */
    public function getTo()
    {
        if($this->lazyLoad()){
            return new Endpoint($this->data['to']['number'], $this->data['to']['type']);
        }

        return $this->to;
   }

    public function setFrom($endpoint)
    {
        if(!($endpoint instanceof Endpoint)){
            $endpoint = new Endpoint($endpoint);
        }

        $this->from = $endpoint;
        return $this;
    }

    /**
     * @return Endpoint
     */
    public function getFrom()
    {
        if($this->lazyLoad()){
            return new Endpoint($this->data['from']['number'], $this->data['from']['type']);
        }

        return $this->from;
    }

    public function setWebhook($type, $url = null, $method = null)
    {
        if($type instanceof Webhook){
            $this->webhooks[$type->getType()] = $type;
            return $this;
        }

        if(is_null($url)){
            throw new \InvalidArgumentException('must provide `Nexmo\Call\Webhook` object, or a type and url: missing url' );
        }

        $this->webhooks[$type] = new Webhook($type, $url, $method);
        return $this;
    }

    public function setTimer($type, $length)
    {
        $this->data[$type . '_timer'] = $length;
    }

    public function setTimeout($type, $length)
    {
        $this->data[$type . '_timeout'] = $length;
    }

    public function getStatus()
    {
        if($this->lazyLoad()){
            return $this->data['status'];
        }
    }

    public function getDirection()
    {
        if($this->lazyLoad()){
            return $this->data['direction'];
        }
    }

    public function getConversation()
    {
        if($this->lazyLoad()){
            return new Conversation($this->data['conversation_uuid']);
        }
    }

    /**
     * Returns true if the resource data is loaded.
     *
     * Will attempt to load the data if it's not already.
     *
     * @return bool
     */
    protected function lazyLoad()
    {
        if(!empty($this->data)){
            return true;
        }

        if(isset($this->id)){
            $this->get($this);
            return true;
        }

        return false;
    }

    public function __get($name)
    {
        switch($name){
            case 'stream':
            case 'talk':
            case 'dtmf':
                return $this->lazySubresource(ucfirst($name));
            default:
                throw new \RuntimeException('property does not exist: ' . $name);
        }
    }

    public function __call($name, $arguments)
    {
        switch($name){
            case 'stream':
            case 'talk':
            case 'dtmf':
                $entity = $this->lazySubresource(ucfirst($name));
                return call_user_func_array($entity, $arguments);
            default:
                throw new \RuntimeException('method does not exist: ' . $name);
        }
    }

    protected function lazySubresource($type)
    {
        if(!isset($this->subresources[$type])){
            $class = 'Nexmo\Call\\' . $type;
            $instance = new $class($this->getId());
            $instance->setClient($this->getClient());
            $this->subresources[$type] = $instance;
        }

        return $this->subresources[$type];
    }

    public function jsonSerialize()
    {
        $data = $this->data;

        if(isset($this->to)){
            $data['to'] = [$this->to->jsonSerialize()];
        }

        if(isset($this->from)){
            $data['from'] = $this->from->jsonSerialize();
        }

        foreach($this->webhooks as $webhook){
            $data = array_merge($data, $webhook->jsonSerialize());
        }

        return $data;
    }

    public function jsonUnserialize(array $json)
    {
        $this->data = $json;
        $this->id = $json['uuid'];
    }
}
