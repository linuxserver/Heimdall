<?php

namespace Github\Api\Project;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;
use Github\Exception\MissingArgumentException;

class Columns extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the accept header for Early Access to the projects api.
     *
     * @see https://developer.github.com/v3/repos/projects/#projects
     *
     * return self
     */
    public function configure()
    {
        $this->acceptHeaderValue = 'application/vnd.github.inertia-preview+json';

        return $this;
    }

    public function all($projectId, array $params = [])
    {
        return $this->get('/projects/'.rawurlencode($projectId).'/columns', array_merge(['page' => 1], $params));
    }

    public function show($id)
    {
        return $this->get('/projects/columns/'.rawurlencode($id));
    }

    public function create($projectId, array $params)
    {
        if (!isset($params['name'])) {
            throw new MissingArgumentException(['name']);
        }

        return $this->post('/projects/'.rawurlencode($projectId).'/columns', $params);
    }

    public function update($id, array $params)
    {
        if (!isset($params['name'])) {
            throw new MissingArgumentException(['name']);
        }

        return $this->patch('/projects/columns/'.rawurlencode($id), $params);
    }

    public function deleteColumn($id)
    {
        return $this->delete('/projects/columns/'.rawurlencode($id));
    }

    public function move($id, array $params)
    {
        if (!isset($params['position'])) {
            throw new MissingArgumentException(['position']);
        }

        return $this->post('/projects/columns/'.rawurlencode($id).'/moves', $params);
    }

    public function cards()
    {
        return new Cards($this->getClient());
    }
}
