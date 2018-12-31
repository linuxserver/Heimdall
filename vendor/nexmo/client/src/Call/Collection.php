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
use Nexmo\Entity\CollectionInterface;
use Nexmo\Entity\CollectionTrait;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;

class Collection implements ClientAwareInterface, CollectionInterface, \ArrayAccess
{
    use ClientAwareTrait;
    use CollectionTrait;

    public static function getCollectionName()
    {
        return 'calls';
    }

    public static function getCollectionPath()
    {
        return '/v1/' . self::getCollectionName();
    }

    public function hydrateEntity($data, $idOrCall)
    {
        if(!($idOrCall instanceof Call)){
            $idOrCall = new Call($idOrCall);
        }

        $idOrCall->setClient($this->getClient());
        $idOrCall->jsonUnserialize($data);

        return $idOrCall;
    }

    /**
     * @param null $callOrFilter
     * @return $this|Call
     */
    public function __invoke(Filter $filter = null)
    {
        if(!is_null($filter)){
            $this->setFilter($filter);
        }

        return $this;
    }

    public function create($call)
    {
        return $this->post($call);
    }

    public function put($payload, $idOrCall)
    {
        if(!($idOrCall instanceof Call)){
            $idOrCall = new Call($idOrCall);
        }

        $idOrCall->setClient($this->getClient());
        $idOrCall->put($payload);
        return $idOrCall;
    }

    public function delete($call = null, $type)
    {
        if(is_object($call) AND is_callable([$call, 'getId'])){
            $call = $call->getId();
        }

        if(!($call instanceof Call)){
            $call = new Call($call);
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath() . '/' . $call->getId() . '/' . $type
            ,'DELETE'
        );

        $response = $this->client->send($request);

        if($response->getStatusCode() != '204'){
            throw $this->getException($response);
        }

        return $call;
    }

    public function post($call)
    {
        if($call instanceof Call){
            $body = $call->getRequestData();
        } else {
            $body = $call;
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath()
            ,'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->client->send($request);

        if($response->getStatusCode() != '201'){
            throw $this->getException($response);
        }

        $body = json_decode($response->getBody()->getContents(), true);
        $call = new Call($body['uuid']);
        $call->jsonUnserialize($body);
        $call->setClient($this->getClient());

        return $call;
    }

    public function get($call)
    {
        if(!($call instanceof Call)){
            $call = new Call($call);
        }

        $call->setClient($this->getClient());
        $call->get();

        return $call;
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        // Error responses aren't consistent. Some are generated within the
        // proxy and some are generated within voice itself. This handles
        // both cases

        // This message isn't very useful, but we shouldn't ever see it
        $errorTitle = 'Unexpected error';

        if (isset($body['title'])) {
            $errorTitle = $body['title'];
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
        //todo: validate form of id
        return true;
    }

    /**
     * @param mixed $call
     * @return Call
     */
    public function offsetGet($call)
    {
        if(!($call instanceof Call)){
            $call = new Call($call);
        }

        $call->setClient($this->getClient());
        return $call;
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
