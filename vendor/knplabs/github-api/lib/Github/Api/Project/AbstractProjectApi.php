<?php

namespace Github\Api\Project;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

abstract class AbstractProjectApi extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the accept header for Early Access to the projects api.
     *
     * @see https://developer.github.com/v3/repos/projects/#projects
     *
     * @return $this
     */
    public function configure()
    {
        $this->acceptHeaderValue = 'application/vnd.github.inertia-preview+json';

        return $this;
    }

    public function show($id, array $params = [])
    {
        return $this->get('/projects/'.rawurlencode($id), array_merge(['page' => 1], $params));
    }

    public function update($id, array $params)
    {
        return $this->patch('/projects/'.rawurlencode($id), $params);
    }

    public function deleteProject($id)
    {
        return $this->delete('/projects/'.rawurlencode($id));
    }

    public function columns()
    {
        return new Columns($this->getClient());
    }
}
