<?php

namespace Github\Api\Repository;

class SecretScanning extends \Github\Api\AbstractApi
{
    /**
     * @link https://docs.github.com/en/enterprise-server@3.5/rest/secret-scanning#list-secret-scanning-alerts-for-a-repository
     *
     * @param string $username
     * @param string $repository
     * @param array  $params
     *
     * @return array|string
     */
    public function alerts(string $username, string $repository, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/secret-scanning/alerts', $params);
    }

    /**
     * @link https://docs.github.com/en/enterprise-server@3.5/rest/secret-scanning#get-a-secret-scanning-alert
     *
     * @param string $username
     * @param string $repository
     * @param int    $alertNumber
     *
     * @return array|string
     */
    public function getAlert(string $username, string $repository, int $alertNumber)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/secret-scanning/alerts/'.$alertNumber);
    }

    /**
     * @link https://docs.github.com/en/enterprise-server@3.5/rest/secret-scanning#update-a-secret-scanning-alert
     *
     * @param string $username
     * @param string $repository
     * @param int    $alertNumber
     * @param array  $params
     *
     * @return array|string
     */
    public function updateAlert(string $username, string $repository, int $alertNumber, array $params = [])
    {
        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/secret-scanning/alerts/'.$alertNumber, $params);
    }

    /**
     * @link https://docs.github.com/en/enterprise-server@3.5/rest/secret-scanning#list-locations-for-a-secret-scanning-alert
     *
     * @param string $username
     * @param string $repository
     * @param int    $alertNumber
     * @param array  $params
     *
     * @return array|string
     */
    public function locations(string $username, string $repository, int $alertNumber, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/secret-scanning/alerts/'.$alertNumber.'/locations', $params);
    }
}
