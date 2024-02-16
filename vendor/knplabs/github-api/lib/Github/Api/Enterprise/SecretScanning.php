<?php

namespace Github\Api\Enterprise;

use Github\Api\AbstractApi;

class SecretScanning extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/enterprise-server@3.5/rest/secret-scanning#list-secret-scanning-alerts-for-an-enterprise
     *
     * @param string $enterprise
     * @param array  $params
     *
     * @return array|string
     */
    public function alerts(string $enterprise, array $params = [])
    {
        return $this->get('/enterprises/'.rawurlencode($enterprise).'/secret-scanning/alerts', $params);
    }
}
