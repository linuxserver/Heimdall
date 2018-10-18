<?php

namespace Github\Api\GitData;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/git/commits/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Commits extends AbstractApi
{
    /**
     * Show a commit for a repository.
     *
     * @param string $username
     * @param string $repository
     * @param string $sha
     *
     * @return array
     */
    public function show($username, $repository, $sha)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/commits/'.rawurlencode($sha));
    }

    /**
     * Create a commit for a repository.
     *
     * @param string $username
     * @param string $repository
     * @param array  $params
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    public function create($username, $repository, array $params)
    {
        if (!isset($params['message'], $params['tree'], $params['parents'])) {
            throw new MissingArgumentException(['message', 'tree', 'parents']);
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/commits', $params);
    }
}
