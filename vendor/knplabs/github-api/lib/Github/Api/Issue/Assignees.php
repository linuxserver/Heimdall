<?php

namespace Github\Api\Issue;

use Github\Api\AbstractApi;
use Github\Exception\InvalidArgumentException;
use Github\Exception\MissingArgumentException;

class Assignees extends AbstractApi
{
    /**
     * List all the available assignees to which issues may be assigned.
     *
     * @param string $username
     * @param string $repository
     * @param array  $parameters
     *
     * @return array
     */
    public function listAvailable($username, $repository, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/assignees', $parameters);
    }

    /**
     * Check to see if a particular user is an assignee for a repository.
     *
     * @link https://developer.github.com/v3/issues/assignees/#check-assignee
     *
     * @param string $username
     * @param string $repository
     * @param string $assignee
     *
     * @return array
     */
    public function check($username, $repository, $assignee)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/assignees/'.rawurlencode($assignee));
    }

    /**
     * Add assignees to an Issue.
     *
     * @link https://developer.github.com/v3/issues/assignees/#add-assignees-to-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param string $issue
     * @param array  $parameters
     *
     * @throws InvalidArgumentException
     * @throws MissingArgumentException
     *
     * @return string
     */
    public function add($username, $repository, $issue, array $parameters)
    {
        if (!isset($parameters['assignees'])) {
            throw new MissingArgumentException('assignees');
        }

        if (!is_array($parameters['assignees'])) {
            throw new InvalidArgumentException('The assignees parameter should be an array of assignees');
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/assignees', $parameters);
    }

    /**
     * Remove assignees from an Issue.
     *
     * @link https://developer.github.com/v3/issues/assignees/#remove-assignees-from-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param string $issue
     * @param array  $parameters
     *
     * @throws MissingArgumentException
     *
     * @return string
     */
    public function remove($username, $repository, $issue, array $parameters)
    {
        if (!isset($parameters['assignees'])) {
            throw new MissingArgumentException('assignees');
        }

        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/assignees', $parameters);
    }
}
