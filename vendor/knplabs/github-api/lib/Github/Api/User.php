<?php

namespace Github\Api;

/**
 * Searching users, getting user information.
 *
 * @link   http://developer.github.com/v3/users/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Thibault Duplessis <thibault.duplessis at gmail dot com>
 */
class User extends AbstractApi
{
    /**
     * Search users by username.
     *
     * @deprecated This method is deprecated use the Search api instead. See https://developer.github.com/v3/search/legacy/#legacy-search-api-is-deprecated
     * @link http://developer.github.com/v3/search/#search-users
     *
     * @param string $keyword the keyword to search
     *
     * @return array list of users found
     */
    public function find($keyword)
    {
        return $this->get('/legacy/user/search/'.rawurlencode($keyword));
    }

    /**
     * Request all users.
     *
     * @link https://developer.github.com/v3/users/#get-all-users
     *
     * @param int|null $id ID of the last user that you've seen
     *
     * @return array list of users found
     */
    public function all($id = null)
    {
        if (!is_int($id)) {
            return $this->get('/users');
        }

        return $this->get('/users', ['since' => rawurldecode($id)]);
    }

    /**
     * Get extended information about a user by its username.
     *
     * @link http://developer.github.com/v3/users/
     *
     * @param string $username the username to show
     *
     * @return array information about the user
     */
    public function show($username)
    {
        return $this->get('/users/'.rawurlencode($username));
    }

    /**
     * Get extended information about a user by its username.
     *
     * @link https://developer.github.com/v3/orgs/
     *
     * @param string $username the username to show
     *
     * @return array information about organizations that user belongs to
     */
    public function organizations($username)
    {
        return $this->get('/users/'.rawurlencode($username).'/orgs');
    }

    /**
     * Get user organizations.
     *
     * @link https://developer.github.com/v3/orgs/#list-your-organizations
     *
     * @return array information about organizations that authenticated user belongs to
     */
    public function orgs()
    {
        return $this->get('/user/orgs');
    }

    /**
     * Request the users that a specific user is following.
     *
     * @link http://developer.github.com/v3/users/followers/
     *
     * @param string $username       the username
     * @param array  $parameters     parameters for the query string
     * @param array  $requestHeaders additional headers to set in the request
     *
     * @return array list of followed users
     */
    public function following($username, array $parameters = [], array $requestHeaders = [])
    {
        return $this->get('/users/'.rawurlencode($username).'/following', $parameters, $requestHeaders);
    }

    /**
     * Request the users following a specific user.
     *
     * @link http://developer.github.com/v3/users/followers/
     *
     * @param string $username       the username
     * @param array  $parameters     parameters for the query string
     * @param array  $requestHeaders additional headers to set in the request
     *
     * @return array list of following users
     */
    public function followers($username, array $parameters = [], array $requestHeaders = [])
    {
        return $this->get('/users/'.rawurlencode($username).'/followers', $parameters, $requestHeaders);
    }

    /**
     * Request the repository that a specific user is watching.
     *
     * @deprecated see subscriptions method
     *
     * @param string $username the username
     *
     * @return array list of watched repositories
     */
    public function watched($username)
    {
        return $this->get('/users/'.rawurlencode($username).'/watched');
    }

    /**
     * Request starred repositories that a specific user has starred.
     *
     * @link http://developer.github.com/v3/activity/starring/
     *
     * @param string $username  the username
     * @param int    $page      the page number of the paginated result set
     * @param int    $perPage   the number of results per page
     * @param string $sort      sort by (possible values: created, updated)
     * @param string $direction direction of sort (possible values: asc, desc)
     *
     * @return array list of starred repositories
     */
    public function starred($username, $page = 1, $perPage = 30, $sort = 'created', $direction = 'desc')
    {
        return $this->get('/users/'.rawurlencode($username).'/starred', [
            'page' => $page,
            'per_page' => $perPage,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    /**
     * Request the repository that a specific user is watching.
     *
     * @link http://developer.github.com/v3/activity/watching/
     *
     * @param string $username the username
     *
     * @return array list of watched repositories
     */
    public function subscriptions($username)
    {
        return $this->get('/users/'.rawurlencode($username).'/subscriptions');
    }

    /**
     * List public repositories for the specified user.
     *
     * @link https://developer.github.com/v3/repos/#list-user-repositories
     *
     * @param string $username    the username
     * @param string $type        role in the repository
     * @param string $sort        sort by
     * @param string $direction   direction of sort, asc or desc
     * @param string $visibility  visibility of repository
     * @param string $affiliation relationship to repository
     *
     * @return array list of the user repositories
     */
    public function repositories($username, $type = 'owner', $sort = 'full_name', $direction = 'asc', $visibility = 'all', $affiliation = 'owner,collaborator,organization_member')
    {
        return $this->get('/users/'.rawurlencode($username).'/repos', [
            'type' => $type,
            'sort' => $sort,
            'direction' => $direction,
            'visibility' => $visibility,
            'affiliation' => $affiliation,
        ]);
    }

    /**
     * List repositories that are accessible to the authenticated user.
     *
     * @link https://developer.github.com/v3/repos/#list-your-repositories
     *
     * @param array $params visibility, affiliation, type, sort, direction
     *
     * @return array list of the user repositories
     */
    public function myRepositories(array $params = [])
    {
        return $this->get('/user/repos', $params);
    }

    /**
     * Get the public gists for a user.
     *
     * @link http://developer.github.com/v3/gists/
     *
     * @param string $username the username
     *
     * @return array list of the user gists
     */
    public function gists($username)
    {
        return $this->get('/users/'.rawurlencode($username).'/gists');
    }

    /**
     * Get the public keys for a user.
     *
     * @link http://developer.github.com/v3/users/keys/#list-public-keys-for-a-user
     *
     * @param string $username the username
     *
     * @return array list of the user public keys
     */
    public function keys($username)
    {
        return $this->get('/users/'.rawurlencode($username).'/keys');
    }

    /**
     * List events performed by a user.
     *
     * @link http://developer.github.com/v3/activity/events/#list-public-events-performed-by-a-user
     *
     * @param string $username
     *
     * @return array
     */
    public function publicEvents($username)
    {
        return $this->get('/users/'.rawurlencode($username).'/events/public');
    }
}
