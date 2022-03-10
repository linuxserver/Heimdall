<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/repos/hooks/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Hooks extends AbstractApi
{
    public function all($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/hooks');
    }

    public function show($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/hooks/'.rawurlencode($id));
    }

    public function create($username, $repository, array $params)
    {
        if (!isset($params['name'], $params['config'])) {
            throw new MissingArgumentException(['name', 'config']);
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/hooks', $params);
    }

    public function update($username, $repository, $id, array $params)
    {
        if (!isset($params['config'])) {
            throw new MissingArgumentException(['config']);
        }

        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/hooks/'.rawurlencode($id), $params);
    }

    public function ping($username, $repository, $id)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/hooks/'.rawurlencode($id).'/pings');
    }

    public function test($username, $repository, $id)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/hooks/'.rawurlencode($id).'/tests');
    }

    public function remove($username, $repository, $id)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/hooks/'.rawurlencode($id));
    }
}
