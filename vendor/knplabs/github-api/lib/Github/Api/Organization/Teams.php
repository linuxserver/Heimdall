<?php

namespace Github\Api\Organization;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/orgs/teams/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Teams extends AbstractApi
{
    public function all($organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/teams');
    }

    public function create($organization, array $params)
    {
        if (!isset($params['name'])) {
            throw new MissingArgumentException('name');
        }
        if (isset($params['repo_names']) && !is_array($params['repo_names'])) {
            $params['repo_names'] = [$params['repo_names']];
        }
        if (isset($params['permission']) && !in_array($params['permission'], ['pull', 'push', 'admin'])) {
            $params['permission'] = 'pull';
        }

        return $this->post('/orgs/'.rawurlencode($organization).'/teams', $params);
    }

    public function show($team)
    {
        return $this->get('/teams/'.rawurlencode($team));
    }

    public function update($team, array $params)
    {
        if (!isset($params['name'])) {
            throw new MissingArgumentException('name');
        }
        if (isset($params['permission']) && !in_array($params['permission'], ['pull', 'push', 'admin'])) {
            $params['permission'] = 'pull';
        }

        return $this->patch('/teams/'.rawurlencode($team), $params);
    }

    public function remove($team)
    {
        return $this->delete('/teams/'.rawurlencode($team));
    }

    public function members($team)
    {
        return $this->get('/teams/'.rawurlencode($team).'/members');
    }

    public function check($team, $username)
    {
        return $this->get('/teams/'.rawurlencode($team).'/memberships/'.rawurlencode($username));
    }

    public function addMember($team, $username)
    {
        return $this->put('/teams/'.rawurlencode($team).'/memberships/'.rawurlencode($username));
    }

    public function removeMember($team, $username)
    {
        return $this->delete('/teams/'.rawurlencode($team).'/memberships/'.rawurlencode($username));
    }

    public function repositories($team)
    {
        return $this->get('/teams/'.rawurlencode($team).'/repos');
    }

    public function repository($team, $organization, $repository)
    {
        return $this->get('/teams/'.rawurlencode($team).'/repos/'.rawurlencode($organization).'/'.rawurlencode($repository));
    }

    public function addRepository($team, $organization, $repository, $params = [])
    {
        if (isset($params['permission']) && !in_array($params['permission'], ['pull', 'push', 'admin'])) {
            $params['permission'] = 'pull';
        }

        return $this->put('/teams/'.rawurlencode($team).'/repos/'.rawurlencode($organization).'/'.rawurlencode($repository), $params);
    }

    public function removeRepository($team, $organization, $repository)
    {
        return $this->delete('/teams/'.rawurlencode($team).'/repos/'.rawurlencode($organization).'/'.rawurlencode($repository));
    }
}
