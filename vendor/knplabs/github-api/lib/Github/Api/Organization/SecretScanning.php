<?php

namespace Github\Api\Organization;

class SecretScanning extends \Github\Api\AbstractApi
{
    /**
     * @link https://docs.github.com/en/enterprise-server@3.5/rest/secret-scanning#list-secret-scanning-alerts-for-an-organization
     *
     * @param string $organization
     * @param array  $params
     *
     * @return array|string
     */
    public function alerts(string $organization, array $params = [])
    {
        return $this->get('/orgs/'.rawurlencode($organization).'/secret-scanning/alerts', $params);
    }
}
