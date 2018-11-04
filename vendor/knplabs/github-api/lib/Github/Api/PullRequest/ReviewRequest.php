<?php

namespace Github\Api\PullRequest;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;

/**
 * @link https://developer.github.com/v3/pulls/review_requests/
 */
class ReviewRequest extends AbstractApi
{
    use AcceptHeaderTrait;

    public function configure()
    {
        return $this;
    }

    /**
     * @link https://developer.github.com/v3/pulls/review_requests/#list-review-requests
     *
     * @param string $username
     * @param string $repository
     * @param int    $pullRequest
     * @param array  $params
     *
     * @return array
     */
    public function all($username, $repository, $pullRequest, array $params = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/requested_reviewers', $params);
    }

    /**
     * @link https://developer.github.com/v3/pulls/review_requests/#create-a-review-request
     *
     * @param string $username
     * @param string $repository
     * @param int    $pullRequest
     * @param array  $reviewers
     *
     * @return string
     */
    public function create($username, $repository, $pullRequest, array $reviewers)
    {
        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/requested_reviewers', ['reviewers' => $reviewers]);
    }

    /**
     * @link https://developer.github.com/v3/pulls/review_requests/#delete-a-review-request
     *
     * @param string $username
     * @param string $repository
     * @param int    $pullRequest
     * @param array  $reviewers
     *
     * @return string
     */
    public function remove($username, $repository, $pullRequest, array $reviewers)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/requested_reviewers', ['reviewers' => $reviewers]);
    }
}
