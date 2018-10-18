<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/repos/statuses/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Statuses extends AbstractApi
{
    /**
     * @link http://developer.github.com/v3/repos/statuses/#list-statuses-for-a-specific-sha
     *
     * @param string $username
     * @param string $repository
     * @param string $sha
     *
     * @return array
     */
    public function show($username, $repository, $sha)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/commits/'.rawurlencode($sha).'/statuses');
    }

    /**
     * @link https://developer.github.com/v3/repos/statuses/#get-the-combined-status-for-a-specific-ref
     *
     * @param string $username
     * @param string $repository
     * @param string $sha
     *
     * @return array
     */
    public function combined($username, $repository, $sha)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/commits/'.rawurlencode($sha).'/status');
    }

    /**
     * @link http://developer.github.com/v3/repos/statuses/#create-a-status
     *
     * @param string $username
     * @param string $repository
     * @param string $sha
     * @param array  $params
     *
     * @throws MissingArgumentException
     *
     * @return array
     */
    public function create($username, $repository, $sha, array $params = [])
    {
        if (!isset($params['state'])) {
            throw new MissingArgumentException('state');
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/statuses/'.rawurlencode($sha), $params);
    }
}
