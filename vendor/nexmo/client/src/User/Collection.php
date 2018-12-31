<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2018 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\User;

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
        return 'users';
    }

    public static function getCollectionPath()
    {
        return '/beta/' . self::getCollectionName();
    }

    public function hydrateEntity($data, $idOrUser)
    {
        if(!($idOrUser instanceof User)){
            $idOrUser = new User($idOrUser);
        }

        $idOrUser->setClient($this->getClient());
        $idOrUser->jsonUnserialize($data);

        return $idOrUser;
    }

    public function hydrateAll($users)
    {
        $hydrated = [];
        foreach ($users as $u) {
            $key = isset($u['user_id']) ? 'user_id' : 'id';
            $user = new User($u[$key]);

            // Setting the client makes us run out of memory and I'm not sure why yet
            // $idOrUser->setClient($this->getClient());

            $user->jsonUnserialize($u);
            $hydrated[] = $user;
        }

        return $hydrated;
    }

    /**
     * @param null $user
     * @return $this|User
     */
    public function __invoke(Filter $filter = null)
    {
        if(!is_null($filter)){
            $this->setFilter($filter);
        }

        return $this;
    }

    public function fetch() {
        $this->fetchPage(self::getCollectionPath());
        return $this->hydrateAll($this->page);
    }

    public function create($user)
    {
        return $this->post($user);
    }

    public function post($user)
    {
        if($user instanceof User){
            $body = $user->getRequestData();
        } else {
            $body = $user;
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath()
            ,'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->client->send($request);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $body = json_decode($response->getBody()->getContents(), true);
        $user = new User($body['id']);
        $user->jsonUnserialize($body);
        $user->setClient($this->getClient());

        return $user;
    }

    public function get($user)
    {
        if(!($user instanceof User)){
            $user = new User($user);
        }

        $user->setClient($this->getClient());
        $user->get();

        return $user;
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        // This message isn't very useful, but we shouldn't ever see it
        $errorTitle = 'Unexpected error';

        if (isset($body['code'])) {
            $errorTitle = $body['code'];
        }

        if (isset($body['description']) && $body['description']) {
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
     * @param mixed $user
     * @return User
     */
    public function offsetGet($user)
    {
        if(!($user instanceof User)){
            $user = new User($user);
        }

        $user->setClient($this->getClient());
        return $user;
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
