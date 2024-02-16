<?php

namespace Github\Api\Deployment;

use Github\Api\AbstractApi;
use Github\Api\Environment\Secrets;
use Github\Api\Environment\Variables;

/**
 * Listing, creating and updating environments.
 *
 * @link https://docs.github.com/en/rest/deployments/environments?apiVersion=2022-11-28#
 */
class Environments extends AbstractApi
{
    /**
     * List environments for a particular repository.
     *
     * @link https://docs.github.com/en/rest/deployments/environments?apiVersion=2022-11-28##list-environments
     *
     * @param string $username   the username of the user who owns the repository
     * @param string $repository the name of the repository
     * @param array  $params     query parameters to filter environments by (see link)
     *
     * @return array the environments requested
     */
    public function all($username, $repository, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments', $params);
    }

    /**
     * Get a environment in selected repository.
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     * @param string $name       the name of the environment
     *
     * @return array
     */
    public function show($username, $repository, $name)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($name));
    }

    /**
     * Create or update a environment for the given username and repo.
     *
     * @link https://docs.github.com/en/rest/deployments/environments?apiVersion=2022-11-28#create-or-update-an-environment
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param string $name       the name of the environment
     * @param array  $params     the new environment data
     *
     * @return array information about the environment
     */
    public function createOrUpdate($username, $repository, $name, array $params = [])
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($name), $params);
    }

    /**
     * Delete a environment for the given username and repo.
     *
     * @link https://docs.github.com/en/rest/deployments/environments?apiVersion=2022-11-28#delete-an-environment
     *
     * @return mixed null on success, array on error with 'message'
     */
    public function remove(string $username, string $repository, string $name)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($name));
    }

    /**
     * @link https://docs.github.com/en/rest/actions/secrets?apiVersion=2022-11-28#about-secrets-in-github-actions
     */
    public function secrets(): Secrets
    {
        return new Secrets($this->getClient());
    }

    /**
     * @link https://docs.github.com/en/rest/actions/variables?apiVersion=2022-11-28#about-variables-in-github-actions
     */
    public function variables(): Variables
    {
        return new Variables($this->getClient());
    }
}
