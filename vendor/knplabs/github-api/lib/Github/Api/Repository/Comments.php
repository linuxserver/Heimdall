<?php

namespace Github\Api\Repository;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/repos/comments/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Comments extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the body type.
     *
     * @link https://developer.github.com/v3/repos/comments/#custom-media-types
     *
     * @param string|null $bodyType
     *
     * @return self
     */
    public function configure($bodyType = null)
    {
        if (!in_array($bodyType, ['raw', 'text', 'html'])) {
            $bodyType = 'full';
        }

        $this->acceptHeaderValue = sprintf('application/vnd.github.%s.%s+json', $this->client->getApiVersion(), $bodyType);

        return $this;
    }

    public function all($username, $repository, $sha = null)
    {
        if (null === $sha) {
            return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/comments');
        }

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/commits/'.rawurlencode($sha).'/comments');
    }

    public function show($username, $repository, $comment)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/comments/'.rawurlencode($comment));
    }

    public function create($username, $repository, $sha, array $params)
    {
        if (!isset($params['body'])) {
            throw new MissingArgumentException('body');
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/commits/'.rawurlencode($sha).'/comments', $params);
    }

    public function update($username, $repository, $comment, array $params)
    {
        if (!isset($params['body'])) {
            throw new MissingArgumentException('body');
        }

        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/comments/'.rawurlencode($comment), $params);
    }

    public function remove($username, $repository, $comment)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/comments/'.rawurlencode($comment));
    }
}
