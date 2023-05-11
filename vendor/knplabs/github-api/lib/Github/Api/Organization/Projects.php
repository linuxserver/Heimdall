<?php

namespace Github\Api\Organization;

use Github\Api\Project\AbstractProjectApi;
use Github\Exception\MissingArgumentException;

class Projects extends AbstractProjectApi
{
    public function all($organization, array $params = [])
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/projects', array_merge(['page' => 1], $params));
    }

    public function create($organization, array $params)
    {
        if (!isset($params['name'])) {
            throw new MissingArgumentException(['name']);
        }

        return $this->post('/orgs/'.rawurlencode($organization).'/projects', $params);
    }
}
