<?php

namespace Github\Api;

use Github\Api\App\Hook;

/**
 * @link   https://developer.github.com/v3/apps/
 *
 * @author Nils Adermann <naderman@naderman.de>
 */
class Apps extends AbstractApi
{
    use AcceptHeaderTrait;

    private function configurePreviewHeader()
    {
        $this->acceptHeaderValue = 'application/vnd.github.machine-man-preview+json';
    }

    /**
     * Create an access token for an installation.
     *
     * @param int $installationId An integration installation id
     * @param int $userId         An optional user id on behalf of whom the
     *                            token will be requested
     *
     * @link https://developer.github.com/v3/apps/#create-a-new-installation-token
     *
     * @return array token and token metadata
     */
    public function createInstallationToken($installationId, $userId = null)
    {
        $parameters = [];
        if ($userId) {
            $parameters['user_id'] = $userId;
        }

        $this->configurePreviewHeader();

        return $this->post('/app/installations/'.$installationId.'/access_tokens', $parameters);
    }

    /**
     * Find all installations for the authenticated application.
     *
     * @link https://developer.github.com/v3/apps/#find-installations
     *
     * @return array
     */
    public function findInstallations()
    {
        $this->configurePreviewHeader();

        return $this->get('/app/installations');
    }

    /**
     * Get an installation of the application.
     *
     * @link https://developer.github.com/v3/apps/#get-an-installation
     *
     * @param int $installationId An integration installation id
     *
     * @return array
     */
    public function getInstallation($installationId)
    {
        $this->configurePreviewHeader();

        return $this->get('/app/installations/'.$installationId);
    }

    /**
     * Get an installation of the application for an organization.
     *
     * @link https://developer.github.com/v3/apps/#get-an-organization-installation
     *
     * @param string $org An organization
     *
     * @return array
     */
    public function getInstallationForOrganization($org)
    {
        $this->configurePreviewHeader();

        return $this->get('/orgs/'.rawurldecode($org).'/installation');
    }

    /**
     * Get an installation of the application for a repository.
     *
     * @link https://developer.github.com/v3/apps/#get-a-repository-installation
     *
     * @param string $owner the owner of a repository
     * @param string $repo  the name of the repository
     *
     * @return array
     */
    public function getInstallationForRepo($owner, $repo)
    {
        $this->configurePreviewHeader();

        return $this->get('/repos/'.rawurldecode($owner).'/'.rawurldecode($repo).'/installation');
    }

    /**
     * Get an installation of the application for a user.
     *
     * @link https://developer.github.com/v3/apps/#get-a-user-installation
     *
     * @param string $username
     *
     * @return array
     */
    public function getInstallationForUser($username)
    {
        $this->configurePreviewHeader();

        return $this->get('/users/'.rawurldecode($username).'/installation');
    }

    /**
     * Delete an installation of the application.
     *
     * @link https://developer.github.com/v3/apps/#delete-an-installation
     *
     * @param int $installationId An integration installation id
     */
    public function removeInstallation($installationId)
    {
        $this->configurePreviewHeader();

        $this->delete('/app/installations/'.$installationId);
    }

    /**
     * List repositories that are accessible to the authenticated installation.
     *
     * @link https://developer.github.com/v3/apps/installations/#list-repositories
     *
     * @param int $userId
     *
     * @return array
     */
    public function listRepositories($userId = null)
    {
        $parameters = [];
        if ($userId) {
            $parameters['user_id'] = $userId;
        }

        $this->configurePreviewHeader();

        return $this->get('/installation/repositories', $parameters);
    }

    /**
     * Add a single repository to an installation.
     *
     * @link https://developer.github.com/v3/apps/installations/#add-repository-to-installation
     *
     * @param int $installationId
     * @param int $repositoryId
     *
     * @return array
     */
    public function addRepository($installationId, $repositoryId)
    {
        $this->configurePreviewHeader();

        return $this->put('/installations/'.$installationId.'/repositories/'.$repositoryId);
    }

    /**
     * Remove a single repository from an installation.
     *
     * @link https://developer.github.com/v3/apps/installations/#remove-repository-from-installation
     *
     * @param int $installationId
     * @param int $repositoryId
     *
     * @return array
     */
    public function removeRepository($installationId, $repositoryId)
    {
        $this->configurePreviewHeader();

        return $this->delete('/installations/'.$installationId.'/repositories/'.$repositoryId);
    }

    /**
     * Get the currently authenticated app.
     *
     * @link https://docs.github.com/en/rest/reference/apps#get-the-authenticated-app
     *
     * @return array
     */
    public function getAuthenticatedApp()
    {
        return $this->get('/app');
    }

    /**
     * Manage the hook of an app.
     *
     * @link https://docs.github.com/en/rest/apps/webhooks
     *
     * @return Hook
     */
    public function hook()
    {
        return new Hook($this->getClient());
    }
}
