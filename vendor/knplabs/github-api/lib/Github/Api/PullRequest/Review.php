<?php

namespace Github\Api\PullRequest;

use Github\Api\AbstractApi;
use Github\Api\AcceptHeaderTrait;
use Github\Exception\InvalidArgumentException;
use Github\Exception\MissingArgumentException;

/**
 * API for accessing Pull Request Reviews from your Git/Github repositories.
 *
 * @link https://developer.github.com/v3/pulls/reviews/
 *
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class Review extends AbstractApi
{
    use AcceptHeaderTrait;

    public function configure()
    {
        trigger_deprecation('KnpLabs/php-github-api', '3.2', 'The "%s" is deprecated and will be removed.', __METHOD__);

        return $this;
    }

    /**
     * Get a listing of a pull request's reviews by the username, repository and pull request number.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#list-reviews-on-a-pull-request
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param array  $params      a list of extra parameters.
     *
     * @return array array of pull request reviews for the pull request
     */
    public function all($username, $repository, $pullRequest, array $params = [])
    {
        if (!empty($params)) {
            trigger_deprecation('KnpLabs/php-github-api', '3.2', 'The "$params" parameter is deprecated, to paginate the results use the "ResultPager" instead.');
        }

        $parameters = array_merge([
            'page' => 1,
            'per_page' => 30,
        ], $params);

        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews', $parameters);
    }

    /**
     * Get a single pull request review by the username, repository, pull request number and the review id.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#get-a-single-review
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param int    $id          the review id
     *
     * @return array the pull request review
     */
    public function show($username, $repository, $pullRequest, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews/'.$id);
    }

    /**
     * Delete a single pull request review by the username, repository, pull request number and the review id.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#delete-a-pending-review
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param int    $id          the review id
     *
     * @return array|string
     */
    public function remove($username, $repository, $pullRequest, $id)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews/'.$id);
    }

    /**
     * Get comments for a single pull request review.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#get-comments-for-a-single-review
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param int    $id          the review id
     *
     * @return array|string
     */
    public function comments($username, $repository, $pullRequest, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews/'.$id.'/comments');
    }

    /**
     * Create a pull request review by the username, repository and pull request number.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#create-a-pull-request-review
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param array  $params      a list of extra parameters.
     *
     * @throws MissingArgumentException
     *
     * @return array the pull request review
     */
    public function create($username, $repository, $pullRequest, array $params = [])
    {
        if (array_key_exists('event', $params) && !in_array($params['event'], ['APPROVE', 'REQUEST_CHANGES', 'COMMENT'], true)) {
            throw new InvalidArgumentException(sprintf('"event" must be one of ["APPROVE", "REQUEST_CHANGES", "COMMENT"] ("%s" given).', $params['event']));
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews', $params);
    }

    /**
     * Submit a pull request review by the username, repository, pull request number and the review id.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#submit-a-pull-request-review
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param int    $id          the review id
     * @param array  $params      a list of extra parameters.
     *
     * @throws MissingArgumentException
     *
     * @return array the pull request review
     */
    public function submit($username, $repository, $pullRequest, $id, array $params = [])
    {
        if (!isset($params['event'])) {
            throw new MissingArgumentException('event');
        }

        if (!in_array($params['event'], ['APPROVE', 'REQUEST_CHANGES', 'COMMENT'], true)) {
            throw new InvalidArgumentException(sprintf('"event" must be one of ["APPROVE", "REQUEST_CHANGES", "COMMENT"] ("%s" given).', $params['event']));
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews/'.$id.'/events', $params);
    }

    /**
     * Dismiss a pull request review by the username, repository, pull request number and the review id.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#dismiss-a-pull-request-review
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param int    $id          the review id
     * @param string $message     a mandatory dismissal message
     *
     * @return array|string
     */
    public function dismiss($username, $repository, $pullRequest, $id, $message)
    {
        if (!is_string($message)) {
            throw new InvalidArgumentException(sprintf('"message" must be a valid string ("%s" given).', gettype($message)));
        }

        if (empty($message)) {
            throw new InvalidArgumentException('"message" is mandatory and cannot be empty');
        }

        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews/'.$id.'/dismissals', [
            'message' => $message,
        ]);
    }

    /**
     * Update a pull request review by the username, repository, pull request number and the review id.
     *
     * @link https://developer.github.com/v3/pulls/reviews/#update-a-pull-request-review
     *
     * @param string $username    the username
     * @param string $repository  the repository
     * @param int    $pullRequest the pull request number
     * @param int    $id          the review id
     * @param string $body        a mandatory dismissal message
     *
     * @return array|string
     */
    public function update($username, $repository, $pullRequest, $id, $body)
    {
        if (!is_string($body)) {
            throw new InvalidArgumentException(sprintf('"body" must be a valid string ("%s" given).', gettype($body)));
        }

        if (empty($body)) {
            throw new InvalidArgumentException('"body" is mandatory and cannot be empty');
        }

        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$pullRequest.'/reviews/'.$id, [
            'body' => $body,
        ]);
    }
}
