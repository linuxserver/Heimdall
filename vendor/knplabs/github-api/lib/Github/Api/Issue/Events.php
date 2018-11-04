<?php

namespace Github\Api\Issue;

use Github\Api\AbstractApi;

/**
 * @link   http://developer.github.com/v3/issues/events/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Events extends AbstractApi
{
    /**
     * Get all events for an issue.
     *
     * @link https://developer.github.com/v3/issues/events/#list-events-for-an-issue
     *
     * @param string   $username
     * @param string   $repository
     * @param int|null $issue
     * @param int      $page
     *
     * @return array
     */
    public function all($username, $repository, $issue = null, $page = 1)
    {
        if (null !== $issue) {
            $path = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/events';
        } else {
            $path = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/events';
        }

        return $this->get($path, [
            'page' => $page,
        ]);
    }

    /**
     * Display an event for an issue.
     *
     * @link https://developer.github.com/v3/issues/events/#get-a-single-event
     *
     * @param $username
     * @param $repository
     * @param $event
     *
     * @return array
     */
    public function show($username, $repository, $event)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/events/'.rawurlencode($event));
    }
}
