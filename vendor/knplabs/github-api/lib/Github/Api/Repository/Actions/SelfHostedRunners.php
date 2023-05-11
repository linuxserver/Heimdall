<?php

namespace Github\Api\Repository\Actions;

use Github\Api\AbstractApi;

/**
 * @link https://docs.github.com/en/rest/reference/actions#self-hosted-runners
 */
class SelfHostedRunners extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-self-hosted-runners-for-a-repository
     *
     * @param string $username
     * @param string $repository
     *
     * @return array|string
     */
    public function all(string $username, string $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurldecode($repository).'/actions/runners');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-a-self-hosted-runner-for-a-repository
     *
     * @param string $username
     * @param string $repository
     * @param int    $runnerId
     *
     * @return array|string
     */
    public function show(string $username, string $repository, int $runnerId)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurldecode($repository).'/actions/runners/'.$runnerId);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#delete-a-self-hosted-runner-from-a-repository
     *
     * @param string $username
     * @param string $repository
     * @param int    $runnerId
     *
     * @return array|string
     */
    public function remove(string $username, string $repository, int $runnerId)
    {
        return $this->delete('/repos/'.rawurldecode($username).'/'.rawurldecode($repository).'/actions/runners/'.$runnerId);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-runner-applications-for-a-repository
     *
     * @param string $username
     * @param string $repository
     *
     * @return array|string
     */
    public function applications(string $username, string $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runners/downloads');
    }
}
