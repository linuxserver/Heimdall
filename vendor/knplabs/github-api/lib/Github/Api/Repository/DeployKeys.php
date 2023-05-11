<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/repos/keys/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class DeployKeys extends AbstractApi
{
    public function all($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/keys');
    }

    public function show($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/keys/'.rawurlencode($id));
    }

    public function create($username, $repository, array $params)
    {
        if (!isset($params['title'], $params['key'])) {
            throw new MissingArgumentException(['title', 'key']);
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/keys', $params);
    }

    public function update($username, $repository, $id, array $params)
    {
        if (!isset($params['title'], $params['key'])) {
            throw new MissingArgumentException(['title', 'key']);
        }

        $this->remove($username, $repository, $id);

        return $this->create($username, $repository, $params);
    }

    public function remove($username, $repository, $id)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/keys/'.rawurlencode($id));
    }
}
