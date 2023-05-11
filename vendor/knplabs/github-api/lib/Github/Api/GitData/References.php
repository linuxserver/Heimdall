<?php

namespace Github\Api\GitData;

use Github\Api\AbstractApi;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/git/references/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class References extends AbstractApi
{
    /**
     * Get all references of a repository.
     *
     * @param string $username
     * @param string $repository
     *
     * @return array
     */
    public function all($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/refs');
    }

    /**
     * Get all matching references for the supplied reference name.
     *
     * @param string $username
     * @param string $repository
     * @param string $reference
     *
     * @return array
     */
    public function matching(string $username, string $repository, string $reference): array
    {
        $reference = $this->encodeReference($reference);

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/matching-refs/'.$reference);
    }

    /**
     * Get all branches of a repository.
     *
     * @param string $username
     * @param string $repository
     *
     * @return array
     */
    public function branches($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/refs/heads');
    }

    /**
     * Get all tags of a repository.
     *
     * @param string $username
     * @param string $repository
     *
     * @return array
     */
    public function tags($username, $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/refs/tags');
    }

    /**
     * Show the reference of a repository.
     *
     * @param string $username
     * @param string $repository
     * @param string $reference
     *
     * @return array
     */
    public function show($username, $repository, $reference)
    {
        $reference = $this->encodeReference($reference);

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/refs/'.$reference);
    }

    /**
     * Create a reference for a repository.
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
        if (!isset($params['ref'], $params['sha'])) {
            throw new MissingArgumentException(['ref', 'sha']);
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/refs', $params);
    }

    /**
     * Update a reference for a repository.
     *
     * @param string $username
     * @param string $repository
     * @param string $reference
     * @param array  $params
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    public function update($username, $repository, $reference, array $params)
    {
        if (!isset($params['sha'])) {
            throw new MissingArgumentException('sha');
        }

        $reference = $this->encodeReference($reference);

        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/refs/'.$reference, $params);
    }

    /**
     * Delete a reference of a repository.
     *
     * @param string $username
     * @param string $repository
     * @param string $reference
     *
     * @return array
     */
    public function remove($username, $repository, $reference)
    {
        $reference = $this->encodeReference($reference);

        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/git/refs/'.$reference);
    }

    /**
     * Encode the raw reference.
     *
     * @param string $rawReference
     *
     * @return string
     */
    private function encodeReference($rawReference)
    {
        return implode('/', array_map('rawurlencode', explode('/', $rawReference)));
    }
}
