<?php

namespace Github\Api\Enterprise;

use Github\Api\AbstractApi;

class UserAdmin extends AbstractApi
{
    /**
     * Suspend a user.
     *
     * @link https://developer.github.com/v3/users/administration/#suspend-a-user
     *
     * @param string $username
     *
     * @return array
     */
    public function suspend($username)
    {
        return $this->put('/users/'.rawurldecode($username).'/suspended', ['Content-Length' => 0]);
    }

    /**
     * Unsuspend a user.
     *
     * @link https://developer.github.com/v3/users/administration/#unsuspend-a-user
     *
     * @param string $username
     *
     * @return array
     */
    public function unsuspend($username)
    {
        return $this->delete('/users/'.rawurldecode($username).'/suspended');
    }
}
