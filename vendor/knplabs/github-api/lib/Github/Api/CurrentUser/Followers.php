<?php

namespace Github\Api\CurrentUser;

use Github\Api\AbstractApi;

/**
 * @link   http://developer.github.com/v3/users/followers/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Followers extends AbstractApi
{
    /**
     * List followed users by the authenticated user.
     *
     * @link http://developer.github.com/v3/repos/followers/
     *
     * @param int $page
     *
     * @return array
     */
    public function all($page = 1)
    {
        return $this->get('/user/following', [
            'page' => $page,
        ]);
    }

    /**
     * Check that the authenticated user follows a user.
     *
     * @link http://developer.github.com/v3/repos/followers/
     *
     * @param string $username the username to follow
     *
     * @return array
     */
    public function check($username)
    {
        return $this->get('/user/following/'.rawurlencode($username));
    }

    /**
     * Make the authenticated user follow a user.
     *
     * @link http://developer.github.com/v3/repos/followers/
     *
     * @param string $username the username to follow
     *
     * @return array
     */
    public function follow($username)
    {
        return $this->put('/user/following/'.rawurlencode($username));
    }

    /**
     * Make the authenticated user un-follow a user.
     *
     * @link http://developer.github.com/v3/repos/followers/
     *
     * @param string $username the username to un-follow
     *
     * @return array
     */
    public function unfollow($username)
    {
        return $this->delete('/user/following/'.rawurlencode($username));
    }
}
