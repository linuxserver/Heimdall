<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2018 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Conversations;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\CollectionInterface;
use Nexmo\Entity\CollectionTrait;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\NoRequestResponseTrait;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;

class Collection implements ClientAwareInterface, CollectionInterface, \ArrayAccess
{
    use ClientAwareTrait;
    use CollectionTrait;
    use JsonSerializableTrait;
    use NoRequestResponseTrait;
    use JsonResponseTrait;

    public static function getCollectionName()
    {
        return 'conversations';
    }

    public static function getCollectionPath()
    {
        return '/beta/' . self::getCollectionName();
    }

    public function hydrateEntity($data, $idOrConversation)
    {
        if(!($idOrConversation instanceof Conversation)){
            $idOrConversation = new Conversation($idOrConversation);
        }

        $idOrConversation->setClient($this->getClient());
        $idOrConversation->jsonUnserialize($data);

        return $idOrConversation;
    }

    public function hydrateAll($conversations)
    {
        $hydrated = [];
        foreach ($conversations as $conversation) {
            $hydrated[] = $this->hydrateEntity($conversation, $conversation['id']);
        }

        return $hydrated;
    }

    /**
     * @param null $conversation
     * @return $this|Conversation
     */
    public function __invoke(Filter $filter = null)
    {
        if(!is_null($filter)){
            $this->setFilter($filter);
        }

        return $this;
    }

    public function create($conversation)
    {
        return $this->post($conversation);
    }

    public function post($conversation)
    {
        if($conversation instanceof Conversation){
            $body = $conversation->getRequestData();
        } else {
            $body = $conversation;
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath()
            ,'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->getClient()->send($request);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $body = json_decode($response->getBody()->getContents(), true);
        $conversation = new Conversation($body['id']);
        $conversation->jsonUnserialize($body);
        $conversation->setClient($this->getClient());

        return $conversation;
    }

    public function get($conversation)
    {
        if(!($conversation instanceof Conversation)){
            $conversation = new Conversation($conversation);
        }

        $conversation->setClient($this->getClient());
        $conversation->get();

        return $conversation;
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        // This message isn't very useful, but we shouldn't ever see it
        $errorTitle = 'Unexpected error';

        if (isset($body['description'])) {
            $errorTitle = $body['description'];
        }

        if (isset($body['error_title'])) {
            $errorTitle = $body['error_title'];
        }

        if($status >= 400 AND $status < 500) {
            $e = new Exception\Request($errorTitle, $status);
        } elseif($status >= 500 AND $status < 600) {
            $e = new Exception\Server($errorTitle, $status);
        } else {
            $e = new Exception\Exception('Unexpected HTTP Status Code');
            throw $e;
        }

        return $e;
    }

    public function offsetExists($offset)
    {
        return true;
    }

    /**
     * @param mixed $conversation
     * @return Conversation
     */
    public function offsetGet($conversation)
    {
        if(!($conversation instanceof Conversation)){
            $conversation = new Conversation($conversation);
        }

        $conversation->setClient($this->getClient());
        return $conversation;
    }

    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('can not set collection properties');
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('can not unset collection properties');
    }
}
