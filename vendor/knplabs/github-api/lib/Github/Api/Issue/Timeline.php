<?php

namespace Github\Api\Issue;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

class Timeline extends AbstractApi
{
    use AcceptHeaderTrait;

    public function configure()
    {
        $this->acceptHeaderValue = 'application/vnd.github.mockingbird-preview';

        return $this;
    }

    /**
     * Get all events for a specific issue.
     *
     * @link https://developer.github.com/v3/issues/timeline/#list-events-for-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param int    $issue
     *
     * @return array
     */
    public function all($username, $repository, $issue)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/timeline');
    }
}
