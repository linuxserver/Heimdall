<?php

namespace Github\Api\Organization\Actions;

use Github\Api\AbstractApi;

class SelfHostedRunners extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/actions/self-hosted-runners?apiVersion=2022-11-28#list-self-hosted-runners-for-an-organization
     *
     * @param string $organization
     * @param array  $parameters
     *
     * @return array|string
     */
    public function all(string $organization, array $parameters = [])
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/actions/runners', $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/actions/self-hosted-runners?apiVersion=2022-11-28#get-a-self-hosted-runner-for-an-organization
     *
     * @param string $organization
     * @param int    $runnerId
     *
     * @return array|string
     */
    public function show(string $organization, int $runnerId)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/actions/runners/'.$runnerId);
    }

    /**
     * @link https://docs.github.com/en/rest/actions/self-hosted-runners?apiVersion=2022-11-28#delete-a-self-hosted-runner-from-an-organization
     *
     * @param string $organization
     * @param int    $runnerId
     *
     * @return array|string
     */
    public function remove(string $organization, int $runnerId)
    {
        return $this->delete('/orgs/'.rawurlencode($organization).'/actions/runners/'.$runnerId);
    }

    /**
     * @link https://docs.github.com/en/rest/actions/self-hosted-runners?apiVersion=2022-11-28#list-runner-applications-for-an-organization
     *
     * @param string $organization
     *
     * @return array|string
     */
    public function applications(string $organization)
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/actions/runners/downloads');
    }
}
