<?php

namespace Github\Api\GitData;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/git/blobs/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Blobs extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the Accept header depending on the blob type.
     *
     * @param string|null $bodyType
     *
     * @return $this
     */
    public function configure($bodyType = null)
    {
        if ('raw' === $bodyType) {
            $this->acceptHeaderValue = sprintf('application/vnd.github.%s.raw', $this->getApiVersion());
        }

        return $this;
    }

    /**
     * Show a blob of a sha for a repository.
     *
     * @param string $username
     * @param string $repository
     * @param string $sha
     *
     * @return array
     */
    public function show($username, $repository, $sha)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/blobs/'.rawurlencode($sha));
    }

    /**
     * Create a blob of a sha for a repository.
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
        if (!isset($params['content'])) {
            throw new MissingArgumentException('content');
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/blobs', $params);
    }
}
