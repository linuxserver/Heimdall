<?php

namespace Github\Api\CurrentUser;

use Github\Api\AbstractApi;

/**
 * @link   https://developer.github.com/v3/activity/watching/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @revised Felipe Valtl de Mello <eu@felipe.im>
 */
class Watchers extends AbstractApi
{
    /**
     * List repositories watched by the authenticated user.
     *
     * @link https://developer.github.com/v3/activity/watching/
     *
     * @param int $page
     *
     * @return array
     */
    public function all($page = 1)
    {
        return $this->get('/user/subscriptions', [
            'page' => $page,
        ]);
    }

    /**
     * Check that the authenticated user watches a repository.
     *
     * @link https://developer.github.com/v3/activity/watching/
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     *
     * @return array
     */
    public function check($username, $repository)
    {
        return $this->get('/user/subscriptions/'.rawurlencode($username).'/'.rawurlencode($repository));
    }

    /**
     * Make the authenticated user watch a repository.
     *
     * @link https://developer.github.com/v3/activity/watching/
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     *
     * @return array
     */
    public function watch($username, $repository)
    {
        return $this->put('/user/subscriptions/'.rawurlencode($username).'/'.rawurlencode($repository));
    }

    /**
     * Make the authenticated user unwatch a repository.
     *
     * @link https://developer.github.com/v3/activity/watching/
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     *
     * @return array
     */
    public function unwatch($username, $repository)
    {
        return $this->delete('/user/subscriptions/'.rawurlencode($username).'/'.rawurlencode($repository));
    }
}
