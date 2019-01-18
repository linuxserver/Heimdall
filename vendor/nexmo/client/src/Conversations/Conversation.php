<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Conversations;


use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\EntityInterface;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\JsonUnserializableInterface;
use Nexmo\Entity\NoRequestResponseTrait;
use Nexmo\User\Collection as UserCollection;
use Nexmo\User\User;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;

class Conversation implements EntityInterface, \JsonSerializable, JsonUnserializableInterface, ClientAwareInterface
{

    use NoRequestResponseTrait;
    use JsonSerializableTrait;
    use JsonResponseTrait;
    use ClientAwareTrait;

    protected $data = [];

    public function __construct($id = null)
    {
        $this->data['id'] = $id;
    }

    public function setName($name)
    {
        $this->data['name'] = $name;
        return $this;
    }

    public function setDisplayName($name)
    {
        $this->data['display_name'] = $name;
        return $this;
    }

    public function getId()
    {
        if (isset($this->data['uuid'])) {
            return $this->data['uuid'];
        }
        return $this->data['id'];
    }

    public function __toString()
    {
        return (string)$this->getId();
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


    public function jsonSerialize()
    {
        return $this->data;
    }

    public function jsonUnserialize(array $json)
    {
        $this->data = $json;
    }

    public function members()
    {
        $response = $this->getClient()->get($this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId() .'/members');

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $memberCollection = new UserCollection();
        return $memberCollection->hydrateAll($data);
    }

    public function addMember(User $user)
    {
        return $this->sendPostAction($user, 'join');
    }

    public function inviteMember(User $user)
    {
        return $this->sendPostAction($user, 'invite');
    }

    public function removeMember(User $user)
    {
        $response = $this->getClient()->delete(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId() .'/members/'. $user->getId()
        );

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }
    }

    public function sendPostAction(User $user, $action, $channel = 'app') {
        $body = $user->getRequestDataForConversation();
        $body['action'] = $action;
        $body['channel'] = ['type' => $channel];

        $response = $this->getClient()->post(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId() .'/members',
            $body
        );

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $body = json_decode($response->getBody()->getContents(), true);

        $user = new User($body['user_id']);
        $user->jsonUnserialize($body);
        $user->setClient($this->getClient());

        return $user;
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


}
