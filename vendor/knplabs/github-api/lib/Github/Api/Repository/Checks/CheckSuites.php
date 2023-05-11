<?php

namespace Github\Api\Repository\Checks;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

/**
 * @link https://docs.github.com/en/rest/reference/checks
 */
class CheckSuites extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * @link https://docs.github.com/en/rest/reference/checks#create-a-check-suite
     *
     * @return array
     */
    public function create(string $username, string $repository, array $params = [])
    {
        $this->acceptHeaderValue = 'application/vnd.github.antiope-preview+json';

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/check-suites', $params);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/checks#update-repository-preferences-for-check-suites
     *
     * @return array
     */
    public function updatePreferences(string $username, string $repository, array $params = [])
    {
        $this->acceptHeaderValue = 'application/vnd.github.antiope-preview+json';

        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/check-suites/preferences', $params);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/checks#get-a-check-suite
     *
     * @return array
     */
    public function getCheckSuite(string $username, string $repository, int $checkSuiteId)
    {
        $this->acceptHeaderValue = 'application/vnd.github.antiope-preview+json';

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/check-suites/'.$checkSuiteId);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/checks#rerequest-a-check-suite
     *
     * @return array
     */
    public function rerequest(string $username, string $repository, int $checkSuiteId)
    {
        $this->acceptHeaderValue = 'application/vnd.github.antiope-preview+json';

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/check-suites/'.$checkSuiteId.'/rerequest');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/checks#list-check-suites-for-a-git-reference
     *
     * @return array
     */
    public function allForReference(string $username, string $repository, string $ref, array $params = [])
    {
        $this->acceptHeaderValue = 'application/vnd.github.antiope-preview+json';

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/commits/'.rawurlencode($ref).'/check-suites', $params);
    }
}
