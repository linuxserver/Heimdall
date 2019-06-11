<?php

namespace Github\Api;

use Github\Api\Organization\Hooks;
use Github\Api\Organization\Members;
use Github\Api\Organization\Teams;

/**
 * Getting organization information and managing authenticated organization account information.
 *
 * @link   http://developer.github.com/v3/orgs/
 *
 * @author Antoine Berranger <antoine at ihqs dot net>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Organization extends AbstractApi
{
    /**
     * @link https://developer.github.com/v3/orgs/#list-all-organizations
     *
     * @return array the organizations
     */
    public function all($since = '')
    {
        return $this->get('/organizations?since='.rawurlencode($since));
    }

    /**
     * Get extended information about an organization by its name.
     *
     * @link http://developer.github.com/v3/orgs/#get
     *
     * @param string $organization the organization to show
     *
     * @return array information about the organization
     */
    public function show($organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization));
    }

    public function update($organization, array $params)
    {
        return $this->patch('/orgs/'.rawurlencode($organization), $params);
    }

    /**
     * List all repositories across all the organizations that you can access.
     *
     * @link http://developer.github.com/v3/repos/#list-organization-repositories
     *
     * @param string $organization the user name
     * @param string $type         the type of repositories
     * @param int    $page         the page
     *
     * @return array the repositories
     */
    public function repositories($organization, $type = 'all', $page = 1)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/repos', [
            'type' => $type,
            'page' => $page,
        ]);
    }

    /**
     * @return Members
     */
    public function members()
    {
        return new Members($this->client);
    }

    /**
     * @return Hooks
     */
    public function hooks()
    {
        return new Hooks($this->client);
    }

    /**
     * @return Teams
     */
    public function teams()
    {
        return new Teams($this->client);
    }

    /**
     * @link http://developer.github.com/v3/issues/#list-issues
     *
     * @param string $organization
     * @param array  $params
     * @param int    $page
     *
     * @return array
     */
    public function issues($organization, array $params = [], $page = 1)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/issues', array_merge(['page' => $page], $params));
    }
}
