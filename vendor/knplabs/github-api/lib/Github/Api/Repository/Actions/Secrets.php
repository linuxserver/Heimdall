<?php

namespace Github\Api\Repository\Actions;

use Github\Api\AbstractApi;

/**
 * @link https://docs.github.com/en/rest/reference/actions#secrets
 */
class Secrets extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-repository-secrets
     *
     * @param string $username
     * @param string $repository
     *
     * @return array|string
     */
    public function all(string $username, string $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/secrets');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-a-repository-secret
     *
     * @param string $username
     * @param string $repository
     * @param string $secretName
     *
     * @return array|string
     */
    public function show(string $username, string $repository, string $secretName)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/secrets/'.rawurlencode($secretName));
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#create-or-update-a-repository-secret
     *
     * @param string $username
     * @param string $repository
     * @param string $secretName
     * @param array  $parameters
     *
     * @return array|string
     */
    public function create(string $username, string $repository, string $secretName, array $parameters = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/secrets/'.rawurlencode($secretName), $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#create-or-update-a-repository-secret
     *
     * @param string $username
     * @param string $repository
     * @param string $secretName
     * @param array  $parameters
     *
     * @return array|string
     */
    public function update(string $username, string $repository, string $secretName, array $parameters = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/secrets/'.rawurlencode($secretName), $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#delete-a-repository-secret
     *
     * @param string $username
     * @param string $repository
     * @param string $secretName
     *
     * @return array|string
     */
    public function remove(string $username, string $repository, string $secretName)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/secrets/'.rawurlencode($secretName));
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-a-repository-public-key
     *
     * @param string $username
     * @param string $repository
     *
     * @return array|string
     */
    public function publicKey(string $username, string $repository)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/secrets/public-key');
    }
}
