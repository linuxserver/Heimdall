<?php

namespace Github\Api;

use DateTime;

/**
 * API for accessing Notifications from your Git/Github repositories.
 *
 * Important! You have to be authenticated to perform these methods
 *
 * @link   https://developer.github.com/v3/activity/notifications/
 *
 * @author Dennis de Greef <github@link0.net>
 */
class Notification extends AbstractApi
{
    /**
     * Get a listing of notifications.
     *
     * @link https://developer.github.com/v3/activity/notifications/
     *
     * @param bool          $includingRead
     * @param bool          $participating
     * @param DateTime|null $since
     *
     * @return array array of notifications
     */
    public function all($includingRead = false, $participating = false, DateTime $since = null, DateTime $before = null)
    {
        $parameters = [
            'all' => $includingRead,
            'participating' => $participating,
        ];

        if ($since !== null) {
            $parameters['since'] = $since->format(DateTime::ISO8601);
        }

        if ($before !== null) {
            $parameters['before'] = $before->format(DateTime::ISO8601);
        }

        return $this->get('/notifications', $parameters);
    }

    /**
     * Marks all notifications as read from the current date.
     *
     * Optionally give DateTime to mark as read before that date.
     *
     * @link https://developer.github.com/v3/activity/notifications/#mark-as-read
     *
     * @param DateTime|null $since
     */
    public function markRead(DateTime $since = null)
    {
        $parameters = [];

        if ($since !== null) {
            $parameters['last_read_at'] = $since->format(DateTime::ISO8601);
        }

        $this->put('/notifications', $parameters);
    }

    /**
     * Mark a single thread as read using its ID.
     *
     * @link https://developer.github.com/v3/activity/notifications/#mark-a-thread-as-read
     *
     * @param int $id
     */
    public function markThreadRead($id)
    {
        $this->patch('/notifications/threads/'.$id);
    }

    /**
     * Gets a single thread using its ID.
     *
     * @link https://developer.github.com/v3/activity/notifications/#view-a-single-thread
     *
     * @param int $id
     */
    public function id($id)
    {
        return $this->get('/notifications/threads/'.$id);
    }
}
