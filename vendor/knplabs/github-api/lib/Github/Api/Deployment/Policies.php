<?php

namespace Github\Api\Deployment;

use Github\Api\AbstractApi;

/**
 * Listing, creating and updating deployments.
 *
 * @link https://docs.github.com/en/rest/deployments/branch-policies?apiVersion=2022-11-28#list-deployment-branch-policies
 */
class Policies extends AbstractApi
{
    /**
     * List deployment branch policies.
     *
     * @link https://docs.github.com/en/rest/deployments/branch-policies?apiVersion=2022-11-28#list-deployment-branch-policies
     *
     * @param string $username    the username of the user who owns the repository
     * @param string $repository  the name of the repository
     * @param string $environment the name of the environment.
     * @param array  $params      query parameters to filter deployments by (see link)
     *
     * @return array the branch policies requested
     */
    public function all(string $username, string $repository, string $environment, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($environment).'/deployment-branch-policies', $params);
    }

    /**
     * Get a deployment branch policy.
     *
     * @link https://docs.github.com/en/rest/deployments/branch-policies?apiVersion=2022-11-28#get-a-deployment-branch-policy
     *
     * @param string $username    the username of the user who owns the repository
     * @param string $repository  the name of the repository
     * @param string $environment the name of the environment.
     * @param int    $id          the unique identifier of the branch policy.
     *
     * @return array
     */
    public function show(string $username, string $repository, string $environment, int $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($environment).'/deployment-branch-policies/'.$id);
    }

    /**
     * Creates a deployment branch policy for an environment.
     *
     * @link https://docs.github.com/en/rest/deployments/branch-policies?apiVersion=2022-11-28#create-a-deployment-branch-policy
     *
     * @param string $username    the username of the user who owns the repository
     * @param string $repository  the name of the repository
     * @param string $environment the name of the environment.
     *
     * @return array information about the deployment branch policy
     */
    public function create(string $username, string $repository, string $environment, array $params)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($environment).'/deployment-branch-policies', $params);
    }

    /**
     * Updates a deployment branch policy for an environment.
     *
     * @link https://docs.github.com/en/rest/deployments/branch-policies?apiVersion=2022-11-28#update-a-deployment-branch-policy
     *
     * @param string $username    the username of the user who owns the repository
     * @param string $repository  the name of the repository
     * @param string $environment the name of the environment.
     * @param int    $id          the unique identifier of the branch policy.
     *
     * @return array information about the deployment branch policy
     */
    public function update(string $username, string $repository, string $environment, int $id, array $params)
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($environment).'/deployment-branch-policies/'.$id, $params);
    }

    /**
     * Delete a deployment branch policy.
     *
     * @link https://docs.github.com/en/rest/deployments/branch-policies?apiVersion=2022-11-28#delete-a-deployment-branch-policy
     *
     * @param string $username    the username of the user who owns the repository
     * @param string $repository  the name of the repository
     * @param string $environment the name of the environment.
     * @param int    $id          the unique identifier of the branch policy.
     *
     * @return mixed null on success, array on error with 'message'
     */
    public function remove(string $username, string $repository, string $environment, int $id)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/environments/'.rawurlencode($environment).'/deployment-branch-policies/'.$id);
    }
}
