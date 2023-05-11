<?php

namespace Github\Api\Organization;

use Github\Api\AbstractApi;

/**
 * @link   https://developer.github.com/v3/orgs/outside_collaborators/
 *
 * @author Matthieu Calie <matthieu@calie.be>
 */
class OutsideCollaborators extends AbstractApi
{
    /**
     * @link https://developer.github.com/v3/orgs/outside_collaborators/#list-outside-collaborators-for-an-organization
     *
     * @param string $organization the organization
     * @param array  $params
     *
     * @return array the organizations
     */
    public function all($organization, array $params = [])
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/outside_collaborators', $params);
    }

    /**
     * @link https://developer.github.com/v3/orgs/outside_collaborators/#convert-an-organization-member-to-outside-collaborator
     *
     * @param string $organization the organization
     * @param string $username     the github username
     *
     * @return array
     */
    public function convert($organization, $username)
    {
        return $this->put('/orgs/'.rawurlencode($organization).'/outside_collaborators/'.rawurldecode($username));
    }

    /**
     * @link https://developer.github.com/v3/orgs/outside_collaborators/#remove-outside-collaborator-from-an-organization
     *
     * @param string $organization the organization
     * @param string $username     the username
     *
     * @return array
     */
    public function remove($organization, $username)
    {
        return $this->delete('/orgs/'.rawurlencode($organization).'/outside_collaborators/'.rawurldecode($username));
    }
}
