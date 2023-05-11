<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/issues/labels/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Labels extends AbstractApi
{
    public function all($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels');
    }

    public function show($username, $repository, $label)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels/'.rawurlencode($label));
    }

    public function create($username, $repository, array $params)
    {
        if (!isset($params['name'], $params['color'])) {
            throw new MissingArgumentException(['name', 'color']);
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels', $params);
    }

    public function update($username, $repository, $label, array $params)
    {
        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels/'.rawurlencode($label), $params);
    }

    public function remove($username, $repository, $label)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels/'.rawurlencode($label));
    }
}
