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

    /**
     * @link https://developer.github.com/v3/teams/#list-teams
     */
    public function show($team, $organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team));
    }

    /**
     * @link https://developer.github.com/v3/teams/#edit-team
     */
    public function update($team, array $params, $organization)
    {
        if (!isset($params['name'])) {
            throw new MissingArgumentException('name');
        }
        if (isset($params['permission']) && !in_array($params['permission'], ['pull', 'push', 'admin'])) {
            $params['permission'] = 'pull';
        }

        return $this->patch('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team), $params);
    }

    /**
     * @link https://developer.github.com/v3/teams/#delete-team
     */
    public function remove($team, $organization)
    {
        return $this->delete('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team));
    }

    /**
     * @link https://developer.github.com/v3/teams/members/#list-team-members
     */
    public function members($team, $organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team).'/members');
    }

    /**
     * @link https://developer.github.com/v3/teams/members/#get-team-membership
     */
    public function check($team, $username, $organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team).'/memberships/'.rawurlencode($username));
    }

    /**
     * @link https://developer.github.com/v3/teams/members/#add-or-update-team-membership
     */
    public function addMember($team, $username, $organization)
    {
        return $this->put('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team).'/memberships/'.rawurlencode($username));
    }

    /**
     * @link https://developer.github.com/v3/teams/members/#remove-team-membership
     */
    public function removeMember($team, $username, $organization)
    {
        return $this->delete('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team).'/memberships/'.rawurlencode($username));
    }

    /**
     * @link https://docs.github.com/en/rest/teams/teams#list-team-repositories
     */
    public function repositories($team, $organization = '')
    {
        if (empty($organization)) {
            return $this->get('/teams/'.rawurlencode($team).'/repos');
        }

        return $this->get('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team).'/repos');
    }

    public function repository($team, $organization, $repository)
    {
        return $this->get('/teams/'.rawurlencode($team).'/repos/'.rawurlencode($organization).'/'.rawurlencode($repository));
    }

    public function addRepository($team, $organization, $repository, $params = [])
    {
        if (isset($params['permission']) && !in_array($params['permission'], ['pull', 'push', 'admin', 'maintain', 'triage'])) {
            $params['permission'] = 'pull';
        }

        return $this->put('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team).'/repos/'.rawurlencode($organization).'/'.rawurlencode($repository), $params);
    }

    public function removeRepository($team, $organization, $repository)
    {
        return $this->delete('/orgs/'.rawurlencode($organization).'/teams/'.rawurlencode($team).'/repos/'.rawurlencode($organization).'/'.rawurlencode($repository));
    }
}
