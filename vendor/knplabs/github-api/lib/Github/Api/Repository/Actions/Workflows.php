<?php

namespace Github\Api\Repository\Actions;

use Github\Api\AbstractApi;

/**
 * @link https://docs.github.com/en/rest/reference/actions#workflows
 */
class Workflows extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-repository-workflows
     *
     * @param string $username
     * @param string $repository
     * @param array  $parameters
     *
     * @return array
     */
    public function all(string $username, string $repository, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/workflows', $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-a-workflow
     *
     * @param string     $username
     * @param string     $repository
     * @param string|int $workflow
     *
     * @return array
     */
    public function show(string $username, string $repository, $workflow)
    {
        if (is_string($workflow)) {
            $workflow = rawurlencode($workflow);
        }

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/workflows/'.$workflow);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-workflow-usage
     *
     * @param string     $username
     * @param string     $repository
     * @param string|int $workflow
     *
     * @return array|string
     */
    public function usage(string $username, string $repository, $workflow)
    {
        if (is_string($workflow)) {
            $workflow = rawurlencode($workflow);
        }

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/workflows/'.$workflow.'/timing');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#create-a-workflow-dispatch-event
     *
     * @param string     $username
     * @param string     $repository
     * @param string|int $workflow
     * @param string     $ref
     * @param array      $inputs
     *
     * @return array|string empty
     */
    public function dispatches(string $username, string $repository, $workflow, string $ref, array $inputs = null)
    {
        if (is_string($workflow)) {
            $workflow = rawurlencode($workflow);
        }
        $parameters = array_filter(['ref' => $ref, 'inputs' => $inputs]);

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/workflows/'.$workflow.'/dispatches', $parameters);
    }
}
