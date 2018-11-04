<?php

namespace Github\Api\Issue;

use Github\Api\AbstractApi;
use Github\Exception\InvalidArgumentException;
use Github\Exception\MissingArgumentException;

/**
 * @link   http://developer.github.com/v3/issues/labels/
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Labels extends AbstractApi
{
    /**
     * Get all labels for a repository or the labels for a specific issue.
     *
     * @link https://developer.github.com/v3/issues/labels/#list-labels-on-an-issue
     *
     * @param string   $username
     * @param string   $repository
     * @param int|null $issue
     *
     * @return array
     */
    public function all($username, $repository, $issue = null)
    {
        if ($issue === null) {
            $path = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels';
        } else {
            $path = '/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/labels';
        }

        return $this->get($path);
    }

    /**
     * Get a single label.
     *
     * @link https://developer.github.com/v3/issues/labels/#get-a-single-label
     *
     * @param string $username
     * @param string $repository
     * @param string $label
     *
     * @return array
     */
    public function show($username, $repository, $label)
    {
        return $this->get('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels/'.rawurlencode($label));
    }

    /**
     * Create a label for a repository.
     *
     * @link https://developer.github.com/v3/issues/labels/#create-a-label
     *
     * @param string $username
     * @param string $repository
     * @param array  $params
     *
     * @throws \Github\Exception\MissingArgumentException
     *
     * @return array
     */
    public function create($username, $repository, array $params)
    {
        if (!isset($params['name'])) {
            throw new MissingArgumentException('name');
        }
        if (!isset($params['color'])) {
            $params['color'] = 'FFFFFF';
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels', $params);
    }

    /**
     * Delete a label for a repository.
     *
     * @link https://developer.github.com/v3/issues/labels/#remove-a-label-from-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param string $label
     *
     * @return array
     */
    public function deleteLabel($username, $repository, $label)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels/'.rawurlencode($label));
    }

    /**
     * Edit a label for a repository.
     *
     * @link https://developer.github.com/v3/issues/labels/#update-a-label
     *
     * @param string $username
     * @param string $repository
     * @param string $label
     * @param string $newName
     * @param string $color
     *
     * @return array
     */
    public function update($username, $repository, $label, $newName, $color)
    {
        $params = [
            'name'  => $newName,
            'color' => $color,
        ];

        return $this->patch('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/labels/'.rawurlencode($label), $params);
    }

    /**
     * Add a label to an issue.
     *
     * @link https://developer.github.com/v3/issues/labels/#remove-a-label-from-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param int    $issue
     * @param string $labels
     *
     * @return array
     *
     * @thorws \Github\Exception\InvalidArgumentException
     */
    public function add($username, $repository, $issue, $labels)
    {
        if (is_string($labels)) {
            $labels = [$labels];
        } elseif (0 === count($labels)) {
            throw new InvalidArgumentException();
        }

        return $this->post('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/labels', $labels);
    }

    /**
     * Replace labels for an issue.
     *
     * @link https://developer.github.com/v3/issues/labels/#replace-all-labels-for-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param int    $issue
     * @param array  $params
     *
     * @return array
     */
    public function replace($username, $repository, $issue, array $params)
    {
        return $this->put('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/labels', $params);
    }

    /**
     * Remove a label for an issue.
     *
     * @link https://developer.github.com/v3/issues/labels/#remove-a-label-from-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param string $issue
     * @param string $label
     *
     * @return null
     */
    public function remove($username, $repository, $issue, $label)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/labels/'.rawurlencode($label));
    }

    /**
     * Remove all labels from an issue.
     *
     * @link https://developer.github.com/v3/issues/labels/#replace-all-labels-for-an-issue
     *
     * @param string $username
     * @param string $repository
     * @param string $issue
     *
     * @return null
     */
    public function clear($username, $repository, $issue)
    {
        return $this->delete('/repos/'.rawurlencode($username).'/'.rawurlencode($repository).'/issues/'.rawurlencode($issue).'/labels');
    }
}
