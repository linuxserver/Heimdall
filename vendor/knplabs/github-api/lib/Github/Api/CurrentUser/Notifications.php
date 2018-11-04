<?php

namespace Github\Api\CurrentUser;

use Github\Api\AbstractApi;

/**
 * @link   http://developer.github.com/v3/activity/notifications/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Notifications extends AbstractApi
{
    /**
     * List all notifications for the authenticated user.
     *
     * @link http://developer.github.com/v3/activity/notifications/#list-your-notifications
     *
     * @param array $params
     *
     * @return array
     */
    public function all(array $params = [])
    {
        return $this->get('/notifications', $params);
    }

    /**
     * List all notifications for the authenticated user in selected repository.
     *
     * @link http://developer.github.com/v3/activity/notifications/#list-your-notifications-in-a-repository
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     * @param array  $params
     *
     * @return array
     */
    public function allInRepository($username, $repository, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/notifications', $params);
    }

    /**
     * Mark all notifications as read.
     *
     * @link http://developer.github.com/v3/activity/notifications/#mark-as-read
     *
     * @param array $params
     *
     * @return array
     */
    public function markAsReadAll(array $params = [])
    {
        return $this->put('/notifications', $params);
    }

    /**
     * Mark all notifications for a repository as read.
     *
     * @link http://developer.github.com/v3/activity/notifications/#mark-notifications-as-read-in-a-repository
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     * @param array  $params
     *
     * @return array
     */
    public function markAsReadInRepository($username, $repository, array $params = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/notifications', $params);
    }

    /**
     * Mark a notification as read.
     *
     * @link http://developer.github.com/v3/activity/notifications/#mark-a-thread-as-read
     *
     * @param int   $id     the notification number
     * @param array $params
     *
     * @return array
     */
    public function markAsRead($id, array $params)
    {
        return $this->patch('/notifications/threads/'.rawurlencode($id), $params);
    }

    /**
     * Show a notification.
     *
     * @link http://developer.github.com/v3/activity/notifications/#view-a-single-thread
     *
     * @param int $id the notification number
     *
     * @return array
     */
    public function show($id)
    {
        return $this->get('/notifications/threads/'.rawurlencode($id));
    }

    /**
     * Show a subscription.
     *
     * @link http://developer.github.com/v3/activity/notifications/#get-a-thread-subscription
     *
     * @param int $id the notification number
     *
     * @return array
     */
    public function showSubscription($id)
    {
        return $this->get('/notifications/threads/'.rawurlencode($id).'/subscription');
    }

    /**
     * Create a subscription.
     *
     * @link http://developer.github.com/v3/activity/notifications/#set-a-thread-subscription
     *
     * @param int   $id     the notification number
     * @param array $params
     *
     * @return array
     */
    public function createSubscription($id, array $params)
    {
        return $this->put('/notifications/threads/'.rawurlencode($id).'/subscription', $params);
    }

    /**
     * Delete a subscription.
     *
     * @link http://developer.github.com/v3/activity/notifications/#delete-a-thread-subscription
     *
     * @param int $id the notification number
     *
     * @return array
     */
    public function removeSubscription($id)
    {
        return $this->delete('/notifications/threads/'.rawurlencode($id).'/subscription');
    }
}
