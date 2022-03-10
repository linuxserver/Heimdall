<?php

namespace Github\Api\Organization\Actions;

use Github\Api\AbstractApi;

/**
 * @link https://docs.github.com/en/rest/reference/actions#secrets
 */
class Secrets extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-organization-secrets
     *
     * @param string $organization
     *
     * @return array|string
     */
    public function all(string $organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/actions/secrets');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-an-organization-secret
     *
     * @param string $organization
     * @param string $secretName
     *
     * @return array|string
     */
    public function show(string $organization, string $secretName)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName));
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#create-or-update-an-organization-secret
     *
     * @param string $organization
     * @param string $secretName
     * @param array  $parameters
     *
     * @return array|string
     */
    public function create(string $organization, string $secretName, array $parameters = [])
    {
        return $this->put('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName), $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#create-or-update-an-organization-secret
     *
     * @param string $organization
     * @param string $secretName
     * @param array  $parameters
     *
     * @return array|string
     */
    public function update(string $organization, string $secretName, array $parameters = [])
    {
        return $this->put('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName), $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#delete-an-organization-secret
     *
     * @param string $organization
     * @param string $secretName
     *
     * @return array|string
     */
    public function remove(string $organization, string $secretName)
    {
        return $this->delete('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName));
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-selected-repositories-for-an-organization-secret
     *
     * @param string $organization
     * @param string $secretName
     *
     * @return array|string
     */
    public function selectedRepositories(string $organization, string $secretName)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName).'/repositories');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#set-selected-repositories-for-an-organization-secret
     *
     * @param string $organization
     * @param string $secretName
     * @param array  $parameters
     *
     * @return array|string
     */
    public function setSelectedRepositories(string $organization, string $secretName, array $parameters = [])
    {
        return $this->put('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName).'/repositories', $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#add-selected-repository-to-an-organization-secret
     *
     * @param string $organization
     * @param string $repositoryId
     * @param string $secretName
     *
     * @return array|string
     */
    public function addSecret(string $organization, string $repositoryId, string $secretName)
    {
        return $this->put('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName).'/repositories/'.$repositoryId);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#remove-selected-repository-from-an-organization-secret
     *
     * @param string $organization
     * @param string $repositoryId
     * @param string $secretName
     *
     * @return array|string
     */
    public function removeSecret(string $organization, string $repositoryId, string $secretName)
    {
        return $this->delete('/orgs/'.rawurlencode($organization).'/actions/secrets/'.rawurlencode($secretName).'/repositories/'.$repositoryId);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-an-organization-public-key
     *
     * @param string $organization
     *
     * @return array|string
     */
    public function publicKey(string $organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/actions/secrets/public-key');
    }
}
