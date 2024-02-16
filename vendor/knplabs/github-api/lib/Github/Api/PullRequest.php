<?php

namespace Github\Api;

use Github\Api\PullRequest\Comments;
use Github\Api\PullRequest\Review;
use Github\Api\PullRequest\ReviewRequest;
use Github\Exception\InvalidArgumentException;
use Github\Exception\MissingArgumentException;

/**
 * API for accessing Pull Requests from your Git/Github repositories.
 *
 * @see   http://developer.github.com/v3/pulls/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class PullRequest extends AbstractApi
{
    use AcceptHeaderTrait;

    /**
     * Configure the body type.
     *
     * @link https://developer.github.com/v3/pulls/#custom-media-types
     *
     * @param string|null $bodyType
     * @param string|null $apiVersion
     *
     * @return $this
     */
    public function configure($bodyType = null, $apiVersion = null)
    {
        if (null === $apiVersion) {
            $apiVersion = $this->getApiVersion();
        }

        if (!in_array($bodyType, ['text', 'html', 'full', 'diff', 'patch'])) {
            $bodyType = 'raw';
        }

        if (!in_array($bodyType, ['diff', 'patch'])) {
            $bodyType .= '+json';
        }

        $this->acceptHeaderValue = sprintf('application/vnd.github.%s.%s', $apiVersion, $bodyType);

        return $this;
    }

    /**
     * Get a listing of a project's pull requests by the username, repository and (optionally) state.
     *
     * @link http://developer.github.com/v3/pulls/
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param array  $parameters a list of extra parameters.
     *
     * @return array array of pull requests for the project
     */
    public function all($username, $repository, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls', $parameters);
    }

    /**
     * Show all details of a pull request, including the discussions.
     *
     * @link http://developer.github.com/v3/pulls/
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param int    $id         the ID of the pull request for which details are retrieved
     *
     * @return array|string pull request details
     */
    public function show($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.$id);
    }

    public function commits($username, $repository, $id, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.rawurlencode($id).'/commits', $parameters);
    }

    public function files($username, $repository, $id, array $parameters = [])
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.rawurlencode($id).'/files', $parameters);
    }

    /**
     * All statuses which are the statuses of its head branch.
     *
     * @see http://developer.github.com/v3/pulls/
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param int    $id         the ID of the pull request for which statuses are retrieved
     *
     * @return array array of statuses for the project
     */
    public function status($username, $repository, $id)
    {
        $link = $this->show($username, $repository, $id)['_links']['statuses']['href'];

        return $this->get($link);
    }

    /**
     * @return Comments
     */
    public function comments()
    {
        return new Comments($this->getClient());
    }

    /**
     * @return Review
     */
    public function reviews()
    {
        return new Review($this->getClient());
    }

    /**
     * @return ReviewRequest
     */
    public function reviewRequests()
    {
        return new ReviewRequest($this->getClient());
    }

    /**
     * Create a pull request.
     *
     * @link   http://developer.github.com/v3/pulls/
     *
     * @param string $username   the username
     * @param string $repository the repository
     * @param array  $params     A String of the branch or commit SHA that you want your changes to be pulled to.
     *                           A String of the branch or commit SHA of your changes. Typically this will be a branch.
     *                           If the branch is in a fork of the original repository, specify the username first:
     *                           "my-user:some-branch". The String title of the Pull Request. The String body of
     *                           the Pull Request. The issue number. Used when title and body is not set.
     *
     * @throws MissingArgumentException
     *
     * @return array
     */
    public function create($username, $repository, array $params)
    {
        // Two ways to create PR, using issue or title
        if (!isset($params['issue']) && !isset($params['title'])) {
            throw new MissingArgumentException(['issue', 'title']);
        }

        if (!isset($params['base'], $params['head'])) {
            throw new MissingArgumentException(['base', 'head']);
        }

        // If `issue` is not sent, then `body` must be sent
        if (!isset($params['issue']) && !isset($params['body'])) {
            throw new MissingArgumentException(['issue', 'body']);
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls', $params);
    }

    public function update($username, $repository, $id, array $params)
    {
        if (isset($params['state']) && !in_array($params['state'], ['open', 'closed'])) {
            $params['state'] = 'open';
        }

        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.rawurlencode($id), $params);
    }

    public function merged($username, $repository, $id)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.rawurlencode($id).'/merge');
    }

    public function merge($username, $repository, $id, $message, $sha, $mergeMethod = 'merge', $title = null)
    {
        if (is_bool($mergeMethod)) {
            $mergeMethod = $mergeMethod ? 'squash' : 'merge';
        }

        if (!in_array($mergeMethod, ['merge', 'squash', 'rebase'], true)) {
            throw new InvalidArgumentException(sprintf('"$mergeMethod" must be one of ["merge", "squash", "rebase"] ("%s" given).', $mergeMethod));
        }

        $params = [
            'commit_message' => $message,
            'sha' => $sha,
            'merge_method' => $mergeMethod,
        ];

        if (is_string($title)) {
            $params['commit_title'] = $title;
        }

        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/pulls/'.rawurlencode($id).'/merge', $params);
    }
}
