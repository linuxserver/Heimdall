<?php

namespace Github\Api\Repository\Actions;

use Github\Api\AbstractApi;

/**
 * @link https://docs.github.com/en/rest/reference/actions#workflow-jobs
 */
class WorkflowJobs extends AbstractApi
{
    /**
     * @link https://docs.github.com/en/rest/reference/actions#list-jobs-for-a-workflow-run
     *
     * @param string $username
     * @param string $repository
     * @param int    $runId
     * @param array  $parameters
     *
     * @return array
     */
    public function all(string $username, string $repository, int $runId, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/runs/'.$runId.'/jobs', $parameters);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#get-a-job-for-a-workflow-run
     *
     * @param string $username
     * @param string $repository
     * @param int    $jobId
     *
     * @return array
     */
    public function show(string $username, string $repository, int $jobId)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/jobs/'.$jobId);
    }

    /**
     * @link https://docs.github.com/en/rest/reference/actions#download-job-logs-for-a-workflow-run
     *
     * @param string $username
     * @param string $repository
     * @param int    $jobId
     *
     * @return array
     */
    public function downloadLogs(string $username, string $repository, int $jobId)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/actions/jobs/'.$jobId.'/logs');
    }
}
