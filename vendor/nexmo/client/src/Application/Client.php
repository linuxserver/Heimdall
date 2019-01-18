<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Application;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\CollectionInterface;
use Nexmo\Entity\CollectionTrait;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;

class Client implements ClientAwareInterface, CollectionInterface
{
    use ClientAwareTrait;
    use CollectionTrait;

    public static function getCollectionName()
    {
        return 'applications';
    }

    public static function getCollectionPath()
    {
        return '/v1/' . self::getCollectionName();
    }

    public function hydrateEntity($data, $id)
    {
        $application = new Application($id);
        $application->jsonUnserialize($data);
        return $application;
    }

    public function get($application)
    {
        if(!($application instanceof Application)){
            $application = new Application($application);
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath() . '/' . $application->getId()
            ,'GET'
        );

        $application->setRequest($request);
        $response = $this->client->send($request);
        $application->setResponse($response);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response, $application);
        }

        return $application;
    }

    public function create($application)
    {
        return $this->post($application);
    }

    public function post($application)
    {
        if(!($application instanceof Application)){
            $application = $this->createFromArray($application);
        }

        $body = $application->getRequestData(false);

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath()
            ,'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $application->setRequest($request);
        $response = $this->client->send($request);
        $application->setResponse($response);

        if($response->getStatusCode() != '201'){
            throw $this->getException($response, $application);
        }

        return $application;
    }

    public function update($application, $id = null)
    {
        return $this->put($application, $id);
    }

    public function put($application, $id = null)
    {
        if(!($application instanceof Application)){
            $application = $this->createFromArray($application);
        }

        if(is_null($id)){
            $id = $application->getId();
        }

        $body = $application->getRequestData(false);

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath() . '/' . $id,
            'PUT',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $application->setRequest($request);
        $response = $this->client->send($request);
        $application->setResponse($response);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response, $application);
        }

        return $application;
    }

    public function delete($application)
    {
        if(($application instanceof Application)){
            $id = $application->getId();
        } else {
            $id = $application;
        }

        $request = new Request(
            $this->getClient()->getApiUrl(). $this->getCollectionPath() . '/' . $id
            ,'DELETE'
        );

        if($application instanceof Application){
            $application->setRequest($request);
        }

        $response = $this->client->send($request);

        if($application instanceof Application){
            $application->setResponse($response);
        }

        if($response->getStatusCode() != '204'){
            throw $this->getException($response, $application);
        }

        return true;
    }

    protected function getException(ResponseInterface $response, $application = null)
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

        //todo use interfaces here
        if(($application instanceof Application) AND (($e instanceof Exception\Request) OR ($e instanceof Exception\Server))){
            $e->setEntity($application);
        }

        return $e;
    }

    protected function createFromArray($array)
    {
        if(!is_array($array)){
            throw new \RuntimeException('application must implement `' . ApplicationInterface::class . '` or be an array`');
        }

        foreach(['name',] as $param){
            if(!isset($array[$param])){
                throw new \InvalidArgumentException('missing expected key `' . $param . '`');
            }
        }

        $application = new Application();
        $application->setName($array['name']);

        foreach(['event', 'answer'] as $type){
            if(isset($array[$type . '_url'])){
                $method = isset($array[$type . '_method']) ? $array[$type . '_method'] : null;
                $application->getVoiceConfig()->setWebhook($type . '_url', new Webhook($array[$type . '_url'], $method));
            }
        }

        return $application;
    }
}
