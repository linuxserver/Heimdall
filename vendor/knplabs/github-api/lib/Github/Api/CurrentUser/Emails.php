<?php

namespace Github\Api\CurrentUser;

use Github\Api\AbstractApi;
use Github\Exception\InvalidArgumentException;

/**
 * @link   http://developer.github.com/v3/users/emails/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Emails extends AbstractApi
{
    /**
     * List emails for the authenticated user.
     *
     * @link http://developer.github.com/v3/users/emails/
     *
     * @return array
     */
    public function all()
    {
        return $this->get('/user/emails');
    }

    /**
     * List public email addresses for a user.
     *
     * @link https://developer.github.com/v3/users/emails/#list-public-email-addresses-for-a-user
     *
     * @return array
     */
    public function allPublic()
    {
        return $this->get('/user/public_emails');
    }

    /**
     * Adds one or more email for the authenticated user.
     *
     * @link http://developer.github.com/v3/users/emails/
     *
     * @param string|array $emails
     *
     * @throws \Github\Exception\InvalidArgumentException
     *
     * @return array
     */
    public function add($emails)
    {
        if (is_string($emails)) {
            $emails = [$emails];
        } elseif (0 === count($emails)) {
            throw new InvalidArgumentException('The user emails parameter should be a single email or an array of emails');
        }

        return $this->post('/user/emails', $emails);
    }

    /**
     * Removes one or more email for the authenticated user.
     *
     * @link http://developer.github.com/v3/users/emails/
     *
     * @param string|array $emails
     *
     * @throws \Github\Exception\InvalidArgumentException
     *
     * @return array
     */
    public function remove($emails)
    {
        if (is_string($emails)) {
            $emails = [$emails];
        } elseif (0 === count($emails)) {
            throw new InvalidArgumentException('The user emails parameter should be a single email or an array of emails');
        }

        return $this->delete('/user/emails', $emails);
    }

    /**
     * Toggle primary email visibility.
     *
     * @link https://developer.github.com/v3/users/emails/#toggle-primary-email-visibility
     *
     * @return array
     */
    public function toggleVisibility()
    {
        return $this->patch('/user/email/visibility');
    }
}
