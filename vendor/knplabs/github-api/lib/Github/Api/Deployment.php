<?php

namespace Github\Api;

use Github\Api\Deployment\Environments;
use Github\Api\Deployment\Policies;
use Github\Exception\MissingArgumentException;

/**
 * Listing, creating and updating deployments.
 *
 * @link https://developer.github.com/v3/repos/deployments/
 */
class Deployment extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * List deployments for a particular repository.
     *
     * @link https://developer.github.com/v3/repos/deployments/#list-deployments
     *
     * @param string $username   the username of the user who owns the repository
     * @param string $repository the name of the repository
     * @param array  $params     query parameters to filter deployments by (see link)
     *
     * @return array the deployments requested
     */
    public function all($username, $repository, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/deployments', $params);
    }

    /**
     * Get a deployment in selected repository.
     *
     * @param string $username   the user who owns the repo
     * @param string $repository the name of the repo
     * @param int    $id         the id of the deployment
     *
     * @return array
     */
    public function show($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/deployments/'.$id);
    }

    /**
     * Create a new deployment for the given username and repo.
     *
     * @link https://developer.github.com/v3/repos/deployments/#create-a-deployment
     *
     * Important: Once a deployment is created, it cannot be updated. Changes are indicated by creating new statuses.
     * @see updateStatus
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param array  $params     the new deployment data
     *
     * @throws MissingArgumentException
     *
     * @return array information about the deployment
     */
    public function create($username, $repository, array $params)
    {
        if (!isset($params['ref'])) {
            throw new MissingArgumentException(['ref']);
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/deployments', $params);
    }

    /**
     * Delete a deployment for the given username and repo.
     *
     * @link https://docs.github.com/en/rest/reference/repos#delete-a-deployment
     *
     * Important: Deployments can only be deleted when in inactive state
     * @see updateStatus
     *
     * @return mixed null on success, array on error with 'message'
     */
    public function remove(string $username, string $repository, int $id)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/deployments/'.$id);
    }

    /**
     * Updates a deployment by creating a new status update.
     *
     * @link https://developer.github.com/v3/repos/deployments/#create-a-deployment-status
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param int    $id         the deployment number
     * @param array  $params     The information about the deployment update.
     *                           Must include a "state" field of pending, success, error, or failure.
     *                           May also be given a target_url and description, see link for more details.
     *
     * @throws MissingArgumentException
     *
     * @return array information about the deployment
     */
    public function updateStatus($username, $repository, $id, array $params)
    {
        if (!isset($params['state'])) {
            throw new MissingArgumentException(['state']);
        }

        // adjust media-type per github docs
        // https://docs.github.com/en/rest/reference/repos#create-a-deployment-status
        if ($params['state'] === 'inactive') {
            $this->acceptHeaderValue = 'application/vnd.github.ant-man-preview+json';
        }
        if ($params['state'] === 'in_progress' || $params['state'] === 'queued') {
            $this->acceptHeaderValue = 'application/vnd.github.flash-preview+json';
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/deployments/'.$id.'/statuses', $params);
    }

    /**
     * Gets all of the status updates tied to a given deployment.
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param int    $id         the deployment identifier
     *
     * @return array the deployment statuses
     */
    public function getStatuses($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/deployments/'.$id.'/statuses');
    }

    /**
     * @return Environments
     */
    public function environments()
    {
        return new Environments($this->getClient());
    }

    /**
     * @return Policies
     */
    public function policies()
    {
        return new Policies($this->getClient());
    }
}
