<?php

namespace Github\Api;

use Github\Api\CurrentUser\Emails;
use Github\Api\CurrentUser\Followers;
use Github\Api\CurrentUser\Memberships;
use Github\Api\CurrentUser\Notifications;
use Github\Api\CurrentUser\PublicKeys;
use Github\Api\CurrentUser\Starring;
use Github\Api\CurrentUser\Watchers;

/**
 * @link   http://developer.github.com/v3/users/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Felipe Valtl de Mello <eu@felipe.im>
 */
class CurrentUser extends AbstractApi
{
    use AcceptHeaderTrait;

    public function show()
    {
        return $this->get('/user');
    }

    public function update(array $params)
    {
        return $this->patch('/user', $params);
    }

    /**
     * @return Emails
     */
    public function emails()
    {
        return new Emails($this->getClient());
    }

    /**
     * @return Followers
     */
    public function follow()
    {
        return new Followers($this->getClient());
    }

    public function followers($page = 1)
    {
        return $this->get('/user/followers', [
            'page' => $page,
        ]);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/issues#list-user-account-issues-assigned-to-the-authenticated-user
     *
     * @param array $params
     * @param bool  $includeOrgIssues
     *
     * @return array
     */
    public function issues(array $params = [], $includeOrgIssues = true)
    {
        return $this->get($includeOrgIssues ? '/issues' : '/user/issues', array_merge(['page' => 1], $params));
    }

    /**
     * @return PublicKeys
     */
    public function keys()
    {
        return new PublicKeys($this->getClient());
    }

    /**
     * @return Notifications
     */
    public function notifications()
    {
        return new Notifications($this->getClient());
    }

    /**
     * @return Memberships
     */
    public function memberships()
    {
        return new Memberships($this->getClient());
    }

    /**
     * @link https://docs.github.com/en/rest/reference/orgs#list-organizations-for-the-authenticated-user
     *
     * @return array
     */
    public function organizations()
    {
        return $this->get('/user/orgs');
    }

    /**
     * @link https://developer.github.com/v3/orgs/teams/#list-user-teams
     *
     * @return array
     */
    public function teams()
    {
        return $this->get('/user/teams');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/repos#list-repositories-for-the-authenticated-user
     *
     * @param string $type        role in the repository
     * @param string $sort        sort by
     * @param string $direction   direction of sort, asc or desc
     * @param string $visibility  visibility of repository
     * @param string $affiliation relationship to repository
     *
     * @return array
     */
    public function repositories($type = 'owner', $sort = 'full_name', $direction = 'asc', $visibility = null, $affiliation = null)
    {
        $params = [
            'type' => $type,
            'sort' => $sort,
            'direction' => $direction,
        ];

        if (null !== $visibility) {
            unset($params['type']);
            $params['visibility'] = $visibility;
        }

        if (null !== $affiliation) {
            unset($params['type']);
            $params['affiliation'] = $affiliation;
        }

        return $this->get('/user/repos', $params);
    }

    /**
     * @return Watchers
     */
    public function watchers()
    {
        return new Watchers($this->getClient());
    }

    /**
     * @return Starring
     */
    public function starring()
    {
        return new Starring($this->getClient());
    }

    /**
     *  @link https://docs.github.com/en/rest/reference/activity#list-repositories-watched-by-the-authenticated-user
     */
    public function subscriptions()
    {
        return $this->get('/user/subscriptions');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/apps#list-app-installations-accessible-to-the-user-access-token
     *
     * @param array $params
     */
    public function installations(array $params = [])
    {
        $this->acceptHeaderValue = 'application/vnd.github.machine-man-preview+json';

        return $this->get('/user/installations', array_merge(['page' => 1], $params));
    }

    /**
     * @link https://developer.github.com/v3/apps/installations/#list-repositories-accessible-to-the-user-access-token
     *
     * @param string $installationId the ID of the Installation
     * @param array  $params
     */
    public function repositoriesByInstallation($installationId, array $params = [])
    {
        $this->acceptHeaderValue = 'application/vnd.github.machine-man-preview+json';

        return $this->get(sprintf('/user/installations/%s/repositories', $installationId), array_merge(['page' => 1], $params));
    }
}
