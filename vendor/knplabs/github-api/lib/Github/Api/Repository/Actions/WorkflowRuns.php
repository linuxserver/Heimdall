<?php

namespace Github\Api\Repository\Actions;

use Github\Api\AbstractApi;

/**
 * @link https://docs.github.com/en/rest/reference/actions#workflow-runs
 */
class WorkflowRuns extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-workflow-runs-for-a-repository
     *
     * @param string $username
     * @param string $repository
     * @param array  $parameters
     *
     * @return array
     */
    public function all(string $username, string $repository, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs', $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-workflow-runs
     *
     * @param string $username
     * @param string $repository
     * @param string $workflow
     * @param array  $parameters
     *
     * @return array
     */
    public function listRuns(string $username, string $repository, string $workflow, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/workflows/'.rawurlencode($workflow).'/runs', $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-a-workflow-run
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     * @param array  $parameters
     *
     * @return array
     */
    public function show(string $username, string $repository, int $runId, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId, $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#delete-a-workflow-run
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     *
     * @return array|string
     */
    public function remove(string $username, string $repository, int $runId)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#re-run-a-workflow
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     *
     * @return array
     */
    public function rerun(string $username, string $repository, int $runId)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId.'/rerun');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#cancel-a-workflow-run
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     *
     * @return array
     */
    public function cancel(string $username, string $repository, int $runId)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId.'/cancel');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-workflow-run-usage
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     *
     * @return array
     */
    public function usage(string $username, string $repository, int $runId)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId.'/timing');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#download-workflow-run-logs
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     *
     * @return array|string
     */
    public function downloadLogs(string $username, string $repository, int $runId)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId.'/logs');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#delete-workflow-run-logs
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     *
     * @return array|string
     */
    public function deleteLogs(string $username, string $repository, int $runId)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId.'/logs');
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#approve-a-workflow-run-for-a-fork-pull-request
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     *
     * @return array|string
     *
     * @experimental This endpoint is currently in beta.
     */
    public function approve(string $username, string $repository, int $runId)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId.'/approve');
    }
}
